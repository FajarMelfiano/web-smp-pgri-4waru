# Rencana Lengkap: Situs SMP PGRI 4 Waru (Laravel)

Versi: 1.0 · 2026-11-10
Pemilik: kamu (tugas web) · Pengerjaan: OpenCode
Status: menunggu aba-aba mulai Tahap 1

---

## 0. Ringkasan keputusan

| Hal | Keputusan | Alasan |
|---|---|---|
| Framework | Laravel 12.x, PHP 8.4 | Diminta. Rilis stabil, dukungan panjang. |
| Tampilan | Blade + komponen anonim | Tanpa SPA, halaman cepat, mudah dirawat siswa/guru. |
| CSS | CSS tulisan sendiri, disusun per layer, dibundel Vite | Token desain terkontrol penuh, tanpa utility soup, bundle kecil. |
| JS | ES module vanilla, tanpa framework | Interaksi sedikit tapi rapi; tidak perlu Alpine/React. |
| Data Tahap 1 | `config/sekolah.php` + array di controller | Belum butuh database. Konten tetap. |
| Data Tahap 2 | SQLite (dev) / MySQL (produksi) + panel admin | PPDB masuk, berita bisa diperbarui sendiri. |
| Font | Plus Jakarta Sans, self-host, OFL | Huruf buatan Indonesia, sudah diunduh. |
| Warna | Navy `#12233c`, kertas `#f4f3ee`, marigold `#e8a712` | Identitas PGRI (biru) dan kuning logo, bukan palet default AI. |
| Tema | Terang dan gelap, ikut sistem, bisa diganti manual | Bukan tema gelap "karena keren"; dibangun dua-duanya dan diuji. |
| Deploy | Artefak zip untuk hosting PHP 8.4 + MySQL | Realistis untuk sekolah; bukan Vercel (Laravel butuh PHP). |

**Catatan lingkungan (sudah diperiksa).** Mesin ini belum punya PHP/Composer
sistem. Sudah disiapkan PHP 8.4.23 statis di `~/.local/php/php` (lengkap dengan
pdo_sqlite, pdo_mysql, mbstring, openssl, zip). Tanpa sudo. Composer dipasang
sebagai `composer.phar` di folder proyek. Kalau kamu mau rapi jangka panjang,
`sudo pacman -S php composer` tetap disarankan, tapi bukan syarat.

---

## 1. Arah desain (dipakai di kedua tahap)

**Design read:** situs profil sekolah untuk orang tua di Waru, bahasa visual
"buku induk dan cap sekolah", dial **ENERGY 2 / RHYTHM 2 / MOTION 2**.

Alasan tiap keputusan:

- **Warna.** Navy = identitas organisasi payung (PGRI). Marigold = kuning logo,
  dipakai hanya untuk aksi utama, garis penanda, dan cap 1979. Kertas hangat
  netral supaya teks panjang nyaman. Tidak ada gradien, tidak ada glow.
- **Tipografi.** Satu keluarga, Plus Jakarta Sans. Bobot 400 untuk isi, 700/800
  untuk judul. Skala: 12, 14, 16, 18, 22, 28, 36, 48, 62 px (clamp).
- **Bentuk.** Radius 4 px di semua elemen. Tanpa drop shadow. Pemisah pakai
  garis rambut 1 px. Ini yang membuat halaman terasa seperti dokumen, bukan
  template SaaS.
- **Kekhasan yang diingat.** Kartu "Data pokok sekolah" di hero dan cap bundar
  "1979". Dua benda ini tidak ada di situs sekolah lain.
- **Gerak.** Satu urutan masuk di hero, kemunculan bagian saat digulir, dan
  umpan balik saat diklik. Tidak ada animasi berulang tanpa sebab.
  Semua berhenti di `prefers-reduced-motion: reduce`.

Larangan yang dijaga: emoji sebagai ikon, gradien ungu, kartu seragam tanpa
hierarki, badge "AI" di atas judul, angka karangan, testimoni karangan, tautan
mati, tombol tanpa aksi.

---

## 2. Arsitektur Tahap 1 (frontend)

### 2.1 Struktur folder

```
web-smp-pgri-4waru/
├─ app/
│  ├─ Http/Controllers/PageController.php
│  └─ Support/School.php              # akses config + helper format
├─ config/sekolah.php                 # identitas, kontak, fakta, navigasi
├─ resources/
│  ├─ css/app.css                     # tokens, base, komponen, util
│  ├─ js/app.js                       # entry
│  ├─ js/modules/                     # theme, nav, reveal, subjects, wizard, map
│  └─ views/
│     ├─ layouts/app.blade.php
│     ├─ partials/{header,footer,meta,progress}.blade.php
│     ├─ components/{btn,field,registry,figure,facts,band,steps,page-head}.blade.php
│     └─ pages/{beranda,profil,akademik,ppdb,kontak}.blade.php
├─ routes/web.php
├─ lang/id/validation.php             # pesan validasi bahasa Indonesia
└─ docs/RENCANA.md
```

### 2.2 Rute

| Method | URI | Nama | Isi |
|---|---|---|---|
| GET | `/` | beranda | Hero, riwayat singkat, mata pelajaran, PPDB, lokasi |
| GET | `/profil` | profil | Riwayat, identitas, visi misi (draf), pengelolaan |
| GET | `/akademik` | akademik | Tingkat, mata pelajaran, penilaian |
| GET | `/ppdb` | ppdb | Alur, dokumen, wizard pesan pendaftaran |
| GET | `/kontak` | kontak | Alamat, peta, telepon, email, jam layanan |
| GET | `/sitemap.xml` | sitemap | Daftar halaman publik |

Semua rute publik memakai middleware `web` bawaan. Tidak ada rute mati.

### 2.3 Interaksi yang dibangun (dan alasannya)

| Interaksi | Kenapa ada | Detail teknis |
|---|---|---|
| Urutan masuk hero | Menjelaskan urutan baca: nama sekolah, janji, aksi, data | CSS `@keyframes` bertahap 60 ms, sekali jalan, mati saat reduced motion |
| Cap 1979 "mengecap" | Penanda identitas, satu momen saja | transform rotate + scale, 420 ms, sekali |
| Nav menempel + garis bawah | Batas antara navigasi dan isi saat digulir | `IntersectionObserver` pada sentinel, tanpa listener scroll |
| Menu mobile | Wajib di 390 px | Tombol `aria-expanded`, tutup dengan Escape, fokus kembali |
| Pengganti tema | Nyaman malam, diuji dua mode | `data-theme` di `<html>`, disimpan di `localStorage`, skrip anti-kedip di `<head>` |
| Rel kemajuan halaman | Orientasi di halaman panjang | Garis 2 px di kiri, dihitung dengan `IntersectionObserver` per bagian |
| Penjelajah mata pelajaran | 11 item terlalu panjang untuk daftar datar | Chip filter (Semua, Bahasa, Sains, Sosial, Keterampilan), transisi opacity + translate, hasil diumumkan lewat `aria-live` |
| Wizard PPDB 3 langkah | Formulir panjang bikin orang tua berhenti di tengah | Langkah: data siswa → data wali → ringkasan. Validasi per langkah, progres, tombol kembali, ringkasan bisa disalin atau dikirim lewat email |
| Peta dimuat saat diklik | Iframe peta berat dan melacak pengunjung | Tombol "Tampilkan peta" → iframe OSM dimuat; tautan teks selalu tersedia |
| Tombol punya tekanan | Umpan balik klik | `translateY(1px)` saat `:active` |

Yang **tidak** dipakai: parallax, kursor magnetik, marquee, preloader, cursor
custom, scroll-hijack. Semua itu menambah berat tanpa menjawab kebutuhan.

### 2.4 Halaman dan isi

Fakta yang dipakai (sumber: Data Referensi Kemendikdasmen dan profil sekolah
yang dipublikasikan):

- Nama: SMP PGRI 4 Waru · NPSN 20540020 · Swasta
- Naungan: YPLP DASMEN PGRI Jawa Timur
- Berdiri 1979, SK 421.3/2829/404.3.1/1979
- Alamat: Jl. Anggrek No. 109, Kureksari, Kec. Waru, Kab. Sidoarjo 61256
- Telepon (031) 8538447 · email smp.pgri4waru@gmail.com · IG @smppgri4waru
- Luas tanah 660 m² · belajar pagi, 6 hari/minggu · koordinat -7.3619, 112.7327

Tidak dicantumkan: akreditasi (sumber bertentangan: satu menyebut B, satu
menyebut belum terakreditasi), jumlah siswa, daftar nama guru, prestasi, biaya,
jadwal PPDB, testimoni.

Yang ditandai jelas sebagai draf: visi dan misi. Yang ditandai sebagai
keterangan umum: syarat dokumen PPDB dan susunan mata pelajaran. Footer
menyatakan situs ini purwarupa tugas, bukan situs resmi.

### 2.5 Aksesibilitas dan performa (target, bukan harapan)

- Kontras teks minimal 4,5:1 (3:1 untuk teks besar) di mode terang dan gelap.
- Fokus keyboard terlihat di semua elemen interaktif; urutan tab masuk akal.
- Menghormati `prefers-reduced-motion` dan `prefers-color-scheme`.
- Tanpa gulir horizontal di 320, 390, 768, 1024, 1440 px.
- Target: LCP < 2,5 dtk, CLS < 0,1, JS < 40 KB terkompresi, font < 30 KB.
- Tanpa permintaan pihak ketiga sebelum pengguna meminta peta.

---

## 3. Arsitektur Tahap 2 (backend + panel)

### 3.1 Model data

```
users        id, name, email, password, role (admin|operator), timestamps
pendaftar    id, nama_siswa, nama_wali, asal_sd, whatsapp, catatan,
             status (baru|dihubungi|diterima|ditolak), sumber, ip, timestamps,
             soft deletes
posts        id, judul, slug (unik), ringkasan, isi, cover_path,
             status (draf|terbit), published_at, user_id, timestamps
settings     id, key (unik), value, timestamps
```

### 3.2 Rute Tahap 2

| Area | Rute | Aksi |
|---|---|---|
| Publik | `POST /ppdb` | Simpan pendaftaran, throttle 5/menit, honeypot |
| Publik | `GET /berita`, `GET /berita/{slug}` | Daftar dan detail berita terbit |
| Auth | `/login`, `/logout` | Login admin, throttle 5 percobaan |
| Admin | `/admin` | Ringkasan: pendaftar baru, berita draf |
| Admin | `/admin/pendaftar` | Daftar, filter status, cari, detail, ubah status |
| Admin | `/admin/berita` | CRUD berita + unggah gambar |
| Admin | `/admin/pengaturan` | Kontak, jam layanan, teks pengumuman |
| Admin | `GET /admin/ekspor/pendaftar.csv` | Ekspor data PPDB |

### 3.3 Keamanan

- CSRF di semua form, `@csrf` wajib.
- Validasi lewat Form Request, bukan di controller.
- Escaping Blade default; tidak ada `{!! !!}` untuk data pengguna.
- Unggahan: `mimes:jpg,jpeg,png,webp`, maks 3 MB, nama file diacak, disimpan di
  disk `public`, bukan `storage/app/public` mentah.
- Rate limit login dan pendaftaran, honeypot, `throttle` per IP.
- Otorisasi lewat middleware `role:admin` untuk aksi hapus.
- Password bcrypt, `.env` tidak masuk repo, `APP_DEBUG=false` di produksi.
- Soft delete untuk pendaftar supaya data tidak hilang saat salah klik.

### 3.4 Notifikasi dan ekspor

- Setiap pendaftaran baru mengirim email ke alamat sekolah (Mail, driver SMTP;
  di dev pakai `log`).
- Ekspor CSV memakai streaming response, bukan `->get()` penuh.
- Ringkasan harian opsional (dijadwalkan, bisa dimatikan).

---

## 4. Rencana kerja per tahap

### Tahap 1: Frontend (tanpa database)

1. Siapkan runtime: Composer lokal, `composer create-project laravel/laravel`,
   konfigurasi `.env` (SQLite kosong, sesi file), Vite.
2. Pindahkan prototipe HTML statis yang sudah ada ke `.agents/prototype-static/`
   sebagai acuan visual. Bukan dibuang, bukan juga dipakai mentah.
3. Token CSS (`@layer tokens`): warna, tipografi, spasi, radius, garis, tema gelap.
4. Komponen Blade: layout, header, footer, tombol, field, kartu data pokok,
   fakta, band, langkah, kepala halaman.
5. Lima halaman Blade dari `config/sekolah.php`, isi sesuai bagian 2.4.
6. Modul JS: tema, nav, reveal, rel kemajuan, penjelajah mata pelajaran,
   wizard PPDB, peta bertombol.
7. `npm run build`, `php artisan serve`, lalu uji klik dan tangkapan layar
   (390 dan 1440, terang dan gelap).
8. Sambungkan `POST /ppdb` (Tahap 2) tanpa mengubah markup wizard.

**Selesai bila:** lima halaman terbuka tanpa galat konsol, semua interaksi
berjalan, lolos Delivery Gate anti-slop, dan token dipakai konsisten.

### Tahap 2: Backend dan panel

9. Migrasi + seeder (admin, contoh berita draf), factory untuk uji.
10. Form Request + controller PPDB, throttle, honeypot, email notifikasi.
11. Autentikasi admin (Breeze minimal atau guard manual) + middleware role.
12. Panel admin: ringkasan, pendaftar (filter, detail, ubah status, ekspor CSV).
13. CRUD berita + unggah gambar + halaman publik berita.
14. Pengaturan situs yang dipakai header/footer/kontak.
15. Uji fitur (Pest): rute tampil, validasi PPDB, akses admin, unggah file.
16. Paketkan: `composer install --no-dev`, `npm run build`, zip, panduan pasang
    di hosting (MySQL, `public` sebagai docroot, `storage:link`, cron).

**Selesai bila:** pendaftaran dari wizard masuk ke database dan terlihat di
panel, email terkirim (atau tercatat di log), tes hijau, dan panduan pasang
sudah diuji di salinan bersih.

---

## 5. Verifikasi (bukti, bukan klaim)

| Tahap | Perintah / langkah | Bukti yang ditunjukkan |
|---|---|---|
| 1 | `npm run build` | keluaran build + ukuran berkas |
| 1 | `php artisan serve` + Chromium | tangkapan layar 390/1440 px, terang/gelap |
| 1 | uji klik manual | daftar: menu, tema, filter mapel, wizard (kosong, salah, benar), peta, semua tautan |
| 1 | audit aksesibilitas | kontras terukur, tab sampai habis, `aria-*` benar |
| 2 | `php artisan test` | ringkasan tes lulus |
| 2 | uji alur PPDB | baris database baru + email di log + tampil di panel |
| 2 | uji otorisasi | tamu ditolak 302, operator tidak bisa hapus (403) |

---

## 6. Risiko dan penanganan

| Risiko | Dampak | Penanganan |
|---|---|---|
| PHP tidak ada di sistem | Tidak bisa menjalankan Laravel | PHP statis 8.4 sudah siap; `sudo pacman -S php composer` sebagai jalur rapi |
| Isi resmi sekolah belum ada (visi, misi, jadwal, biaya) | Situs terasa kosong atau malah mengarang | Draf ditandai terlihat, angka resmi tidak dicantumkan, panel admin disiapkan untuk diisi |
| Tidak ada foto sekolah | Halaman terasa seperti dokumen | Desain memang dokumen; Tahap 2 menyediakan unggah foto berita. Foto asli diminta ke sekolah, bukan dibuat-buat |
| Peta pihak ketiga | Berat dan melacak | Peta dimuat setelah diklik, tautan teks selalu ada |
| Email sekolah tidak aktif | Notifikasi gagal | Driver `log` di dev, status kirim dicatat, data tetap tersimpan |
| Hosting sekolah hanya cPanel | Deploy gagal | Panduan khusus cPanel: docroot ke `public/`, MySQL, cron scheduler |

---

## 7. Yang kamu putuskan sebelum mulai

Hanya dua hal, jawabannya boleh singkat:

1. **Nama domain/hosting** kalau sudah ada. Kalau belum, saya siapkan build yang
   bisa dijalankan lokal dan zip siap unggah.
2. **Perlu halaman berita di Tahap 2?** Kalau tidak, Tahap 2 cukup PPDB + panel
   pendaftar, dan pekerjaan lebih cepat.

Kalau tidak dijawab, saya pakai default: belum ada domain, berita ikut dibuat.

---

## 8. Skill yang dipakai menyusun rencana ini

`brainstorm` (gerbang P0), `antislop` + `antislop-ui` + `antislop-copywriting`
(filter), `design-taste-frontend-v2-experimental` (dial dan aturan layout),
`frontend-design` (arah visual, tipografi, copy), `verification-before-completion`
(protokol bukti). Tahap 1 nanti menambah `web-design-guidelines`, `impeccable`,
`a11y-audit`, dan `better-icons` bila ikon memang dibutuhkan.
