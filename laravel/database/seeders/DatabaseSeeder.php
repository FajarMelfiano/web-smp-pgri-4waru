<?php

namespace Database\Seeders;

use App\Models\AlumniReview;
use App\Models\Galeri;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@smppgri4waru.id'],
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'operator@smppgri4waru.id'],
            [
                'name' => 'Operator Tata Usaha',
                'password' => Hash::make('password123'),
                'role' => 'operator',
            ]
        );

        Post::query()->updateOrCreate(
            ['slug' => 'informasi-awal-tahun-ajaran-smp-pgri-4-waru'],
            [
                'judul' => 'Informasi Awal Tahun Ajaran SMP PGRI 4 Waru',
                'ringkasan' => 'Kegiatan belajar mengajar di SMP PGRI 4 Waru berlangsung pagi hari selama enam hari kerja di Jl. Anggrek No. 109, Kureksari.',
                'isi' => "SMP PGRI 4 Waru menyelenggarakan kegiatan belajar mengajar pada pagi hari selama enam hari dalam seminggu (Senin sampai Sabtu) di Jl. Anggrek No. 109, Desa Kureksari, Kecamatan Waru, Kabupaten Sidoarjo.\n\nBagi orang tua atau wali calon peserta didik yang membutuhkan informasi pendaftaran maupun kelengkapan berkas administrasi, silakan menghubungi kantor sekolah melalui telepon (031) 8538447 pada jam kerja.",
                'cover_path' => '/images/sekolah-gedung.jpg',
                'status' => 'terbit',
                'published_at' => now()->subDays(5),
                'user_id' => $admin->id,
            ]
        );

        Post::query()->updateOrCreate(
            ['slug' => 'layanan-pendaftaran-peserta-didik-baru-kureksari'],
            [
                'judul' => 'Layanan Pendaftaran Peserta Didik Baru (PPDB) di Kantor Sekolah',
                'ringkasan' => 'Calon wali murid dapat mendaftarkan putra-putrinya secara daring melalui situs ini atau datang langsung ke kantor tata usaha.',
                'isi' => "Panitia Penerimaan Peserta Didik Baru (PPDB) SMP PGRI 4 Waru melayani pendaftaran calon siswa lulusan SD/MI di kawasan Kureksari, Waru, dan sekitarnya.\n\nOrang tua/wali dapat mengisi formulir pendaftaran awal melalui halaman PPDB pada situs ini, kemudian menyerahkan berkas Kartu Keluarga, Akta Kelahiran, dan Surat Keterangan Lulus di kantor sekolah Jl. Anggrek No. 109.",
                'cover_path' => '/images/sekolah-kelas-pagi.jpg',
                'status' => 'terbit',
                'published_at' => now()->subDays(2),
                'user_id' => $admin->id,
            ]
        );

        Post::query()->updateOrCreate(
            ['slug' => 'penguatan-kurikulum-dan-pembelajaran-informatika'],
            [
                'judul' => 'Pelaksanaan 11 Mata Pelajaran Wajib & Literasi Informatika',
                'ringkasan' => 'Struktur kurikulum SMP mencakup kelompok sains, bahasa, sosial, keterampilan, serta mata pelajaran Informatika.',
                'isi' => "SMP PGRI 4 Waru melaksanakan 11 mata pelajaran struktur kurikulum jenjang Sekolah Menengah Pertama, mulai dari Pendidikan Agama, Pendidikan Pancasila, Bahasa Indonesia, Matematika, IPA, IPS, Bahasa Inggris, PJOK, Seni Budaya, Prakarya, hingga Informatika.\n\nPembelajaran dirancang tertib setiap pagi agar siswa memiliki fondasi akademik dan karakter yang kuat untuk melanjutkan ke jenjang SMA maupun SMK.",
                'cover_path' => '/images/sekolah-multimedia.jpg',
                'status' => 'terbit',
                'published_at' => now()->subDay(),
                'user_id' => $admin->id,
            ]
        );

        Galeri::query()->updateOrCreate(
            ['judul' => 'Lingkungan Kampus Jl. Anggrek No. 109 Kureksari'],
            [
                'kategori' => 'Fasilitas & Lingkungan',
                'keterangan' => 'Lokasi belajar SMP PGRI 4 Waru di Desa Kureksari, Kecamatan Waru, Kabupaten Sidoarjo yang berdiri sejak 1979.',
                'image_path' => '/images/sekolah-gedung.jpg',
                'tampil_beranda' => true,
            ]
        );

        Galeri::query()->updateOrCreate(
            ['judul' => 'Suasana Kegiatan Belajar Pagi (Senin–Sabtu)'],
            [
                'kategori' => 'Pembelajaran Kelas',
                'keterangan' => 'Kegiatan belajar mengajar berlangsung tertib setiap pagi hari selama enam hari kerja dalam sepekan.',
                'image_path' => '/images/sekolah-kelas-pagi.jpg',
                'tampil_beranda' => true,
            ]
        );

        Galeri::query()->updateOrCreate(
            ['judul' => 'Pembelajaran Kurikulum SMP & Literasi Digital'],
            [
                'kategori' => 'Kegiatan Sekolah',
                'keterangan' => 'Pendampingan 11 mata pelajaran nasional termasuk Informatika dan Prakarya bagi peserta didik.',
                'image_path' => '/images/sekolah-multimedia.jpg',
                'tampil_beranda' => true,
            ]
        );

        Galeri::query()->updateOrCreate(
            ['judul' => 'Upacara Bendera Hari Senin di Halaman Sekolah'],
            [
                'kategori' => 'Kegiatan Sekolah',
                'keterangan' => 'Pembinaan kedisiplinan dan wawasan kebangsaan peserta didik melalui upacara bendera rutin setiap Senin pagi.',
                'image_path' => '/images/sekolah-upacara.jpg',
                'tampil_beranda' => true,
            ]
        );

        Galeri::query()->updateOrCreate(
            ['judul' => 'Praktikum Ilmu Pengetahuan Alam (IPA) Bersama Guru'],
            [
                'kategori' => 'Pembelajaran Kelas',
                'keterangan' => 'Siswa melakukan pengamatan dan percobaan kelompok pada mata pelajaran Ilmu Pengetahuan Alam.',
                'image_path' => '/images/sekolah-praktikum-ipa.jpg',
                'tampil_beranda' => true,
            ]
        );

        Galeri::query()->updateOrCreate(
            ['judul' => 'Kegiatan Pramuka & Pembinaan Karakter Siswa'],
            [
                'kategori' => 'Ekstrakurikuler',
                'keterangan' => 'Latihan kepramukaan untuk melatih kemandirian, kerja sama regu, dan kepemimpinan siswa SMP.',
                'image_path' => '/images/sekolah-pramuka.jpg',
                'tampil_beranda' => true,
            ]
        );

        Galeri::query()->updateOrCreate(
            ['judul' => 'Ruang Baca & Literasi Perpustakaan Sekolah'],
            [
                'kategori' => 'Fasilitas & Lingkungan',
                'keterangan' => 'Sudut baca dan koleksi buku penunjang pelajaran untuk menumbuhkan minat baca siswa.',
                'image_path' => '/images/sekolah-perpustakaan.jpg',
                'tampil_beranda' => false,
            ]
        );

        Galeri::query()->updateOrCreate(
            ['judul' => 'Diskusi Kelompok & Pendampingan Wali Kelas'],
            [
                'kategori' => 'Pembelajaran Kelas',
                'keterangan' => 'Pendampingan belajar terarah dari kelas VII hingga persiapan akhir kelas IX.',
                'image_path' => '/images/sekolah-diskusi-kelas.jpg',
                'tampil_beranda' => false,
            ]
        );

        AlumniReview::query()->updateOrCreate(
            ['nama' => 'Rizky Pratama', 'angkatan' => '2019'],
            [
                'pekerjaan' => 'Alumni Kureksari · Lulusan Perguruan Tinggi di Surabaya',
                'ulasan' => 'Bapak dan Ibu guru di SMP PGRI 4 Waru sangat sabar membimbing kami dari kelas VII sampai lulus. Lingkungan sekolahnya dekat dengan warga Kureksari dan membuat kami disiplin bangun pagi.',
                'foto_path' => '/images/alumni-rizky.jpg',
                'status' => 'terbit',
            ]
        );

        AlumniReview::query()->updateOrCreate(
            ['nama' => 'Siti Nurhaliza', 'angkatan' => '2020'],
            [
                'pekerjaan' => 'Mahasiswi Perguruan Tinggi di Surabaya',
                'ulasan' => 'Banyak kenangan berharga selama tiga tahun belajar di Jalan Anggrek No. 109. Teman-teman dan gurunya kekeluargaan sekali, sangat membantu saya menyiapkan diri masuk SMA.',
                'foto_path' => '/images/alumni-siti.jpg',
                'status' => 'terbit',
            ]
        );

        AlumniReview::query()->updateOrCreate(
            ['nama' => 'Dimas Wahyu Saputra', 'angkatan' => '2022'],
            [
                'pekerjaan' => 'Pelajar SMA di Waru, Sidoarjo',
                'ulasan' => 'Jadwal belajar pagi enam hari membuat kebiasaan belajar jadi teratur. Terima kasih untuk seluruh guru SMP PGRI 4 Waru atas ilmu dan nasihatnya.',
                'foto_path' => '/images/alumni-dimas.jpg',
                'status' => 'terbit',
            ]
        );

        AlumniReview::query()->updateOrCreate(
            ['nama' => 'Putri Rahmawati', 'angkatan' => '2021'],
            [
                'pekerjaan' => 'Lulusan SMK Negeri di Sidoarjo',
                'ulasan' => 'Pelajaran IPA dan Informatikanya sangat membantu waktu saya lanjut ke SMK. Guru-gurunya ramah dan selalu terbuka kalau kami bertanya di luar jam pelajaran.',
                'foto_path' => '/images/alumni-putri.jpg',
                'status' => 'terbit',
            ]
        );

        AlumniReview::query()->updateOrCreate(
            ['nama' => 'Bagus Setiawan', 'angkatan' => '2018'],
            [
                'pekerjaan' => 'Wiraswasta Muda di Waru, Sidoarjo',
                'ulasan' => 'Dari kegiatan Pramuka dan kerja kelompok di SMP PGRI 4 Waru, saya belajar disiplin dan tanggung jawab. Bangga pernah jadi bagian dari sekolah ini.',
                'foto_path' => '/images/alumni-bagus.jpg',
                'status' => 'terbit',
            ]
        );

        AlumniReview::query()->updateOrCreate(
            ['nama' => 'Fajar Adi Nugroho', 'angkatan' => '2017'],
            [
                'pekerjaan' => 'Karyawan Swasta di Surabaya',
                'ulasan' => 'Sekolah yang hangat dan penuh kenangan di Kureksari. Nilai kesopanan dan ketertiban yang diajarkan bapak ibu guru masih terasa manfaatnya sampai sekarang di dunia kerja.',
                'foto_path' => '/images/alumni-fajar.jpg',
                'status' => 'terbit',
            ]
        );

        $savedSettings = [
            'nama_sekolah' => 'SMP PGRI 4 Waru',
            'tagline' => 'Kureksari, Sidoarjo',
            'npsn' => '20540020',
            'naungan' => 'YPLP DASMEN PGRI Jawa Timur',
            'sk_pendirian' => '421.3/2829/404.3.1/1979',
            'tahun_berdiri' => '1979',
            'luas_tanah' => '660 m²',
            'waktu_belajar' => 'Pagi hari, enam hari dalam seminggu',
            'telepon' => '(031) 8538447',
            'email' => 'smp.pgri4waru@gmail.com',
            'instagram' => '@smppgri4waru',
            'alamat' => 'Jl. Anggrek No. 109, Kureksari, Kec. Waru, Kab. Sidoarjo',
            'kode_pos' => '61256',
            'koordinat' => '-7.3619, 112.7327',
            'jam_layanan' => 'Pagi hari, enam hari dalam seminggu (Senin–Sabtu)',
            'hero_eyebrow' => 'Selamat datang di SMP PGRI 4 Waru',
            'hero_judul' => 'Sekolah menengah pertama untuk keluarga Kureksari sejak 1979.',
            'hero_deskripsi' => 'Sekolah swasta di bawah YPLP DASMEN PGRI Jawa Timur. Alamatnya tidak pernah pindah dari Jalan Anggrek No. 109, dan kegiatan belajar berlangsung pagi hari, enam hari dalam seminggu.',
            'visi_sekolah' => 'Menjadi sekolah menengah pertama yang dekat dengan warganya, tempat siswa Kureksari belajar dengan tertib, percaya diri, dan siap melanjutkan pendidikan.',
            'pengumuman_ppdb' => 'Pendaftaran dilakukan langsung di kantor sekolah Jl. Anggrek No. 109, Kureksari.',
            'status_ppdb' => 'Dibuka: Layanan Kantor Sekolah',
            'tahun_ajaran' => '2026/2027',
            'logo_path' => 'site/logo_sekolah-655024df-93c6-49ea-8c84-3185a58193df.png',
            'hero_image_path' => 'site/gedung-smp-pgri4-waru.webp',
        ];

        foreach ($savedSettings as $key => $value) {
            Setting::setValue($key, $value);
        }
    }
}
