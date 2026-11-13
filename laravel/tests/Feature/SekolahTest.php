<?php

namespace Tests\Feature;

use App\Mail\PendaftarBaruMail;
use App\Models\AlumniReview;
use App\Models\Galeri;
use App\Models\Pendaftar;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SekolahTest extends TestCase
{
    use RefreshDatabase;

    public function test_lima_halaman_publik_dan_berita_terbuka_dengan_status_200(): void
    {
        $post = Post::factory()->create([
            'judul' => 'Pengumuman Kegiatan Pagi',
            'slug' => 'pengumuman-kegiatan-pagi',
            'status' => 'terbit',
        ]);

        $this->get('/')->assertOk()
            ->assertSee('SMP PGRI 4 Waru')
            ->assertSee('/images/logo-smp-pgri4-waru.png', false)
            ->assertSee('id="photo-lightbox"', false)
            ->assertSee('class="mobile-quickbar"', false);
        $this->get('/profil')->assertOk()->assertSee('20540020');
        $this->get('/akademik')->assertOk()
            ->assertSee('Pendidikan Pancasila')
            ->assertSee('data-subject-search', false);
        $this->get('/ppdb')->assertOk()
            ->assertSee('Penerimaan siswa baru')
            ->assertSee('id="ppdb-doc-checklist"', false);
        $this->get('/kontak')->assertOk()->assertSee('Jl. Anggrek No. 109');
        $this->get('/berita')->assertOk()
            ->assertSee('Pengumuman Kegiatan Pagi')
            ->assertSee('data-news-search', false);
        $this->get('/berita/'.$post->slug)->assertOk()->assertSee('Pengumuman Kegiatan Pagi');
        $this->get('/alumni')->assertOk()
            ->assertSee('Suara & ulasan')
            ->assertSee('id="alumni-live-preview"', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee('pengumuman-kegiatan-pagi');
    }

    public function test_ppdb_post_menyimpan_data_dan_mengirim_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/ppdb', [
            'siswa' => 'Raka Pratama',
            'asal' => 'SDN Kureksari 1',
            'wali' => 'Budi Santoso',
            'wa' => '0812-3456-7890',
            'catatan' => 'Mohon info jadwal daftar ulang.',
            'website_url' => '',
        ]);

        $response->assertCreated()
            ->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('pendaftar', [
            'nama_siswa' => 'Raka Pratama',
            'asal_sd' => 'SDN Kureksari 1',
            'nama_wali' => 'Budi Santoso',
            'whatsapp' => '081234567890',
            'status' => 'baru',
        ]);

        Mail::assertSent(PendaftarBaruMail::class, function (PendaftarBaruMail $mail): bool {
            return $mail->pendaftar->nama_siswa === 'Raka Pratama';
        });
    }

    public function test_ppdb_post_menolak_data_tidak_valid_dan_honeypot(): void
    {
        $this->postJson('/ppdb', [
            'siswa' => '',
            'asal' => '',
            'wali' => '',
            'wa' => '123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['nama_siswa', 'asal_sd', 'nama_wali', 'whatsapp']);

        $this->postJson('/ppdb', [
            'siswa' => 'Bot Spam',
            'asal' => 'SDN Spam',
            'wali' => 'Wali Spam',
            'wa' => '081234567890',
            'website_url' => 'https://spam.example.com',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['website_url']);
    }

    public function test_seluruh_halaman_panel_admin_wajib_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/ppdb')->assertRedirect('/login');
        $this->get('/admin/berita')->assertRedirect('/login');
        $this->get('/admin/galeri')->assertRedirect('/login');
        $this->get('/admin/alumni')->assertRedirect('/login');
        $this->get('/admin/akademik')->assertRedirect('/login');
        $this->get('/admin/petugas')->assertRedirect('/login');
        $this->get('/admin/pengaturan')->assertRedirect('/login');
        $this->get('/admin/ekspor/pendaftar.csv')->assertRedirect('/login');
    }

    public function test_halaman_admin_terpisah_dan_rbac_operator_vs_admin(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $admin = User::factory()->admin()->create();
        $pendaftar = Pendaftar::factory()->create(['nama_siswa' => 'Siti Aminah']);

        // Operator bisa membuka semua sub-halaman admin
        $this->actingAs($operator)->get('/admin')->assertOk()->assertSee('Akses Cepat Modul');
        $this->actingAs($operator)->get('/admin/ppdb')->assertOk()->assertSee('Siti Aminah');
        $this->actingAs($operator)->get('/admin/berita')->assertOk()->assertSee('Berita & Pengumuman');
        $this->actingAs($operator)->get('/admin/akademik')->assertOk()->assertSee('Struktur Mata Pelajaran SMP');
        $this->actingAs($operator)->get('/admin/petugas')->assertOk()->assertSee('Daftar Akun Terdaftar');
        $this->actingAs($operator)->get('/admin/pengaturan')->assertOk()->assertSee('Kustomisasi Penuh Situs');

        $this->actingAs($operator)
            ->patch("/admin/pendaftar/{$pendaftar->id}/status", ['status' => 'diterima'])
            ->assertRedirect('/admin/ppdb');

        $this->assertDatabaseHas('pendaftar', [
            'id' => $pendaftar->id,
            'status' => 'diterima',
        ]);

        $this->actingAs($operator)
            ->get('/admin/ekspor/pendaftar.csv')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->actingAs($operator)
            ->delete("/admin/pendaftar/{$pendaftar->id}")
            ->assertForbidden();

        // Admin bisa menghapus (soft delete)
        $this->actingAs($admin)
            ->delete("/admin/pendaftar/{$pendaftar->id}")
            ->assertRedirect('/admin/ppdb');

        $this->assertSoftDeleted('pendaftar', ['id' => $pendaftar->id]);
    }

    public function test_admin_dapat_mengelola_berita_dan_kustomisasi_penuh_serta_unggah_berkas(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/berita', [
                'judul' => 'Jadwal Kerja Bakti Sekolah',
                'ringkasan' => 'Kegiatan hari Sabtu pagi.',
                'isi' => 'Seluruh siswa membawa alat kebersihan masing-masing.',
                'status' => 'terbit',
                'cover' => UploadedFile::fake()->create('sampul.jpg', 120, 'image/jpeg'),
            ])
            ->assertRedirect('/admin/berita');

        $post = Post::query()->where('judul', 'Jadwal Kerja Bakti Sekolah')->firstOrFail();
        $this->assertSame('terbit', $post->status);
        $this->assertNotNull($post->cover_path);
        Storage::disk('public')->assertExists($post->cover_path);

        // Toggle status berita
        $this->actingAs($admin)
            ->patch("/admin/berita/{$post->id}/status")
            ->assertRedirect('/admin/berita');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 'draf',
        ]);

        // Kustomisasi penuh situs + unggah logo, hero, dan brosur
        $this->actingAs($admin)
            ->post('/admin/pengaturan', [
                'nama_sekolah' => 'SMP PGRI 4 Waru Sidoarjo',
                'tagline' => 'Kureksari, Waru',
                'telepon' => '(031) 8538447',
                'email' => 'smp.pgri4waru@gmail.com',
                'jam_layanan' => '07.00 - 13.00 WIB',
                'pengumuman_ppdb' => 'Bawa map merah untuk berkas.',
                'logo_sekolah' => UploadedFile::fake()->create('logo.png', 80, 'image/png'),
                'hero_image' => UploadedFile::fake()->create('hero.jpg', 200, 'image/jpeg'),
                'brosur_ppdb' => UploadedFile::fake()->create('brosur.pdf', 300, 'application/pdf'),
            ])
            ->assertRedirect('/admin/pengaturan');

        $this->assertDatabaseHas('settings', [
            'key' => 'nama_sekolah',
            'value' => 'SMP PGRI 4 Waru Sidoarjo',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'jam_layanan',
            'value' => '07.00 - 13.00 WIB',
        ]);

        // Tambah petugas baru
        $this->actingAs($admin)
            ->post('/admin/petugas', [
                'name' => 'Operator Dua',
                'email' => 'op2@smppgri4waru.sch.id',
                'role' => 'operator',
                'password' => 'Rahasia123',
            ])
            ->assertRedirect('/admin/petugas');

        $this->assertDatabaseHas('users', [
            'email' => 'op2@smppgri4waru.sch.id',
            'role' => 'operator',
        ]);
    }

    public function test_publik_dapat_mengirim_ulasan_alumni_dan_admin_mengelola_galeri_serta_alumni(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        // Alumni mengirim ulasan dari halaman publik /alumni
        $this->post('/alumni', [
            'nama' => 'Nadia Putri',
            'angkatan' => '2021',
            'pekerjaan' => 'SMAN 1 Waru',
            'ulasan' => 'Senang sekali pernah belajar di SMP PGRI 4 Waru, gurunya sangat perhatian.',
            'website_url' => '',
        ])->assertRedirect('/alumni');

        $review = AlumniReview::query()->where('nama', 'Nadia Putri')->firstOrFail();
        $this->assertSame('menunggu', $review->status);

        // Sebelum disetujui, belum tampil di halaman publik
        $this->get('/alumni')->assertOk()->assertDontSee('Nadia Putri');

        // Admin menyetujui (terbitkan) ulasan alumni
        $this->actingAs($admin)
            ->patch("/admin/alumni/{$review->id}/status")
            ->assertRedirect('/admin/alumni');

        $this->assertDatabaseHas('alumni_reviews', [
            'id' => $review->id,
            'status' => 'terbit',
        ]);

        // Setelah diterbitkan, tampil di /alumni dan Beranda /
        $this->get('/alumni')->assertOk()->assertSee('Nadia Putri');
        $this->get('/')->assertOk()->assertSee('Nadia Putri');

        // Admin menambahkan foto galeri kegiatan baru
        $this->actingAs($admin)
            ->post('/admin/galeri', [
                'judul' => 'Upacara Hari Senin di Halaman Sekolah',
                'kategori' => 'Kegiatan Sekolah',
                'keterangan' => 'Diikuti seluruh siswa kelas VII, VIII, dan IX.',
                'tampil_beranda' => '1',
                'foto' => UploadedFile::fake()->create('upacara.jpg', 150, 'image/jpeg'),
            ])
            ->assertRedirect('/admin/galeri');

        $foto = Galeri::query()->where('judul', 'Upacara Hari Senin di Halaman Sekolah')->firstOrFail();
        Storage::disk('public')->assertExists($foto->image_path);

        // Foto tampil di Beranda dan halaman Berita
        $this->get('/')->assertOk()->assertSee('Upacara Hari Senin di Halaman Sekolah');
        $this->get('/berita')->assertOk()->assertSee('Upacara Hari Senin di Halaman Sekolah');
    }

    public function test_studio_berita_blogger_menyimpan_html_kaya_preset_sampul_dan_merender_aman(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/berita', [
                'judul' => 'Liputan Praktikum Informatika Siswa',
                'slug' => 'liputan-praktikum-informatika-siswa',
                'ringkasan' => '',
                'isi' => '<h2>Tujuan Kegiatan</h2><p>Siswa kelas VIII melaksanakan <b>praktikum jaringan</b> di laboratorium.</p><blockquote>Belajar aktif dan kolaboratif setiap hari.</blockquote><script>alert(1)</script>',
                'status' => 'terbit',
                'cover_preset' => '/images/sekolah-multimedia.jpg',
            ])
            ->assertRedirect('/admin/berita');

        $post = Post::query()->where('slug', 'liputan-praktikum-informatika-siswa')->firstOrFail();
        $this->assertSame('/images/sekolah-multimedia.jpg', $post->cover_path);
        $this->assertStringNotContainsString('<h2>', $post->excerpt());
        $this->assertStringContainsString('Tujuan Kegiatan Siswa kelas VIII', $post->excerpt());

        // Halaman baca berita menampilkan blok HTML kaya tetapi membuang tag script berbahaya
        $this->get('/berita/liputan-praktikum-informatika-siswa')
            ->assertOk()
            ->assertSee('<h2>Tujuan Kegiatan</h2>', false)
            ->assertSee('<b>praktikum jaringan</b>', false)
            ->assertSee('<blockquote>Belajar aktif dan kolaboratif setiap hari.</blockquote>', false)
            ->assertDontSee('<script>alert(1)</script>', false);

        // Mode sunting menampilkan artikel yang dipilih
        $this->actingAs($admin)
            ->get('/admin/berita?edit='.$post->id)
            ->assertOk()
            ->assertSee('Perbarui Artikel')
            ->assertSee('Liputan Praktikum Informatika Siswa');
    }
}
