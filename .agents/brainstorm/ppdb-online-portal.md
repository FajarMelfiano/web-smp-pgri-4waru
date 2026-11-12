# Brainstorm: Sistem & Portal PPDB Online SMP PGRI 4 Waru

Tanggal: 2026-11-11  
Fase: P0 (Gerbang Brainstorm)  
Rujukan UX/Alur: `https://smktelkom-sda.sch.id/ppdb` + 5 tangkapan layar portal `ppdb.telkomschools.sch.id` (Signup 2 langkah, Beranda Portal, Modal Tata Cara Pendaftaran, Pilih Jalur).

---

## Langkah 1 — Framing

1. **Masalah yang diselesaikan**:
   Saat ini situs SMP PGRI 4 Waru (`/ppdb`) hanya menyediakan alur pendaftaran offline ("di kantor sekolah") dan formulir pesan 3 langkah. Calon siswa dan orang tua tidak dapat membuat akun PPDB, login memantau status, memilih jalur gelombang, mengunggah dokumen persyaratan (KK, Akta, SKL/Rapor, Foto), maupun melihat hasil pengumuman dan mencetak kartu peserta secara daring.
2. **User utama & konteks**:
   - **Calon Siswa / Orang Tua Wali** (mayoritas HP Android 390px, jaringan seluler standar, non-teknis): butuh alur langkah demi langkah yang jelas, bahasa Indonesia sederhana, validasi langsung, dan kemampuan melanjutkan proses kapan saja lewat login akun.
   - **Admin / Operator Tata Usaha** (desktop/laptop di kantor sekolah Jl. Anggrek No. 109): butuh pengaturan jalur & biaya PPDB, verifikasi pembayaran dan kelengkapan berkas dalam satu panel admin, penetapan status kelulusan + catatan pengumuman, serta ekspor CSV.
3. **Success criteria**:
   - Publik dapat mengakses informasi PPDB, jadwal jalur aktif dari DB, syarat berkas, dan tombol `Daftar PPDB Online` (`/ppdb/daftar`) serta `Masuk Portal PPDB` (`/ppdb/masuk`) dari halaman `/ppdb` maupun Beranda `/`.
   - Calon siswa dapat mendaftar akun 2 langkah (Email + Kata Sandi berindikator syarat → Identitas Awal + NIK + Tanggal Lahir + WhatsApp), masuk ke portal ber-sidebar, dan menyelesaikan seluruh 6 tahap (`Pilih Jalur` → `Bayar/Konfirmasi Pendaftaran` → `Isi Data Diri` → `Unggah Berkas` → `Kuesioner` → `Kartu Peserta` & `Pengumuman`).
   - Dokumen pribadi siswa (KK, Akta, SKL, Foto, Bukti Bayar) tersimpan di disk privat dan hanya dapat diakses oleh siswa bersangkutan atau petugas admin yang login.
   - Seluruh pengujian PHPUnit (lama & baru), Pint formatter, dan verifikasi Playwright 390px + 1280px (terang & gelap) lolos 100%.
4. **Bukan scope kali ini**:
   - Integrasi API payment gateway berbayar otomatis (Midtrans/Xendit) dengan merchant key produksi.
   - OTP SMS/WhatsApp berbayar otomatis.
   - Sistem ujian CBT / tes online di dalam browser.

---

## Job Stories & Asumsi Berisiko

### Job Stories
1. **Membuat Akun PPDB**: *Saat saya (orang tua/calon siswa) siap mendaftarkan anak ke SMP PGRI 4 Waru, saya ingin membuat akun PPDB dengan email, kata sandi aman, tanggal lahir, WhatsApp wali, dan NIK, sehingga data pendaftaran tersimpan dan bisa dilanjutkan kapan saja.*
2. **Memilih Jalur & Konfirmasi Biaya**: *Saat masuk ke portal PPDB, saya ingin melihat alur 6 langkah dan memilih jalur pendaftaran yang sedang dibuka beserta rincian periode dan biayanya, sehingga saya mendapat Nomor Pendaftaran resmi dan tahu langkah selanjutnya.*
3. **Pembayaran / Verifikasi Pendaftaran**: *Saat jalur yang dipilih memiliki biaya daftar (atau gratis), saya ingin memilih bayar transfer (unggah bukti) atau bayar di kantor TU, lalu memantau riwayat verifikasinya agar tahap pengisian biodata terbuka.*
4. **Melengkapi Biodata, Berkas & Kuesioner**: *Saat pembayaran terkonfirmasi, saya ingin mengisi biodata lengkap siswa & orang tua, mengunggah foto/PDF berkas persyaratan, dan mengisi kuesioner sekolah, sehingga status pendaftaran saya siap diverifikasi panitia.*
5. **Kartu Peserta & Pengumuman**: *Saat pendaftaran selesai, saya ingin mencetak Kartu Peserta dan memantau halaman Pengumuman untuk mengetahui apakah saya diterima dan instruksi daftar ulangnya.*
6. **Verifikasi oleh Petugas Admin**: *Saat ada calon siswa mendaftar online, sebagai Operator/Admin saya ingin meninjau bukti bayar, berkas unggahan, dan biodata dari panel `/admin/ppdb`, lalu menyetujui atau memberi catatan perbaikan sebelum menetapkan status diterima/ditolak.*

### Asumsi Berisiko
- **Feasibility (Isolasi Sesi Auth)**: Memakai tabel `users` yang sama berisiko menimpa sesi Admin saat testing di browser yang sama atau mencampur otorisasi. *Mitigasi*: Gunakan auth guard terpisah `ppdb` (`ppdb_accounts`).
- **Viability (Biaya & Alur Pembayaran)**: Tidak semua SMP swasta memungut biaya daftar online via gateway. *Mitigasi*: Admin dapat mengatur tiap jalur `biaya_daftar = 0` (otomatis lunas langsung lanjut Step 3) atau nominal tertentu (unggah bukti transfer / konfirmasi bayar di kantor TU -> diverifikasi Admin).
- **Usability (HP 390px & Upload Foto HP)**: Foto kamera HP sering berformat JPG/PNG/WebP besar atau tabel `Pilih Jalur` melebar di 390px. *Mitigasi*: Desain tabel responsif, stepper aman untuk 390px, batas ukuran 4 MB dengan pesan jelas, dan konfirmasi ulang sebelum finalisasi.
- **Compatibility (Kompatibilitas Fitur Lama)**: Tes dan alur lama (`GET /ppdb` dengan `#ppdb-doc-checklist` dan `POST /ppdb` konsultasi cepat) tidak boleh rusak. *Mitigasi*: Pertahankan endpoint & checklist tersebut sebagai bagian dari halaman publik `/ppdb`.

---

## Langkah 2 — Tiga Pendekatan

1. **Pendekatan A — Form Wizard Publik Tanpa Login (`pendaftar` tunggal)**:
   - *Ide*: Perluas form `/ppdb` menjadi form panjang sekali kirim tanpa akun/login.
   - *Kelebihan*: Cepat dibuat, tanpa tabel akun.
   - *Kekurangan*: Siswa tidak bisa keluar-masuk melanjutkan berkas, tidak ada portal siswa, tidak sesuai 5 gambar referensi.
   - *Risiko*: Data hilang jika koneksi putus; tidak ada halaman pengumuman pribadi.
2. **Pendekatan B — Satu Tabel `users` (`role = 'siswa'`) + Guard `web` Tunggal**:
   - *Ide*: Gabung akun calon siswa ke `users`, bedakan lewat `role`.
   - *Kelebihan*: Hanya 1 tabel auth.
   - *Kekurangan*: Sesi admin & siswa saling menimpa di browser yang sama; kolom NIK/tanggal lahir mengotori tabel petugas; rawan bug otorisasi jika middleware lupa cek role.
   - *Risiko*: Kebocoran akses antar role atau bentrok redirect `/login`.
3. **Pendekatan C — Guard Terpisah `ppdb` (`ppdb_accounts`) + Skema Relasional Portal PPDB + Sinkronisasi Admin**:
   - *Ide*: Tambah guard session `ppdb` untuk `PpdbAccount` (login email/NIK + password), dengan relasi ke `ppdb_jalur`, `ppdb_pendaftaran` (1 siswa = 1 pendaftaran aktif, nomor registrasi unik), `ppdb_pembayaran`, dan `ppdb_berkas` (disk privat). Panel `/admin/ppdb` mengelola jalur, verifikasi pembayaran/berkas, dan kelulusan, sekaligus tetap mendukung tabel `pendaftar` lama untuk pesan konsultasi.
   - *Kelebihan*: Pemisahan keamanan total antara siswa dan petugas, alur identik dengan 5 referensi screenshot, data ternormalisasi dan mudah diuji PHPUnit, file pribadi aman.
   - *Kekurangan*: Memerlukan 1 migrasi baru, 1 controller auth PPDB, 1 controller portal PPDB, perluasan `AdminController`, serta view portal.
   - *Risiko*: Kompleksitas state antar tahap perlu aturan transisi yang tegas.

**Dipilih Pendekatan C** karena paling aman, bersih, mudah dirawat, dan memenuhi seluruh kebutuhan portal daftar/login siswa tanpa mengganggu panel admin maupun pengujian yang sudah ada.

---

## Langkah 2b — Mekanisme & Sistem

1. **Alur Data**:
   - Publik `/ppdb` (info + jalur aktif dari `ppdb_jalur`) → `/ppdb/daftar` (2 tahap JS/server: validasi Email & Password → validasi Nama, Tgl Lahir, WA, NIK → simpan `ppdb_accounts` + `ppdb_pendaftaran` awal → login otomatis ke guard `ppdb`) → `/ppdb/portal` (`Beranda`).
   - Di Portal:
     `Pilih Jalur` (terbit `no_pendaftaran`, mis. `PPDB-2026-0001`, buat tagihan) → `Bayar Pendaftaran` (jika biaya = 0 otomatis `terkonfirmasi`; jika > 0 siswa pilih Transfer Bank + unggah bukti atau Bayar di Kantor TU → `menunggu_verifikasi` → Admin klik `Verifikasi Lunas`) → `Isi Data Diri` (biodata siswa + orang tua + asal SD) → `Unggah Berkas` (KK, Akta, SKL/Rapor, Pas Foto ke disk `local`) → `Kuesioner` (finalisasi pendaftaran) → `Kartu Peserta` (siap cetak) & `Pengumuman` (tampilkan keputusan Admin).
2. **State & Transisi**:
   - `tahap_pembayaran`: `belum_pilih_jalur` → `menunggu_pembayaran` → `menunggu_verifikasi` → `lunas` (atau `ditolak` → dapat kirim ulang bukti).
   - `tahap_data_diri`: `terkunci` (sebelum lunas) → `belum_diisi` → `lengkap`.
   - `tahap_berkas`: `terkunci` → `belum_lengkap` → `menunggu_verifikasi` → `terverifikasi` / `perlu_perbaikan`.
   - `tahap_kuesioner`: `terkunci` → `belum_diisi` → `selesai`.
   - `status_seleksi`: `proses` → `terverifikasi` → `diterima` / `ditolak`.
3. **Kontrak Rute**:
   - Publik: `GET /ppdb`, `POST /ppdb` (pertahankan pesan konsultasi cepat).
   - Auth PPDB (`guest:ppdb` / `auth:ppdb`): `GET /ppdb/daftar`, `POST /ppdb/daftar`, `GET /ppdb/masuk`, `POST /ppdb/masuk`, `POST /ppdb/keluar`.
   - Portal PPDB (`middleware('auth:ppdb')`, prefix `/ppdb/portal`):
     - `GET /ppdb/portal` (`beranda`)
     - `GET /ppdb/portal/jalur`, `POST /ppdb/portal/jalur` (`pilih-jalur`)
     - `GET /ppdb/portal/pembayaran`, `POST /ppdb/portal/pembayaran`, `GET /ppdb/portal/riwayat-pembayaran`
     - `GET /ppdb/portal/data-diri`, `POST /ppdb/portal/data-diri`
     - `GET /ppdb/portal/berkas`, `POST /ppdb/portal/berkas`, `GET /ppdb/portal/berkas/{berkas}/unduh`
     - `GET /ppdb/portal/kuesioner`, `POST /ppdb/portal/kuesioner`
     - `GET /ppdb/portal/kartu-peserta`
     - `GET /ppdb/portal/pengumuman`, `GET /ppdb/portal/bantuan`
   - Admin (`middleware('auth')` pada guard `web`):
     - `GET /admin/ppdb` (tab Pendaftar Online, Kelola Jalur PPDB, & Konsultasi/Pesan Lama)
     - `POST /admin/ppdb/jalur`, `PATCH /admin/ppdb/jalur/{jalur}`, `DELETE /admin/ppdb/jalur/{jalur}` (role admin)
     - `GET /admin/ppdb/online/{pendaftaran}`, `PATCH /admin/ppdb/online/{pendaftaran}/pembayaran`, `PATCH /admin/ppdb/online/{pendaftaran}/berkas/{berkas}`, `PATCH /admin/ppdb/online/{pendaftaran}/status`, `GET /admin/ppdb/online/{pendaftaran}/berkas/{berkas}/unduh`, `GET /admin/ppdb/online/{pendaftaran}/bukti-bayar`, `GET /admin/ekspor/ppdb-online.csv`.
4. **Model Data**:
   - `ppdb_accounts`: `id`, `nama_lengkap`, `email` (unique), `nik` (16 char unique), `tanggal_lahir`, `whatsapp`, `password`, `remember_token`, `timestamps`.
   - `ppdb_jalur`: `id`, `nama`, `kode` (unique), `deskripsi`, `tanggal_mulai`, `tanggal_selesai`, `biaya_daftar` (unsignedInteger default 0), `kuota` (nullable unsignedInteger), `dibuka` (boolean default true), `urutan`, `timestamps`.
   - `ppdb_pendaftaran`: `id`, `ppdb_account_id` (unique FK), `ppdb_jalur_id` (nullable FK), `no_pendaftaran` (nullable unique), `tahun_ajaran`, biodata siswa (`nisn`, `jenis_kelamin`, `tempat_lahir`, `agama`, `alamat`, `asal_sd`, `tahun_lulus_sd`, `anak_ke`, `jumlah_saudara`), data ortu (`nama_ayah`, `pekerjaan_ayah`, `nama_ibu`, `pekerjaan_ibu`, `nama_wali`, `no_wa_wali`, `penghasilan_ortu`), `kuesioner` (JSON/text), flag tahap (`jalur_dipilih_at`, `data_diri_lengkap_at`, `berkas_lengkap_at`, `kuesioner_selesai_at`), `status_seleksi` (`proses`, `terverifikasi`, `diterima`, `ditolak`), `catatan_admin`, `timestamps`.
   - `ppdb_pembayaran`: `id`, `ppdb_pendaftaran_id` (FK), `kode_invoice` (unique), `nominal`, `metode` (`gratis`, `transfer`, `kantor`), `status` (`menunggu_pembayaran`, `menunggu_verifikasi`, `lunas`, `ditolak`), `bukti_path` (nullable), `catatan_siswa`, `catatan_admin`, `verified_by` (nullable FK `users`), `verified_at`, `timestamps`.
   - `ppdb_berkas`: `id`, `ppdb_pendaftaran_id` (FK), `jenis` (`kk`, `akta`, `skl`, `foto`), `file_path`, `original_name`, `mime_type`, `status` (`menunggu`, `valid`, `ditolak`), `catatan_admin`, `timestamps` (unique `[ppdb_pendaftaran_id, jenis]`).
5. **Keamanan Mekanisme**:
   - Guard terpisah `ppdb` + throttle 5/menit pada `/ppdb/daftar` & `/ppdb/masuk`.
   - Validasi server-side ulang pada setiap transisi tahap (mencegah siswa langsung POST ke `/ppdb/portal/data-diri` atau `/kuesioner` sebelum syarat tahap sebelumnya terpenuhi).
   - File pribadi (KK, Akta, SKL, Foto, Bukti Transfer) disimpan di disk `'local'` (`storage/app/private/ppdb/...`), diunduh hanya lewat controller dengan cek kepemilikan (`$berkas->pendaftaran->ppdb_account_id === Auth::guard('ppdb')->id()`) atau guard admin `web`.

---

## Langkah 3 — Rencana Anti-AI-Slop

- **Identitas**: Portal PPDB resmi SMP PGRI 4 Waru yang hangat, tertib, dan lugas — mengikuti struktur dashboard 5 tangkapan layar rujukan (`Telkom Schools PPDB`) tetapi sepenuhnya mengenakan token visual SMP PGRI 4 Waru (`Plus Jakarta Sans`, biru PGRI `--red-solid: #126ea6`, tinta `--ink: #18181b`, permukaan `#ffffff`/`#fafbfc`/`#f4f4f5`, sudut membulat konsisten, dan mode gelap penuh).
- **Komponen & Ikon**: Gunakan sprite Phosphor (`partials/icons.blade.php`), tanpa emoji dekoratif atau teks generik; seluruh state kosong, terkunci, menunggu verifikasi, error, dan sukses dirancang eksplisit dengan tombol aksi lanjut yang jelas.
- **Mobile 390px**: Sidebar berubah menjadi drawer geser (sama dengan pola `.adm-sidebar`), tabel jalur/riwayat memiliki tampilan rapi tanpa horizontal scroll halaman, tombol aksi minimum 44px.

---

## Langkah 4 — Rencana Kebenaran & Edge Cases

1. **Edge cases**:
   - Pendaftar mengisi kata sandi tidak memenuhi 3 syarat (panjang 8–16, huruf besar+kecil, 1 angka) atau konfirmasi tidak cocok → ditolak di klien & server dengan pesan spesifik per rule.
   - NIK bukan 16 digit atau sudah terdaftar, atau email sudah dipakai → tampilkan pesan validasi jelas dengan tautan ke `/ppdb/masuk`.
   - Jalur `Ditutup` atau periode lewat/kuota penuh di-POST manual → ditolak di server (`422` / redirect error).
   - Siswa mengunggah file > 4 MB atau bukan JPG/PNG/WebP/PDF → validasi `mimes` & `max:4096`.
   - Siswa mengganti berkas yang ditolak Admin → file lama dihapus aman dan status reset menjadi `menunggu`.
2. **Pre-mortem**:
   - *Skenario 1*: Siswa bingung saat jalur berbayar tidak langsung membuka langkah Data Diri. *Pencegahan*: Tampilkan ringkasan status bayar jelas + opsi `Bayar di Kantor TU` atau `Unggah Bukti Transfer` dan sediakan 1 jalur bebas biaya / mudah dikonfirmasi Admin.
   - *Skenario 2*: Admin membuka portal siswa untuk uji coba lalu kehilangan sesi admin. *Pencegahan*: Guard `ppdb` dan `web` sepenuhnya terpisah.
   - *Skenario 3*: Tampilan stepper 6 langkah atau tabel `Pilih Jalur` pecah di layar 390px. *Pencegahan*: Verifikasi screenshot Playwright di `390x844` (terang & gelap).

---

## Langkah 5 — Rencana Verifikasi

- `cd laravel && /home/melfiano/.local/php/php vendor/bin/pint --format agent`
- `cd laravel && /home/melfiano/.local/php/php artisan test --compact` (seluruh test lama + feature test lengkap alur daftar akun, login, pilih jalur, pembayaran/konfirmasi, biodata, upload berkas private + isolation check, kuesioner, kartu peserta, pengumuman, dan verifikasi admin).
- Playwright browser test pada `http://127.0.0.1:8000` di viewport `390x844` dan `1280x800` (mode terang & gelap) mencakup `/ppdb`, `/ppdb/daftar`, `/ppdb/masuk`, seluruh halaman `/ppdb/portal/*`, serta `/admin/ppdb` — memastikan 0 console error, 0 horizontal overflow, dan gambar/ikon/modal berfungsi penuh.
