# Brainstorm: Situs SMP PGRI 4 Waru

Tanggal: 2026-11-10
Fase: P0 (blueprint) sampai P3 (implementasi) dalam satu sesi
Status: scope disetujui sebagai purwarupa tugas, bukan situs resmi sekolah

## 1. Framing

**Masalah.** Orang tua di Kureksari dan Waru yang mencari SMP swasta tidak punya
satu tempat untuk melihat identitas sekolah, mata pelajaran, dan cara mendaftar.
Situs resmi lama (smppgri4waru.com) tidak bisa diakses.

**Pengguna.** Orang tua calon siswa (HP Android, koneksi lambat, tidak teknis),
siswa kelas IX yang mencari info lanjut, dan guru/kepala sekolah yang perlu
membagikan satu tautan.

**Berhasil bila.**
1. Alamat, telepon, email, dan NPSN terbaca tanpa membuka halaman lain.
2. Jalur PPDB bisa dipahami dalam satu kali gulir, dan pesan pendaftaran bisa
   dikirim dari halaman itu juga.
3. Halaman terbuka di layar 390 px tanpa gulir ke samping, dan lolos keyboard.

**Bukan scope.** CMS, login, database, berita dinamis, galeri foto asli, PPDB
online dengan pembayaran, multi-bahasa.

## 2. Tiga pendekatan

**A. Situs profil statis, 5 halaman.** Ringan, tanpa build, bisa dibuka dari
folder mana pun. Kekurangan: header/footer digandakan per halaman.

**B. Next.js + CMS kepala sekolah.** Bisa diperbarui tanpa kode. Kekurangan:
butuh server, DB, dan akun; untuk tugas ini biayanya tidak sepadan.

**C. Satu halaman panjang (one-pager).** Cepat dibuat, tapi alamat dan PPDB
tenggelam, dan tautan yang dibagikan ke orang tua tidak menunjuk bagian tertentu.

**Dipilih A.** Semua isi bersifat tetap (identitas, mata pelajaran, alur PPDB),
jadi tidak butuh backend. Halaman terpisah membuat tautan WhatsApp bisa langsung
menunjuk halaman PPDB.

## 3. Mekanisme

- Alur data: konten HTML statis → CSS token → JS hanya untuk nav mobile, tema,
  dan formulir. Tidak ada jaringan selain peta OpenStreetMap dan pratinjau surel.
- Formulir PPDB: validasi di klien → pesan disusun → pengguna memilih "Buka
  aplikasi email" (mailto) atau "Salin pesan". Tidak ada data yang dikirim diam
  diam ke mana pun.
- Kegagalan: tanpa JS, formulir tetap tampil sebagai daftar kontak biasa; peta
  punya tautan teks sebagai cadangan; font punya fallback sistem.
- Keamanan: tidak ada input yang disimpan atau dieksekusi. Semua keluaran form
  disusun dari nilai yang di-escape saat ditulis ke DOM lewat `textContent`.

## 4. Arah desain

Reading: situs profil sekolah untuk orang tua di Waru, bahasa visual "buku induk
sekolah" (dokumen, garis tipis, cap), dial ENERGY 2 / RHYTHM 2 / MOTION 2.

- Palet: navy `#12233c` (identitas PGRI), kertas `#f4f3ee`, aksen marigold
  `#e8a712` (kuning logo PGRI). Hanya tiga warna + netral.
- Tipografi: Plus Jakarta Sans (huruf buatan Indonesia, alasan: situs sekolah
  Indonesia, angka NPSN dan tahun harus tajam di layar HP).
- Bentuk: radius 4 px di semua elemen, tanpa bayangan. Pemisah memakai garis
  rambut, bukan kartu.
- Satu hal yang diingat: kartu data pokok sekolah di hero dan cap "1979".
- Larangan yang dijaga: tanpa emoji, tanpa gradien ungu, tanpa ikon Lucide,
  tanpa kutipan/testimoni karangan, tanpa angka karangan.

## 5. Kebenaran data

Fakta yang dipakai (NPSN 20540020, alamat Jl. Anggrek No. 109 Kureksari, telepon
(031) 8538447, email smp.pgri4waru@gmail.com, Instagram @smppgri4waru, berdiri
1979, SK 421.3/2829/404.3.1/1979, naungan YPLP DASMEN PGRI Jatim, luas tanah
660 m2, belajar pagi 6 hari) berasal dari Data Referensi Kemendikdasmen dan
profil sekolah yang dipublikasikan.

Tidak dipakai: akreditasi (sumber saling bertentangan: satu menyebut B, satu
menyebut belum terakreditasi), jumlah siswa, daftar guru, prestasi, biaya, dan
jadwal PPDB. Visi dan misi ditandai sebagai draf. Footer menyatakan situs ini
purwarupa tugas, bukan situs resmi.

## 6. Verifikasi

- `python3 -m http.server` lalu cek tiap halaman dengan Chromium.
- Tangkapan layar 390 px dan 1440 px, mode terang dan gelap.
- Klik: menu mobile (buka/tutup/Escape), tautan nav, tombol tema, formulir
  kosong (validasi), formulir valid (panel hasil + salin pesan), peta.
- Cek kontras teks utama, fokus keyboard terlihat, tanpa gulir horizontal.
