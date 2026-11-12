# Brainstorm — Ganti Kata Sandi (Siswa PPDB, Petugas, dan Admin)

Tanggal: 2026-11-12
Fase: P0 (blueprint) → siap masuk P2/P3

## 1. Framing

**Masalah.** Sekarang kata sandi hanya bisa diganti lewat tautan email
(`lupa-sandi`). SMTP masih `log` dan banyak siswa memakai email yang jarang
dibuka. Akibatnya: siswa yang lupa sandi praktis terkunci, dan panitia tidak
punya jalan memperbaiki akun tanpa menyentuh database. Petugas/admin yang
ingin mengganti sandi sendiri juga bergantung pada email.

**User utama.**
1. Calon siswa PPDB (HP, 390px, sering tidak paham istilah teknis).
2. Petugas/operator (desktop + HP di loket, verifikasi pendaftar).
3. Admin sekolah (desktop, mengelola akun).

**Sukses (terverifikasi).**
- Siswa bisa mengganti sandi sendiri dengan memasukkan sandi lama, lalu login
  ulang dengan sandi baru — tanpa email.
- Petugas/admin bisa mengganti sandi sendiri dari panel.
- Admin bisa menetapkan sandi baru untuk akun siswa dan akun petugas lain.
- Tidak ada jalur baru untuk mengambil alih akun tanpa kredensial/izin.

**Bukan scope.**
- Tautan undangan email, verifikasi email, 2FA.
- Paksa ganti sandi saat login pertama (butuh kolom baru + alur login).
- Manajemen sesi per perangkat ("keluar dari semua perangkat").
- Riwayat sandi (mencegah pemakaian ulang sandi lama).
- Kebijakan kedaluwarsa sandi.

## 2. Tiga pendekatan

**A. Halaman "Akun Saya" di tiap panel + form reset per baris di daftar akun
( dipilih).**
+ Satu pola untuk semua peran; tanpa modal, tanpa JS baru yang rapuh.
+ Reset per baris memakai `<details>` native → keyboard & HP aman.
− Dua tempat berbeda (self-service vs reset orang lain) harus konsisten.

**B. Satu halaman "Keamanan" terpusat di admin yang memuat semua akun.**
+ Satu layar untuk admin.
− Siswa tetap butuh halaman sendiri, jadi tetap dua tempat. Daftar akun
  campur dua tabel (users + ppdb_accounts) → query gabungan rapuh.

**C. Hanya lewat email (perbaiki SMTP), tanpa form baru.**
+ Nol permukaan serangan baru.
− Tidak menyelesaikan masalah: email siswa tidak terbaca, dan admin tetap
  tidak bisa menolong di loket.

**Pilihan: A.** Alasan: menutup masalah nyata (terkunci tanpa email), tetap
memakai pola form yang sudah ada (`field`, `adm-panel`, `form-actions`), dan
setiap jalur tetap butuh kredensial atau izin admin.

## 3. Mekanisme

### Alur data

Self-service siswa:
`/ppdb/portal/akun` (GET) → form (sandi lama, sandi baru, konfirmasi) →
`POST /ppdb/portal/akun/sandi` → validasi → `Hash::check` sandi lama →
`forceFill(password)` + `remember_token` baru + `session()->regenerate()` →
redirect `?status`.

Self-service petugas/admin: sama, guard `web`, `/admin/akun`.

Admin reset sandi siswa:
`/admin/ppdb/online/{pendaftaran}` → panel "Keamanan Akun Siswa" →
`POST /admin/ppdb/online/{pendaftaran}/akun/sandi` → izin `role:admin` →
validasi → `forceFill(password)` + `remember_token` baru → `Log::info`
(aktor, target, waktu).

Admin reset sandi petugas:
`/admin/petugas` → baris akun → `<details>` "Ganti sandi" →
`POST /admin/petugas/{user}/sandi` → izin `role:admin` → sama seperti di atas.

### State
`idle → submit → (valid|invalid) → sukses (redirect + flash) | galat (kembali
dengan old input + pesan per field)`. Tidak ada state tersimpan di klien.
Gagal di tengah = tidak ada perubahan (satu `save()`), aman diulang.

### Kontrak rute

| Method | Path | Nama | Middleware |
|---|---|---|---|
| GET | `/admin/akun` | `admin.account` | `auth:web` |
| POST | `/admin/akun/sandi` | `admin.account.password` | `auth:web`, `throttle:6,1` |
| POST | `/admin/petugas/{user}/sandi` | `admin.petugas.password` | `auth:web`, `role:admin`, `throttle:10,1` |
| POST | `/admin/ppdb/online/{pendaftaran}/akun/sandi` | `admin.ppdb.online.password` | `auth:web`, `role:admin`, `throttle:10,1` |
| GET | `/ppdb/portal/akun` | `ppdb.portal.akun` | `auth:ppdb` |
| POST | `/ppdb/portal/akun/sandi` | `ppdb.portal.akun.password` | `auth:ppdb`, `throttle:6,1` |

### Model data
Tidak ada migrasi. `users.password` dan `ppdb_accounts.password` sudah
`hashed` cast. `remember_token` dirotasi saat sandi berubah supaya cookie
"ingat saya" lama mati.

### Keamanan mekanisme
- Self-service **wajib** sandi lama (`current_password:web|ppdb`) → sesi
  yang dibajak tidak bisa mengganti sandi tanpa tahu sandi lama. Gagal
  `current_password` → 422/redirect dengan pesan, bukan 500.
- Reset oleh admin **hanya** `role:admin`; operator tidak boleh.
- Route model binding (`{user}`, `{pendaftaran}`) tidak bisa dipakai lintas
  peran karena middleware role dicek sebelum controller.
- Tidak ada email/ID akun di URL untuk jalur siswa (pakai sesi guard).
- Rate limit: 6/menit untuk self (menebak sandi lama), 10/menit untuk admin.
- `Log::info` untuk setiap reset oleh admin (aktor + target + IP) → jejak
  kalau ada penyalahgunaan.
- Sandi tidak pernah dicatat di log, tidak dikirim ke view.
- Panjang maksimum 72 (batas bcrypt) supaya tidak terpotong diam-diam.

### Kegagalan
| Langkah gagal | Yang dilihat user | Data |
|---|---|---|
| Validasi | pesan per field, isian lama dipertahankan | tidak berubah |
| Sandi lama salah | "Kata sandi saat ini tidak cocok." | tidak berubah |
| DB error saat save | halaman error 500 standar (tidak membocorkan detail) | tidak berubah (satu save) |
| Pendaftaran tanpa akun PPDB (data lama) | panel reset tidak tampil | tidak berubah |

## 4. Rencana anti-slop (UI)

- **Identitas**: panel administrasi sekolah yang tenang dan operasional —
  putih, garis tipis, aksen merah PGRI yang sudah ada. Tidak ada warna baru,
  tidak ada gradient, tidak ada kartu dekoratif.
- **Sistem visual**: hanya token yang ada (`--surface`, `--line`, `--ink*`,
  `--red`, `--r-md`, `--shadow-sm`), `.field`, `.btn`, `.adm-panel`,
  `.adm-grid-2`, `.adm-alert`, `.adm-badge`.
- **Pola interaksi**:
  - Halaman "Akun" = dua kolom: form sandi + ringkasan identitas akun
    (nama, email, NIK/no. pendaftaran, peran). Di HP menumpuk.
  - Tombol tampil/sembunyikan sandi (`i-eye`/`i-eye-slash`, `aria-pressed`)
    supaya tidak salah ketik di HP.
  - Reset oleh admin = `<details>` per baris: tertutup secara default,
    label jelas "Ganti sandi", isi 2 field + tombol.
  - Microcopy: "Masukkan kata sandi yang sekarang dipakai." /
    "Setelah disimpan, beri tahu yang bersangkutan untuk segera menggantinya."
  - Sukses: `.adm-alert` hijau-netral. Galat: `.field.has-error` + `.error`.
- **Larangan**: tanpa emoji, tanpa placeholder "Lorem", tanpa teks generik
  ("Welcome"), tanpa modal baru, tanpa JS framework.

## 5. Rencana kebenaran

**Edge case.** (1) field kosong; (2) konfirmasi tidak cocok; (3) sandi baru
sama dengan lama; (4) sandi < 8 karakter / tanpa angka / tanpa huruf;
(5) sandi > 72 karakter; (6) sandi lama salah; (7) operator memanggil rute
reset admin → 403; (8) tamu memanggil rute → redirect login; (9) pendaftaran
tanpa `ppdb_account`; (10) dua submit beruntun (double klik) — idempoten.

**Failure mode.** DB mati → 500 standar, tidak ada perubahan. Rate limit
terlampaui → 429 dengan pesan Laravel. Tidak ada dependensi luar.

**Pre-mortem.**
1. *Siswa tertukar akun*: reset memakai `{pendaftaran}` → `account` relasi,
   bukan input ID akun. Pencegahan: akses hanya dari relasi, bukan parameter
   akun terpisah.
2. *Sesi lama tetap hidup setelah sandi diganti*: rotasi `remember_token`
   + `session()->regenerate()`; sesi aktif lain tidak diputus (dibatasi
   scope, dicatat di laporan).
3. *Admin salah orang*: panel menampilkan nama + no. pendaftaran + email
   sebelum field sandi, dan tombol berbunyi "Simpan sandi untuk akun ini".

**Keamanan.** CSRF di semua form, otorisasi per aksi, validasi di server,
tanpa menambah permukaan upload/URL.

**Performa.** Nol query tambahan selain `find`/relasi yang sudah dimuat.
Tidak ada N+1 baru.

## 6. Rencana verifikasi

- `php artisan test --compact --filter=AccountPassword` → semua hijau
  (self siswa, self petugas, reset admin→siswa, reset admin→petugas,
  operator 403, sandi lama salah, konfirmasi salah, sandi sama, tamu 302).
- `php artisan test --compact` penuh → tidak ada regresi.
- `vendor/bin/pint --dirty --format agent` → bersih.
- Browser (Playwright, 390px & 1440px): halaman Akun siswa & admin,
  ubah sandi sukses (alert muncul), sandi lama salah (pesan field),
  toggle tampil sandi, reset dari panel admin, tanpa overflow horizontal,
  0 console error.
- Cek `option 1`: setelah ganti sandi, `POST /ppdb/masuk` dengan sandi baru
  berhasil dan sandi lama gagal.

## 7. Asumsi berisiko (terbuka)

1. **Admin boleh mereset sandi admin lain.** Konsisten dengan
   `destroyPetugas` yang sudah memberi `role:admin` hak menghapus akun lain;
   setiap reset dicatat di log. Kalau tidak diinginkan, cukup sembunyikan
   baris berperan `admin` di daftar.
2. **Sesi lain tidak diputus** saat sandi diganti (hanya cookie ingat-saya).
   Butuh tabel sesi/guard terpisah untuk memutus semuanya; di luar scope.
3. **Tidak ada paksa-ganti saat login** untuk sandi yang ditetapkan admin.
   Dicatat sebagai pekerjaan lanjutan bila diminta.
