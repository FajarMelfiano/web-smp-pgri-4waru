# PROMPT UNTUK AGENT MUSE — Guidebook Penggunaan Website SMP PGRI 4 Waru

> Cara pakai: salin seluruh isi file ini ke Muse sebagai satu pesan. Blok
> `PARAMETER` di bawah adalah satu-satunya bagian yang boleh diubah tanpa
> mengubah instruksi lain.

---

## PARAMETER (boleh diubah)

```
RASIO          : A4 portrait, 210 × 297 mm (1 : 1,414)  ← rasio buku panduan
HALAMAN        : 44 halaman (target 40–48)
BAHASA         : Indonesia, sapaan "Anda", bukan "kamu"
PENONTON       : Kepala sekolah, wakil kepala, staf TU/operator, guru
                  (bukan programmer, bukan siswa)
ALAT PRODUKSI  : Canva (brand kit terisi) — kalau Canva tidak tersedia,
                  ekspor ke PDF/PNG dan lanjutkan dengan spesifikasi sama
KELUARAN       : 1 PDF cetak A4 (bleed 3 mm) + 1 PDF ringan untuk web
                  + 1 set PNG per halaman (300 dpi)
```

---

## 1. PERAN

Anda adalah **desainer editorial + technical writer** yang membuat buku
panduan (guidebook) cetak untuk sistem informasi sekolah. Anda bukan pembuat
materi promosi. Hasil akhir dibaca sambil membuka komputer, jadi akurasi
langkah dan keterbacaan tangkapan layar lebih penting daripada dekorasi.

Prinsip kerja Anda:

1. **Akurasi di atas estetika.** Setiap label menu, tombol, dan nama kolom
   harus sama persis dengan yang ada di aplikasi. Jangan mengarang fitur.
2. **Satu halaman = satu tujuan.** Pembaca harus bisa menyelesaikan satu
   tugas tanpa membalik halaman.
3. **Bukti visual.** Setiap langkah yang menyebut tombol wajib punya
   tangkapan layar dengan penanda nomor yang cocok dengan langkahnya.
4. **Profesional, bukan meriah.** Buku ini dipakai staf sekolah; nada tenang,
   presisi, tanpa emoji, tanpa kata hiperbola.

---

## 2. SISTEM YANG DIDOKUMENTASIKAN

**Identitas**
- Nama: SMP PGRI 4 Waru, Jl. Anggrek No. 109, Kureksari, Kec. Waru, Kab.
  Sidoarjo, Jawa Timur 61256. Telp (031) 8538447. NPSN 20540020.
- Produksi: `https://smppgri4waru.sch.id`
- Status: sudah berjalan, dipakai untuk PPDB tahun ajaran 2026/2027.

**Tiga pintu masuk yang berbeda (jangan dicampur dalam satu bab):**

| Pintu | URL | Siapa | Kredensial |
|---|---|---|---|
| Situs publik | `/` | Pengunjung | tanpa login |
| Panel admin | `/login` | Admin & operator sekolah | email + kata sandi |
| Portal PPDB | `/ppdb/masuk` | Calon siswa / orang tua | email **atau** NIK + kata sandi |

**Peran di panel admin**
- `Admin`: semua menu, termasuk Petugas & Hak Akses.
- `Operator`: semua kecuali pengelolaan akun petugas (ditolak 403).

---

## 3. STRUKTUR ISI YANG WAJIB ADA

Nomor halaman di bawah adalah saran; jaga urutannya.

**Depan (6 hlm)**
1. Sampul: judul, subjudul, nama sekolah, tahun ajaran, logo.
2. Halaman hak pakai + versi dokumen (`v1.0 — November 2026`) + catatan
   "perbarui bila menu berubah".
3. Kata pengantar (≤180 kata) — dari kepala sekolah, bukan dari vendor.
4. Cara memakai panduan: arti ikon penanda, arti blok "Catatan", "Perhatian",
   "Tips", dan cara membaca nomor langkah pada tangkapan layar.
5. Daftar isi (dengan nomor halaman; harus cocok setelah final).
6. Peta sistem: satu diagram tiga pintu masuk + alur PPDB 6 tahap.

**Bab 1 — Masuk dan Keluar (4 hlm)**
- Login admin: buka `/login`, isi email, isi kata sandi, klik tombol masuk.
- Lupa kata sandi admin (`/admin/lupa-sandi`), tautan reset berlaku terbatas.
- Keluar sesi (tombol "Keluar sesi" di kiri bawah).
- Portal siswa: `/ppdb/masuk`, isian `identifier` = email **atau** NIK.
- Perbedaan pesan galat "kredensial salah" vs "akun terkunci".

**Bab 2 — Ringkasan Operasional (2 hlm)**
- 4 kartu angka: Total Pendaftar, Diterima & Terverifikasi, Perlu Tindak
  Lanjut, Publikasi Berita — jelaskan apa yang harus dilakukan bila angkanya
  naik.
- 8 kartu "Akses Cepat Modul".
- Blok "Distribusi Status Pendaftar PPDB".

**Bab 3 — Pendaftar PPDB (10 hlm)** ← bab terpanjang, paling sering dipakai
- 3.1 Melihat daftar dan menyaring status (`/admin/ppdb`).
- 3.2 Membuka detail satu pendaftar (`/admin/ppdb/online/{id}`): data diri,
  jalur, pembayaran, berkas, kuesioner.
- 3.3 Mengubah status pendaftar (Baru → Dihubungi → Diterima / Ditolak).
  Sertakan konsekuensi tiap status terhadap portal siswa.
- 3.4 Verifikasi berkas: 4 berkas wajib (KK, Akta Kelahiran, SKL/Rapor, Foto).
  Cara menandai terverifikasi, cara menolak, cara mengunduh berkas.
- 3.5 Verifikasi pembayaran dan melihat bukti transfer.
- 3.6 Mengatur jalur pendaftaran (`/admin/ppdb`, bagian jalur: tambah, ubah,
  hapus) dan efeknya ke pilihan di portal siswa.
- 3.7 Mereset kata sandi akun siswa dari panel admin.
- 3.8 Ekspor CSV (`/admin/ekspor/pendaftar.csv`, `/admin/ekspor/ppdb-online.csv`):
  kapan dipakai, isi kolom, catatan bahwa berkas dibuka di Excel/Sheets.

**Bab 4 — Berita & Pengumuman (6 hlm)**
- 4.1 Membuat berita: judul, isi, mode **Visual / HTML / Pratinjau**.
- 4.2 Toolbar format (paragraf, tebal, miring, garis bawah, coret, daftar,
  kutipan, tautan, sisipkan foto).
- 4.3 "Setelan Entri": status (Terbit langsung / Simpan sebagai draf), gambar
  sampul (unggah atau pilih dari galeri sekolah), permalink/slug, ringkasan
  untuk kartu & SEO (maks 300 karakter), templat kerangka berita.
- 4.4 Menyimpan draf vs menerbitkan; perbedaan hasil di situs publik.
- 4.5 Mengelola daftar artikel: cari, filter status, "Buka di Studio",
  "Jadikan Draf", "Baca", "Hapus".
- Aturan penulisan: judul ≤ 70 karakter, ringkasan 1–2 kalimat, satu sampul
  rasio 16:9.

**Bab 5 — Galeri Foto (3 hlm)**
- Unggah foto (JPG/PNG/WebP, batas ukuran), alt text/keterangan, hapus foto,
  dan pengaruhnya ke halaman publik `/berita` & `/`.

**Bab 6 — Ulasan Alumni (3 hlm)**
- Alur moderasi: masuk → dibaca → ubah status (tampil/sembunyi) → hapus.
- Panduan menilai ulasan yang layak tampil (tanpa data pribadi, tanpa ujaran
  kebencian).

**Bab 7 — Kurikulum & Profil (3 hlm)**
- Mengubah 11 mata pelajaran, jam pelajaran, dan data pokok sekolah yang
  tampil di halaman `/akademik` dan `/profil`.

**Bab 8 — Pengaturan Situs (4 hlm)**
- Identitas sekolah, logo, foto beranda, brosur PPDB, tahun ajaran, dan teks
  halaman. Beri peringatan eksplisit: **perubahan di sini langsung terlihat
  publik**; sarankan mengubah saat jam sepi.

**Bab 9 — Petugas & Hak Akses (4 hlm)** *(hanya admin)*
- Menambah petugas: nama, email login, peran (Operator/Admin), kata sandi awal.
- Aturan kata sandi berlaku di seluruh sistem: **8–16 karakter, harus ada
  huruf besar, huruf kecil, dan angka**.
- Mengganti kata sandi petugas lain (halaman khusus per petugas).
- Menghapus akun, dan batasan: admin tidak bisa mereset kata sandinya sendiri
  lewat menu petugas (diarahkan ke Akun & Kata Sandi).

**Bab 10 — Akun & Kata Sandi (3 hlm)**
- Ganti kata sandi sendiri: kata sandi lama, baru, konfirmasi.
- Kesalahan umum: sandi lama salah, konfirmasi tidak cocok, sandi sama dengan
  yang lama, sandi di luar 8–16 karakter.

**Bab 11 — Portal Calon Siswa (8 hlm)**
- Pendaftaran akun `/ppdb/daftar`: langkah 1 Data Akun (email, kata sandi,
  ulangi), langkah 2 Identitas Siswa (nama, lahir, WA, NIK).
- 6 tahap berurutan, jelaskan bahwa tahap terkunci sampai tahap sebelumnya
  selesai:
  1. Pendaftaran Jalur
  2. Pembayaran / Konfirmasi (gratis atau transfer + unggah bukti)
  3. Pengisian Data Diri
  4. Unggah Berkas Persyaratan (4 berkas wajib)
  5. Kuesioner Calon Siswa
  6. Kartu Peserta & Pengumuman (cetak kartu, pantau hasil seleksi)
- Menu Pengumuman, Bantuan, Akun & Kata Sandi, Tata Cara Pendaftaran.
- Bagian khusus untuk **orang tua**: apa yang harus disiapkan sebelum mulai
  (KK, akta, SKL/rapor, foto, nomor WA aktif).

**Bab 12 — Situs Publik (3 hlm)**
- Peta halaman: `/`, `/profil`, `/akademik`, `/berita`, `/alumni`, `/ppdb`,
  `/kontak`.
- Dua tombol utama: "Alur pendaftaran" → `/ppdb/daftar`,
  "Kenali sekolah" → `/profil`.

**Belakang (6 hlm)**
- Pemecahan masalah (minimal 12 kasus): tidak bisa login, lupa sandi, email
  reset tidak masuk (termasuk catatan bahwa pengiriman email bergantung SMTP),
  unggahan gagal karena ukuran, tahap terkunci, kartu peserta tidak muncul,
  status tidak berubah, halaman publik tidak berubah setelah simpan.
- Daftar istilah (PPDB, jalur inden/prestasi/reguler, verifikasi, draf,
  slug/permalink, NIK, NPSN, SKL).
- Checklist operasional harian/mingguan panitia (tabel centang).
- Halaman kontak bantuan sekolah + jam layanan.
- Sampul belakang.

---

## 4. ARAH DESAIN (WAJIB DIIKUTI, JANGAN PAKAI TEMPLATE CANVA)

Buku ini harus terasa **satu keluarga** dengan websitenya, bukan tema baru.

**Token yang dipakai (sama dengan situs):**
```
Aksen utama   #0e5e8f   (biru sekolah)
Aksen terang  #126ea6
Tinta         #18181b  ·  Tinta-2 #3f3f46  ·  Tinta-3 #6b6b74
Latar         #ffffff  ·  Latar tint #f4f4f5  ·  Kertas #fafbfc
Garis         #e4e4e7
Radius        kecil 10px · sedang 16px · besar 24px  (untuk kotak/catatan)
Tipografi     Plus Jakarta Sans (200–800)
Motif         titik 1px, jarak 26px, opasitas 6–8% — hanya di bidang kosong
```

**Keputusan yang harus bisa Anda jelaskan** (tulis di catatan desain):
- Kenapa satu warna aksen saja: karena panduan dibaca sambil bekerja, penanda
  warna harus berarti (biru = aksi, merah = peringatan, bukan dekorasi).
- Kenapa tangkapan layar diletakkan di kanan dan langkah di kiri: mata staf
  bergerak dari teks pendek ke gambar, bukan sebaliknya.
- Kenapa tidak ada ilustrasi orang/karikatur: mengalihkan perhatian dari bukti.

**Yang dilarang:**
- Emoji sebagai ikon; ikon garis 1,5–2px dari set yang konsisten.
- Gradien ungu/biru, glow, blob, glassmorphism, kartu seragam tanpa hierarki.
- Eyebrow ALL-CAPS di atas semua judul; nomor hiasan `01/02/03` bila bukan
  urutan; teks placeholder seperti "Lorem ipsum" atau "Coming soon".
- Nomor halaman berupa deretan angka tanpa bab; gunakan format `Bab 3 · 24`.

**Grid cetak:**
- Sampul: margin 0, judul 72–88pt, area kosong minimal 30%.
- Halaman isi: margin atas 18 mm, bawah 20 mm, dalam 22 mm, luar 16 mm,
  grid 12 kolom, gutter 5 mm, baseline 4 mm, nomor halaman di luar grid.
- Satu halaman maksimal **7 langkah** dan **1 tangkapan layar utama**; kalau
  lebih, pecah ke halaman berikutnya.

**Halaman pembuka bab (chapter opener):** latar `#0e5e8f`, nomor bab besar
120pt, judul 40pt, satu kalimat tujuan bab, tanpa gambar.

---

## 5. SPESIFIKASI TANGKAPAN LAYAR

Ambil di aplikasi yang benar-benar berjalan, bukan rekayasa.

```
Viewport     : 1440 × 900, deviceScaleFactor 2 (hasil 2880 × 1800)
Tema         : terang saja
Data         : pakai data nyata; jangan mengosongkan tabel
Anotasi      : kotak/angka penanda 1..n, warna #0e5e8f, garis 2px, angka 24pt
               putih di dalam bulatan biru; posisi tidak menutupi teks tombol
Format       : PNG, tanpa bayangan tepi browser (crop ke area konten)
```

Daftar minimal 24 tangkapan layar (nama file → isi):

```
01-login.png                /login kosong
02-login-galat.png          pesan galat kredensial salah
03-ringkasan.png            /admin penuh
04-ppdb-daftar-pendaftar.png /admin/ppdb tabel + filter
05-ppdb-detail.png          /admin/ppdb/online/{id} bagian data diri
06-ppdb-status.png          dialog/aksi ubah status
07-ppdb-berkas.png          daftar 4 berkas + tombol verifikasi
08-ppdb-pembayaran.png      verifikasi pembayaran + bukti
09-ppdb-jalur.png           pengaturan jalur
10-csv.png                  tombol ekspor + contoh isi CSV
11-berita-editor.png        editor berita mode Visual
12-berita-toolbar.png       toolbar difokuskan
13-berita-setelan.png       panel Setelan Entri
14-berita-pratinjau.png     mode Pratinjau
15-berita-daftar.png        daftar artikel + filter
16-galeri.png               /admin/galeri
17-alumni.png               /admin/alumni moderasi
18-akademik.png             /admin/akademik
19-pengaturan.png           /admin/pengaturan
20-petugas.png              /admin/petugas tabel
21-sandi-petugas.png        /admin/petugas/{id}/sandi
22-akun.png                 /admin/akun
23-portal-daftar.png        /ppdb/daftar langkah 1
24-portal-beranda.png       /ppdb/portal 6 tahap
25-portal-berkas.png        /ppdb/portal/berkas
26-portal-kartu.png         /ppdb/portal/kartu-peserta
27-publik-berita.png        /berita
28-publik-ppdb.png          /ppdb
```

Kredensial uji untuk mengambil tangkapan layar (minta ke pemilik sistem;
jangan menuliskan kata sandi di dalam buku):

```
Admin    : admin@smppgri4waru.id
Operator : operator@smppgri4waru.id
Siswa    : akun calon siswa yang sudah menyelesaikan 6 tahap
```

**Aturan penting:** tidak ada satu pun kata sandi, token, atau tautan reset
yang boleh tercetak di buku. Kalau perlu menampilkan, sensor dengan blok abu.

---

## 6. GAYA PENULISAN LANGKAH

- Satu langkah = satu aksi, diawali kata kerja: "Klik **Pendaftar PPDB** di
  menu kiri." bukan "Pendaftar PPDB adalah menu yang…".
- Nama elemen UI ditulis **tebal** dan persis: **Simpan Draf**, **Publikasikan**,
  **Ganti sandi**, **Ubah Status**.
- Jangan menyebut nomor baris kode, nama tabel database, atau istilah teknis
  (`route`, `middleware`, `CSRF`, `slug` boleh disebut sebagai "permalink").
- Setiap bab ditutup blok **"Kalau ada yang tidak sesuai"** berisi 2–3 kasus
  gagal dan jalan keluarnya.
- Kalimat maksimal 20 kata. Paragraf maksimal 3 kalimat.

---

## 7. ALUR KERJA YANG HARUS ANDA JALANKAN

1. **Inventaris dulu.** Buka setiap halaman di daftar bagian 3 dengan akun
   admin dan akun siswa. Catat label tombol yang sebenarnya. Kalau ada yang
   tidak cocok dengan dokumen ini, **ikuti aplikasi**, dan laporkan
   perbedaannya di akhir.
2. **Tulis isi dulu, desain kemudian.** Susun naskah per bab dalam teks biasa
   (tanpa desain) agar mudah dikoreksi. Jangan membuat halaman jadi sebelum
   naskahnya disetujui.
3. **Ambil tangkapan layar** sesuai bagian 5, beri nomor penanda.
4. **Tentukan arah desain dua kali.** Buat dua variasi halaman isi (bukan dua
   tema warna): (A) tangkapan layar besar dengan langkah bernomor di bawah,
   (B) langkah di kiri, tangkapan layar di kanan. Tunjukkan keduanya, jelaskan
   konsekuensinya, lalu pakai yang dipilih.
5. **Bangun master page di Canva**: sampul, pembuka bab, halaman langkah,
   halaman referensi penuh, halaman checklist, halaman kontak. Semua halaman
   isi memakai master, bukan salinan manual.
6. **Isi halaman**, jaga konsistensi: satu jenis kotak catatan, satu gaya
   penanda, satu gaya nomor halaman.
7. **Audit sendiri sebelum menyerahkan** (bagian 8), perbaiki, lalu ekspor.

---

## 8. GERBANG KUALITAS SEBELUM SERAH TERIMA

Cetak laporan PASS/FAIL untuk tiap butir. Jangan menyerahkan bila ada FAIL.

```
[ ] Semua label menu di buku sama persis dengan aplikasi (cek 20 label acak)
[ ] Tidak ada fitur yang tidak ada di aplikasi
[ ] Daftar isi cocok dengan nomor halaman final
[ ] Setiap langkah bernomor punya penanda dengan nomor yang sama
[ ] Tidak ada tangkapan layar terpotong, buram, atau < 300 dpi
[ ] Tidak ada kata sandi, token, email pribadi asli, atau NIK asli tercetak
[ ] Semua teks minimal 10pt; keterangan gambar minimal 8,5pt
[ ] Tidak ada teks menempel margin < 10 mm atau terpotong bleed
[ ] Satu halaman maksimal 7 langkah
[ ] Setiap bab punya blok "Kalau ada yang tidak sesuai"
[ ] Tidak ada emoji, tidak ada "Lorem ipsum", tidak ada teks placeholder
[ ] Halaman ganjil di kanan (recto) saat dicetak dua sisi
[ ] PDF cetak memakai bleed 3 mm + tanda potong; PDF web tanpa bleed
```

Cara pembuktian: render PDF ke gambar (`pdftoppm -r 150`) lalu periksa setiap
halaman pada 100%; bukan hanya melihat thumbnail.

---

## 9. KELUARAN YANG DISERAHKAN

```
guidebook-smp-pgri-4waru-v1.pdf        (cetak, A4, bleed 3 mm, 300 dpi)
guidebook-smp-pgri-4waru-v1-web.pdf    (ringan, < 15 MB, untuk dibagikan)
guidebook-pages/*.png                  (PNG per halaman, 300 dpi)
screenshots/*.png                      (tangkapan layar sumber + anotasi)
catatan-desain.md                      (keputusan desain + daftar istilah)
laporan-qa.md                          (checklist bagian 8, PASS/FAIL)
selisih-dokumen.md                     (perbedaan aplikasi vs prompt ini)
```

Kalau Canva dipakai: berikan juga tautan desain yang bisa diubah pemilik
sekolah, dan pastikan semua warna/font berasal dari Brand Kit, bukan warna
lepas.

---

## 10. KRITERIA SELESAI

Panduan dinyatakan selesai bila **staf TU yang belum pernah memakai sistem**
bisa, hanya dengan buku ini:

1. Masuk ke panel admin tanpa dibantu.
2. Memverifikasi satu pendaftar (berkas + pembayaran) sampai status berubah.
3. Menerbitkan satu berita dengan sampul, lalu menemukannya di `/berita`.
4. Menambah satu petugas operator dan mengganti kata sandinya.
5. Mengganti kata sandinya sendiri.
6. Menjelaskan ke orang tua cara mendaftar PPDB sampai mendapat kartu peserta.

Kalau salah satu tidak tercapai, bab terkait belum selesai.
