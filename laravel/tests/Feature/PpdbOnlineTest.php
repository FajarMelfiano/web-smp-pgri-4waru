<?php

namespace Tests\Feature;

use App\Models\PpdbAccount;
use App\Models\PpdbBerkas;
use App\Models\PpdbJalur;
use App\Models\PpdbPembayaran;
use App\Models\PpdbPendaftaran;
use App\Models\User;
use Database\Seeders\PpdbJalurSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PpdbOnlineTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_daftar_dan_masuk_ppdb_terbuka_dan_portal_wajib_login_siswa(): void
    {
        $this->seed(PpdbJalurSeeder::class);

        $this->get('/ppdb/daftar')
            ->assertOk()
            ->assertSee('Buat Akun PPDB')
            ->assertSee('Panjang 8 - 16 karakter');

        $this->get('/ppdb/masuk')
            ->assertOk()
            ->assertSee('Masuk Portal PPDB');

        $this->get('/ppdb/portal')
            ->assertRedirect('/ppdb/masuk');
    }

    public function test_registrasi_akun_ppdb_memvalidasi_aturan_kata_sandi_nik_dan_honeypot(): void
    {
        // Kata sandi tanpa huruf besar / angka & NIK kurang dari 16 digit
        $this->post('/ppdb/daftar', [
            'email' => 'calon@siswa.id',
            'password' => 'semuankecil',
            'password_confirmation' => 'semuankecil',
            'nama_lengkap' => 'Rizky Pratama',
            'tanggal_lahir' => '2013-05-14',
            'whatsapp' => '81234567890',
            'nik' => '3515',
            'website_url' => '',
        ])->assertSessionHasErrors(['password', 'nik']);

        // Honeypot terisi
        $this->post('/ppdb/daftar', [
            'email' => 'bot@siswa.id',
            'password' => 'SandiAman123',
            'password_confirmation' => 'SandiAman123',
            'nama_lengkap' => 'Bot Spam',
            'tanggal_lahir' => '2013-05-14',
            'whatsapp' => '81234567890',
            'nik' => '3515181405130001',
            'website_url' => 'https://spam.example.com',
        ])->assertSessionHasErrors(['website_url']);
    }

    public function test_alur_lengkap_siswa_jalur_gratis_sampai_cetak_kartu_peserta_dan_login_via_nik(): void
    {
        Storage::fake('local');
        $this->seed(PpdbJalurSeeder::class);

        $jalurGratis = PpdbJalur::query()->where('biaya_daftar', 0)->firstOrFail();

        // 1. Registrasi akun 2 langkah
        $response = $this->post('/ppdb/daftar', [
            'email' => 'alya.putri@gmail.com',
            'password' => 'SmpPgri42026',
            'password_confirmation' => 'SmpPgri42026',
            'nama_lengkap' => 'Alya Putri Ramadhani',
            'tanggal_lahir' => '2013-08-17',
            'whatsapp' => '0812-3456-7891',
            'nik' => '3515185708130002',
            'website_url' => '',
        ]);

        $response->assertRedirect('/ppdb/portal');
        $this->assertAuthenticated('ppdb');

        $account = PpdbAccount::query()->where('email', 'alya.putri@gmail.com')->firstOrFail();
        $this->assertSame('6281234567891', $account->whatsapp);

        $pendaftaran = PpdbPendaftaran::query()->where('ppdb_account_id', $account->id)->firstOrFail();
        $this->assertStringStartsWith('PPDB-', $pendaftaran->no_pendaftaran);

        // Step-lock: Sebelum pilih jalur, akses ke Data Diri dialihkan ke Pemilihan Jalur
        $this->get('/ppdb/portal/data-diri')
            ->assertRedirect('/ppdb/portal/jalur');

        // 2. Pilih jalur gratis -> otomatis lunas & diarahkan ke Data Diri
        $this->post('/ppdb/portal/jalur', [
            'ppdb_jalur_id' => $jalurGratis->id,
        ])->assertRedirect('/ppdb/portal/data-diri');

        $pendaftaran->refresh();
        $this->assertSame($jalurGratis->id, $pendaftaran->ppdb_jalur_id);
        $this->assertSame('lunas', $pendaftaran->pembayaran?->status);

        // Step-lock: Sebelum isi Data Diri, akses ke Berkas dialihkan ke Data Diri
        $this->get('/ppdb/portal/berkas')
            ->assertRedirect('/ppdb/portal/data-diri');

        // 3. Isi Data Diri & Orang Tua
        $this->post('/ppdb/portal/data-diri', [
            'nisn' => '0134567890',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Sidoarjo',
            'agama' => 'Islam',
            'anak_ke' => 1,
            'jumlah_saudara' => 2,
            'alamat_lengkap' => 'Jl. Anggrek II No. 15 RT 02 RW 01',
            'kelurahan_desa' => 'Kureksari',
            'kecamatan' => 'Waru',
            'kabupaten_kota' => 'Sidoarjo',
            'kode_pos' => '61256',
            'asal_sd' => 'SDN Kureksari 1',
            'npsn_sd' => '20501234',
            'tahun_lulus_sd' => 2026,
            'nama_ayah' => 'Hendra Wijaya',
            'pekerjaan_ayah' => 'Karyawan Swasta',
            'no_hp_ayah' => '081234567800',
            'nama_ibu' => 'Rina Susanti',
            'pekerjaan_ibu' => 'Ibu Rumah Tangga',
            'no_hp_ibu' => '081234567801',
            'penghasilan_ortu' => 'Rp 2.000.000 - Rp 4.000.000',
        ])->assertRedirect('/ppdb/portal/berkas');

        // Step-lock: Sebelum unggah 4 berkas wajib, akses ke Kuesioner dialihkan ke Berkas
        $this->get('/ppdb/portal/kuesioner')
            ->assertRedirect('/ppdb/portal/berkas');

        // 4. Unggah 4 Berkas Wajib (KK, Akta, SKL, Foto)
        foreach (['kk', 'akta', 'skl', 'foto'] as $jenis) {
            $ext = $jenis === 'foto' ? 'jpg' : 'pdf';
            $mime = $jenis === 'foto' ? 'image/jpeg' : 'application/pdf';

            $this->post('/ppdb/portal/berkas', [
                'jenis' => $jenis,
                'file' => UploadedFile::fake()->create("dokumen-{$jenis}.{$ext}", 180, $mime),
            ])->assertRedirect('/ppdb/portal/berkas');
        }

        $this->assertSame(4, PpdbBerkas::query()->where('ppdb_pendaftaran_id', $pendaftaran->id)->count());

        // 5. Isi Kuesioner
        $this->post('/ppdb/portal/kuesioner', [
            'sumber_info' => 'Guru / Sekolah Asal (SD/MI)',
            'alasan_memilih' => 'Lokasi dekat rumah dan lingkungan sekolah tertib.',
            'minat_ekstrakurikuler' => ['Pramuka', 'Futsal / Olahraga'],
            'saran' => 'Semoga diterima di kelas VII.',
            'pernyataan_setuju' => '1',
        ])->assertRedirect('/ppdb/portal/kartu-peserta');

        // 6. Buka Kartu Peserta & Pengumuman
        $this->get('/ppdb/portal/kartu-peserta')
            ->assertOk()
            ->assertSee('Kartu Tanda Peserta PPDB')
            ->assertSee('Alya Putri Ramadhani')
            ->assertSee($pendaftaran->no_pendaftaran);

        $this->get('/ppdb/portal/pengumuman')
            ->assertOk()
            ->assertSee('Dalam Proses Verifikasi');

        // 7. Keluar lalu masuk kembali menggunakan NIK 16 digit
        $this->post('/ppdb/keluar')->assertRedirect('/ppdb/masuk');
        $this->assertGuest('ppdb');

        $this->post('/ppdb/masuk', [
            'identitas' => '3515185708130002',
            'password' => 'SmpPgri42026',
        ])->assertRedirect('/ppdb/portal');

        $this->assertAuthenticatedAs($account, 'ppdb');
    }

    public function test_jalur_berbayar_dan_verifikasi_panitia_admin_serta_proteksi_file_privat(): void
    {
        Storage::fake('local');
        $this->seed(PpdbJalurSeeder::class);

        $admin = User::factory()->admin()->create();
        $jalurBerbayar = PpdbJalur::query()->where('biaya_daftar', '>', 0)->firstOrFail();

        $account1 = PpdbAccount::factory()->create([
            'nama_lengkap' => 'Bagas Adi Saputra',
            'nik' => '3515180101130009',
        ]);
        $pendaftaran1 = PpdbPendaftaran::factory()->create([
            'ppdb_account_id' => $account1->id,
            'ppdb_jalur_id' => null,
        ]);

        // Siswa 1 memilih jalur berbayar -> diarahkan ke halaman Pembayaran
        $this->actingAs($account1, 'ppdb')
            ->post('/ppdb/portal/jalur', [
                'ppdb_jalur_id' => $jalurBerbayar->id,
            ])
            ->assertRedirect('/ppdb/portal/pembayaran');

        // Siswa 1 mengunggah bukti transfer
        $this->actingAs($account1, 'ppdb')
            ->post('/ppdb/portal/pembayaran', [
                'metode' => 'transfer',
                'bukti' => UploadedFile::fake()->create('struk-transfer.jpg', 150, 'image/jpeg'),
                'catatan_siswa' => 'Transfer via Bank Jatim a.n. Orang Tua',
            ])
            ->assertRedirect('/ppdb/portal/data-diri');

        $pembayaran = PpdbPembayaran::query()->where('ppdb_pendaftaran_id', $pendaftaran1->id)->firstOrFail();
        $this->assertSame('menunggu', $pembayaran->status);
        $this->assertNotNull($pembayaran->bukti_path);
        Storage::disk('local')->assertExists($pembayaran->bukti_path);

        // Siswa lain TIDAK BOLEH mengunduh bukti pembayaran Siswa 1 (403 Forbidden)
        $account2 = PpdbAccount::factory()->create();
        $this->actingAs($account2, 'ppdb')
            ->get("/ppdb/portal/pembayaran/{$pembayaran->id}/bukti")
            ->assertForbidden();

        // Siswa 1 sendiri BOLEH mengunduh bukti pembayarannya
        $this->actingAs($account1, 'ppdb')
            ->get("/ppdb/portal/pembayaran/{$pembayaran->id}/bukti")
            ->assertOk();

        // Admin BOLEH melihat daftar pendaftar online, mengunduh bukti, memverifikasi pembayaran & meluluskan siswa
        $this->actingAs($admin, 'web')
            ->get('/admin/ppdb?tab=online')
            ->assertOk()
            ->assertSee('Bagas Adi Saputra');

        $this->actingAs($admin, 'web')
            ->get("/admin/ppdb/online/{$pendaftaran1->id}")
            ->assertOk()
            ->assertSee($pendaftaran1->no_pendaftaran);

        $this->actingAs($admin, 'web')
            ->get("/ppdb/portal/pembayaran/{$pembayaran->id}/bukti")
            ->assertOk();

        $this->actingAs($admin, 'web')
            ->patch("/admin/ppdb/pembayaran/{$pembayaran->id}/verifikasi", [
                'status' => 'lunas',
                'catatan_admin' => 'Transfer masuk ke rekening Bank Jatim.',
            ])
            ->assertRedirect("/admin/ppdb/online/{$pendaftaran1->id}");

        $this->assertDatabaseHas('ppdb_pembayaran', [
            'id' => $pembayaran->id,
            'status' => 'lunas',
        ]);

        $this->actingAs($admin, 'web')
            ->patch("/admin/ppdb/online/{$pendaftaran1->id}/status", [
                'status_seleksi' => 'diterima',
                'catatan_panitia' => 'Selamat, Anda dinyatakan diterima di SMP PGRI 4 Waru.',
            ])
            ->assertRedirect("/admin/ppdb/online/{$pendaftaran1->id}");

        $this->assertDatabaseHas('ppdb_pendaftaran', [
            'id' => $pendaftaran1->id,
            'status_seleksi' => 'diterima',
        ]);

        // Ekspor CSV PPDB Online
        $this->actingAs($admin, 'web')
            ->get('/admin/ekspor/ppdb-online.csv')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_step1_draft_and_password_are_preserved_when_step2_nik_validation_fails(): void
    {
        $this->postJson('/ppdb/daftar/tahap-1', [
            'email' => 'fajar.siswa@example.com',
            'password' => 'SmpPgri42026',
            'password_confirmation' => 'SmpPgri42026',
        ])->assertOk()->assertJson([
            'ok' => true,
            'email' => 'fajar.siswa@example.com',
        ]);

        // Submit Step 2 with invalid 12-digit NIK -> should redirect back to /ppdb/daftar on Step 2 with Step 1 preserved
        $this->from('/ppdb/daftar')
            ->post('/ppdb/daftar', [
                'email' => 'fajar.siswa@example.com',
                'password' => 'SmpPgri42026',
                'password_confirmation' => 'SmpPgri42026',
                'nama_lengkap' => 'Fajar Melfiano',
                'tanggal_lahir' => '2013-03-21',
                'whatsapp' => '895417356277',
                'nik' => '213212312312',
            ])
            ->assertRedirect('/ppdb/daftar')
            ->assertSessionHasErrors(['nik'])
            ->assertSessionDoesntHaveErrors(['email', 'password']);

        $page = $this->get('/ppdb/daftar');
        $page->assertOk()
            ->assertSee('data-initial-step="2"', false)
            ->assertSee('value="fajar.siswa@example.com"', false)
            ->assertSee('value="SmpPgri42026"', false);

        // Now fix only NIK on Step 2 without re-typing password -> registration succeeds
        $this->post('/ppdb/daftar', [
            'nama_lengkap' => 'Fajar Melfiano',
            'tanggal_lahir' => '2013-03-21',
            'whatsapp' => '895417356277',
            'nik' => '3515182103130001',
        ])->assertRedirect('/ppdb/portal');

        $this->assertAuthenticated('ppdb');
    }

    public function test_admin_can_login_while_already_authenticated_as_ppdb_student(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin.test@smppgri4waru.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $account = PpdbAccount::query()->create([
            'email' => 'siswa.dual@example.com',
            'password' => Hash::make('SmpPgri42026'),
            'nama_lengkap' => 'Siswa Dual Guard',
            'tanggal_lahir' => '2013-04-12',
            'whatsapp' => '081234567890',
            'nik' => '3515181204130007',
        ]);

        PpdbPendaftaran::query()->create([
            'no_pendaftaran' => 'PPDB-2026-0099',
            'ppdb_account_id' => $account->id,
            'tahun_ajaran' => '2026/2027',
            'status_seleksi' => 'proses',
        ]);

        // 1. Login as PPDB student first
        $this->post('/ppdb/masuk', [
            'identifier' => 'siswa.dual@example.com',
            'password' => 'SmpPgri42026',
        ])->assertRedirect('/ppdb/portal');

        $this->assertAuthenticatedAs($account, 'ppdb');

        // 2. Visiting /admin/login or /login while logged into PPDB works without 404 or redirect back to portal
        $this->get('/admin/login')->assertOk()->assertSee('Masuk petugas sekolah');
        $this->get('/login')->assertOk()->assertSee('Masuk petugas sekolah');

        // 3. Submitting admin credentials at /admin/login logs into `web` guard while preserving `ppdb` guard
        $this->post('/admin/login', [
            'email' => 'admin.test@smppgri4waru.id',
            'password' => 'password123',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin, 'web');
        $this->assertAuthenticatedAs($account, 'ppdb');

        // 4. Both /admin/ppdb and /ppdb/portal remain accessible in the same session
        $this->get('/admin/ppdb')->assertOk();
        $this->get('/ppdb/portal/beranda')->assertOk();

        // 5. Logging out of admin keeps PPDB session alive
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest('web');
        $this->assertAuthenticatedAs($account, 'ppdb');
    }
}
