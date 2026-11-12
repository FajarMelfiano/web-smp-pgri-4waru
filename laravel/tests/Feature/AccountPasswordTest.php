<?php

namespace Tests\Feature;

use App\Models\PpdbAccount;
use App\Models\PpdbPendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountPasswordTest extends TestCase
{
    use RefreshDatabase;

    private const SANDI_LAMA = 'Rahasia123';

    private const SANDI_BARU = 'KunciBaru456';

    public function test_halaman_akun_kedua_panel_hanya_untuk_yang_sudah_masuk(): void
    {
        $this->get('/admin/akun')->assertRedirect('/login');
        $this->get('/ppdb/portal/akun')->assertRedirect('/ppdb/masuk');
    }

    public function test_calon_siswa_mengganti_kata_sandinya_sendiri_lalu_bisa_masuk_dengan_sandi_baru(): void
    {
        $akun = PpdbAccount::factory()->create(['password' => self::SANDI_LAMA]);

        $this->actingAs($akun, 'ppdb')
            ->get('/ppdb/portal/akun')
            ->assertOk()
            ->assertSee('Akun &amp; Kata Sandi', false)
            ->assertSee($akun->email);

        $this->actingAs($akun, 'ppdb')
            ->post('/ppdb/portal/akun/sandi', [
                'current_password' => self::SANDI_LAMA,
                'password' => self::SANDI_BARU,
                'password_confirmation' => self::SANDI_BARU,
            ])
            ->assertRedirect(route('ppdb.portal.akun'))
            ->assertSessionHas('ppdb_status');

        $this->assertTrue(Hash::check(self::SANDI_BARU, $akun->refresh()->password));
        $this->assertSame($akun->id, auth('ppdb')->id());

        $this->post('/ppdb/masuk', [
            'identifier' => $akun->email,
            'password' => self::SANDI_BARU,
        ])->assertRedirect('/ppdb/portal');

        auth('ppdb')->logout();

        $this->post('/ppdb/masuk', [
            'identifier' => $akun->email,
            'password' => self::SANDI_LAMA,
        ])->assertSessionHasErrors('identifier');
    }

    public function test_sandi_lama_salah_menolak_perubahan_dan_tidak_mengubah_data(): void
    {
        $akun = PpdbAccount::factory()->create(['password' => self::SANDI_LAMA]);

        $this->actingAs($akun, 'ppdb')
            ->from('/ppdb/portal/akun')
            ->post('/ppdb/portal/akun/sandi', [
                'current_password' => 'SandiNgawur999',
                'password' => self::SANDI_BARU,
                'password_confirmation' => self::SANDI_BARU,
            ])
            ->assertRedirect('/ppdb/portal/akun')
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check(self::SANDI_LAMA, $akun->refresh()->password));
    }

    public function test_sandi_baru_ditolak_jika_sama_lemah_atau_tidak_cocok_konfirmasinya(): void
    {
        $akun = PpdbAccount::factory()->create(['password' => self::SANDI_LAMA]);

        // Sama dengan sandi sekarang
        $this->actingAs($akun, 'ppdb')
            ->post('/ppdb/portal/akun/sandi', [
                'current_password' => self::SANDI_LAMA,
                'password' => self::SANDI_LAMA,
                'password_confirmation' => self::SANDI_LAMA,
            ])
            ->assertSessionHasErrors('password');

        // Tanpa huruf besar
        $this->actingAs($akun, 'ppdb')
            ->post('/ppdb/portal/akun/sandi', [
                'current_password' => self::SANDI_LAMA,
                'password' => 'hurufkecil123',
                'password_confirmation' => 'hurufkecil123',
            ])
            ->assertSessionHasErrors('password');

        // Konfirmasi tidak cocok
        $this->actingAs($akun, 'ppdb')
            ->post('/ppdb/portal/akun/sandi', [
                'current_password' => self::SANDI_LAMA,
                'password' => self::SANDI_BARU,
                'password_confirmation' => 'KunciBaru457',
            ])
            ->assertSessionHasErrors('password');

        // Terlalu pendek
        $this->actingAs($akun, 'ppdb')
            ->post('/ppdb/portal/akun/sandi', [
                'current_password' => self::SANDI_LAMA,
                'password' => 'Ku7',
                'password_confirmation' => 'Ku7',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check(self::SANDI_LAMA, $akun->refresh()->password));
    }

    public function test_petugas_mengganti_kata_sandinya_sendiri_dan_remember_token_dirotasi(): void
    {
        $petugas = User::factory()->create(['password' => self::SANDI_LAMA, 'remember_token' => 'token-lama']);

        $this->actingAs($petugas, 'web')
            ->get('/admin/akun')
            ->assertOk()
            ->assertSee('Ganti Kata Sandi');

        $this->actingAs($petugas, 'web')
            ->post('/admin/akun/sandi', [
                'current_password' => self::SANDI_LAMA,
                'password' => self::SANDI_BARU,
                'password_confirmation' => self::SANDI_BARU,
            ])
            ->assertRedirect(route('admin.account'))
            ->assertSessionHas('status');

        $petugas->refresh();

        $this->assertTrue(Hash::check(self::SANDI_BARU, $petugas->password));
        $this->assertNotSame('token-lama', $petugas->remember_token);
        $this->assertSame($petugas->id, auth('web')->id());

        $this->post('/admin/akun/sandi', [
            'current_password' => 'SandiNgawur999',
            'password' => 'KunciLain789',
            'password_confirmation' => 'KunciLain789',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_admin_mereset_kata_sandi_calon_siswa_dari_halaman_detail_pendaftar(): void
    {
        $admin = User::factory()->admin()->create(['password' => self::SANDI_LAMA]);
        $akun = PpdbAccount::factory()->create(['password' => self::SANDI_LAMA]);
        $pendaftaran = PpdbPendaftaran::factory()->create(['ppdb_account_id' => $akun->id]);

        $this->actingAs($admin, 'web')
            ->get('/admin/ppdb/online/'.$pendaftaran->id)
            ->assertOk()
            ->assertSee('Keamanan Akun Portal Siswa');

        $this->actingAs($admin, 'web')
            ->post('/admin/ppdb/online/'.$pendaftaran->id.'/akun/sandi', [
                'new_password' => self::SANDI_BARU,
                'new_password_confirmation' => self::SANDI_BARU,
            ])
            ->assertRedirect(route('admin.ppdb.online.show', $pendaftaran))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check(self::SANDI_BARU, $akun->refresh()->password));

        $this->post('/ppdb/masuk', [
            'identifier' => $akun->email,
            'password' => self::SANDI_BARU,
        ])->assertRedirect('/ppdb/portal');
    }

    public function test_admin_mereset_kata_sandi_petugas_lain(): void
    {
        $admin = User::factory()->admin()->create(['password' => self::SANDI_LAMA]);
        $operator = User::factory()->create(['password' => self::SANDI_LAMA, 'role' => 'operator']);

        $this->actingAs($admin, 'web')
            ->post('/admin/petugas/'.$operator->id.'/sandi', [
                'new_password' => self::SANDI_BARU,
                'new_password_confirmation' => self::SANDI_BARU,
            ])
            ->assertRedirect(route('admin.petugas'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check(self::SANDI_BARU, $operator->refresh()->password));
    }

    public function test_operator_tidak_boleh_mereset_kata_sandi_akun_lain(): void
    {
        $operator = User::factory()->create(['password' => self::SANDI_LAMA, 'role' => 'operator']);
        $rekan = User::factory()->create(['password' => self::SANDI_LAMA]);
        $akun = PpdbAccount::factory()->create(['password' => self::SANDI_LAMA]);
        $pendaftaran = PpdbPendaftaran::factory()->create(['ppdb_account_id' => $akun->id]);

        $this->actingAs($operator, 'web')
            ->post('/admin/petugas/'.$rekan->id.'/sandi', [
                'new_password' => self::SANDI_BARU,
                'new_password_confirmation' => self::SANDI_BARU,
            ])
            ->assertForbidden();

        $this->actingAs($operator, 'web')
            ->post('/admin/ppdb/online/'.$pendaftaran->id.'/akun/sandi', [
                'new_password' => self::SANDI_BARU,
                'new_password_confirmation' => self::SANDI_BARU,
            ])
            ->assertForbidden();

        $this->assertTrue(Hash::check(self::SANDI_LAMA, $rekan->refresh()->password));
        $this->assertTrue(Hash::check(self::SANDI_LAMA, $akun->refresh()->password));
    }

    public function test_admin_tidak_bisa_menyetel_sandi_yang_sama_dengan_sandi_lama(): void
    {
        $admin = User::factory()->admin()->create(['password' => self::SANDI_LAMA]);
        $akun = PpdbAccount::factory()->create(['password' => self::SANDI_LAMA]);
        $pendaftaran = PpdbPendaftaran::factory()->create(['ppdb_account_id' => $akun->id]);

        $this->actingAs($admin, 'web')
            ->post('/admin/ppdb/online/'.$pendaftaran->id.'/akun/sandi', [
                'new_password' => self::SANDI_LAMA,
                'new_password_confirmation' => self::SANDI_LAMA,
            ])
            ->assertSessionHasErrors('new_password');

        $this->assertTrue(Hash::check(self::SANDI_LAMA, $akun->refresh()->password));
    }

    public function test_petugas_baru_wajib_memakai_kata_sandi_yang_kuat(): void
    {
        $admin = User::factory()->admin()->create(['password' => self::SANDI_LAMA]);

        $this->actingAs($admin, 'web')
            ->post('/admin/petugas', [
                'name' => 'Petugas Lemah',
                'email' => 'lemah@smppgri4waru.sch.id',
                'role' => 'operator',
                'password' => 'semuakecil',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'lemah@smppgri4waru.sch.id']);
    }
}
