# Brainstorm: Berita Publik, Galeri Foto & Ulasan Alumni SMP PGRI 4 Waru

## Langkah 1 — Framing
1. **Masalah**: Pengunjung situs publik (calon wali murid, siswa, dan alumni) membutuhkan bukti hidup kegiatan sekolah melalui berita bergambar, galeri dokumentasi foto yang bisa ditambah oleh pengurus sekolah, serta halaman khusus tempat alumni meninggalkan kesan/ulasan dan melihat kabar almamater.
2. **User Utama**:
   - **Publik / Calon Wali Murid**: Membaca berita terbaru, melihat foto kegiatan di Beranda & halaman Berita, serta membaca ulasan alumni yang sudah diverifikasi.
   - **Alumni SMP PGRI 4 Waru**: Mengirimkan ulasan/testimoni (nama, tahun lulus/angkatan, profesi/sekolah lanjutan, isi ulasan, dan foto opsional) melalui formulir publik.
   - **Admin / Operator TU**: Mengunggah & mengelola foto galeri, menerbitkan berita bergambar (beserta galeri foto lampiran), serta memoderasi ulasan alumni (`menunggu` -> `disetujui` / `sembunyikan` / `hapus`).
3. **Success Criteria**:
   - Beranda publik (`/`) menampilkan seksi Berita Terbaru, Galeri Foto Kegiatan, dan kutipan Ulasan Alumni yang telah disetujui.
   - Halaman `/berita` menampilkan daftar berita dengan foto sampul + seksi Galeri Foto Sekolah.
   - Halaman `/alumni` menampilkan daftar ulasan alumni yang disetujui + formulir publik pengiriman ulasan alumni (dengan honeypot anti-spam dan moderasi admin).
   - Panel Admin memiliki pengelolaan Galeri Foto dan Moderasi/Manajemen Alumni tanpa indikator warna hijau (tetap patuh palet merah `#c81e25`, hitam `#18181b`, netral).
4. **Bukan Scope**: Sistem jejaring sosial antar-alumni dengan login akun alumni atau komentar berbalas.

## Langkah 2 — Divergensi (3 Pendekatan)
1. **Pendekatan A (Tabel Terpisah `galeri` & `alumni_reviews` + Moderasi Admin)**:
   - *Ide*: Buat tabel khusus `galeri` (judul, kategori, deskripsi, foto) dan `alumni_reviews` (nama, angkatan, pekerjaan/sekolah lanjutan, ulasan, foto_path, status moderasi), ditampilkan di Beranda, `/berita`, `/alumni`, dan dikelola di `/admin/galeri` serta `/admin/alumni`.
   - *Kelebihan*: Struktur data bersih, ulasan alumni dari publik aman karena melewati moderasi (`disetujui`), dan admin bisa leluasa menambah/menghapus foto maupun ulasan kapan saja.
   - *Kekurangan*: Membutuhkan 1 migrasi baru dan 2 halaman admin tambahan.
   - *Risiko*: Moderasi perlu status default yang jelas.
2. **Pendekatan B (Menyimpan Foto & Alumni di JSON Settings)**:
   - *Ide*: Simpan array JSON di tabel `settings`.
   - *Kelebihan*: Tanpa tabel baru.
   - *Kekurangan*: Sulit difilter, sulit paginasi, rawan race-condition saat alumni mengisi form publik.
3. **Pendekatan C (Menggabungkan semua sebagai tipe Post di tabel `posts`)**:
   - *Ide*: Tambah kolom `type` (`berita`, `foto`, `alumni`) di `posts`.
   - *Kelebihan*: Satu tabel.
   - *Kekurangan*: Kolom spesifik alumni (`angkatan`, `pekerjaan`) jadi tidak terstruktur.

**Keputusan**: Pilih **Pendekatan A**. Paling kokoh, mudah diuji dengan PHPUnit, dan memisahkan entitas Berita (`posts`), Galeri Foto (`galeri`), serta Ulasan Alumni (`alumni_reviews`).

## Langkah 2b — Mekanisme & Sistem
1. **Alur Data**:
   - **Galeri Foto**: Admin unggah foto di `/admin/galeri` (atau lampirkan di berita) -> simpan berkas di `storage/app/public/galeri/` -> simpan baris di tabel `galeri` -> tampil otomatis di Beranda (`/`) dan `/berita`.
   - **Ulasan Alumni**:
     - Jalur Publik: Alumni isi form di `/alumni` (`POST /alumni` dengan rate limit & honeypot `website_url`) -> simpan ke `alumni_reviews` dengan `status = 'menunggu'` (atau admin bisa langsung tambah dari `/admin/alumni` dengan `status = 'terbit'`).
     - Jalur Moderasi: Admin/Operator di `/admin/alumni` klik `Terbitkan` (`status = 'terbit'`) atau `Sembunyikan` (`status = 'menunggu'`) -> ulasan `terbit` langsung tampil di `/alumni` dan Beranda (`/`).
2. **Model Data**:
   - Tabel `galeri`: `id`, `judul`, `kategori` (Kegiatan, Fasilitas, Prestasi, Dokumentasi), `keterangan` (nullable), `image_path`, `tampil_beranda` (boolean default true), `timestamps`.
   - Tabel `alumni_reviews`: `id`, `nama`, `angkatan` (string 10, contoh `2018`), `pekerjaan` (string 120, contoh `SMAN 1 Waru / Mahasiswa`), `ulasan` (text), `foto_path` (nullable), `status` (`menunggu`, `terbit`), `timestamps`.
3. **Kontrak Rute**:
   - Publik: `GET /berita` (`BeritaController@index`), `GET /berita/{slug}` (`BeritaController@show`), `GET /alumni` (`AlumniController@index`), `POST /alumni` (`AlumniController@store`).
   - Navigasi Publik (`layouts/app.blade.php`): Beranda, Profil, Akademik, Berita, Alumni, PPDB, Kontak.
   - Admin: `GET /admin/galeri`, `POST /admin/galeri`, `DELETE /admin/galeri/{galeri}`, `GET /admin/alumni`, `POST /admin/alumni`, `PATCH /admin/alumni/{alumni}/status`, `DELETE /admin/alumni/{alumni}`.

## Langkah 3 — Rencana Anti-AI-Slop
- `antislop active: during (global preference).`
- **Visual**: Mengikuti sistem desain Telkom (`#c81e25` merah, `#18181b` hitam tinta, permukaan `#fafbfc`/`#ffffff`, sudut `16px-24px`, tipografi Plus Jakarta Sans).
- **Tanpa Data Palsu (R-18 / R-38)**: Jika belum ada ulasan alumni atau foto yang diunggah, tampilkan empty state jujur yang mengundang alumni mengisi formulir atau mengarahkan admin mengunggah foto, dan hanya tampilkan data yang benar-benar ada di database.
- **Tanpa Bubble Hijau**: Status ulasan `terbit` menggunakan badge merah solid (`#c81e25`), status `menunggu` menggunakan badge merah muda lembut (`rgba(200,30,37,0.08)`).

## Langkah 4 & 5 — Rencana Kebenaran & Verifikasi
- Validasi ketat unggah gambar (`jpg,jpeg,png,webp`, maks 4MB), honeypot `website_url` pada form publik `/alumni`.
- Verifikasi dengan `./vendor/bin/pint --format agent`, `php artisan test --compact`, dan pengecekan visual Playwright 1440px & 390px pada `/`, `/berita`, `/alumni`, `/admin/galeri`, dan `/admin/alumni`.
