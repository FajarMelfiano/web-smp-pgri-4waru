<?php

namespace Tests\Feature;

use App\Models\PpdbAccount;
use App\Models\User;
use App\Notifications\ResetPasswordCalonSiswa;
use App\Notifications\ResetPasswordPetugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_lupa_sandi_terbuka_untuk_petugas_dan_calon_siswa(): void
    {
        $this->get('/admin/lupa-sandi')
            ->assertOk()
            ->assertSee('Lupa kata sandi petugas');

        $this->get('/ppdb/lupa-sandi')
            ->assertOk()
            ->assertSee('Lupa kata sandi portal PPDB');
    }

    public function test_petugas_bisa_meminta_tautan_dan_mengganti_kata_sandi(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'petugas@smppgri4waru.id',
            'password' => 'SandiLama123',
        ]);

        $this->post('/admin/lupa-sandi', ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordPetugas::class);

        /** @var ResetPasswordPetugas $notification */
        $notification = Notification::sent($user, ResetPasswordPetugas::class)->first();
        $token = $notification->token;

        $this->get('/admin/reset-sandi/'.$token.'?email='.urlencode($user->email))
            ->assertOk()
            ->assertSee('Buat kata sandi baru');

        $this->post('/admin/reset-sandi', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'RahasiaBaru2026',
            'password_confirmation' => 'RahasiaBaru2026',
        ])->assertRedirect(route('admin'));

        $this->assertTrue(Hash::check('RahasiaBaru2026', $user->fresh()->password));
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_calon_siswa_bisa_meminta_tautan_dan_mengganti_kata_sandi(): void
    {
        Notification::fake();

        $account = PpdbAccount::factory()->create([
            'email' => 'calon.siswa@example.com',
            'password' => 'SandiLama123',
        ]);

        $this->post('/ppdb/lupa-sandi', ['email' => $account->email])
            ->assertRedirect()
            ->assertSessionHas('ppdb_status');

        Notification::assertSentTo($account, ResetPasswordCalonSiswa::class);

        /** @var ResetPasswordCalonSiswa $notification */
        $notification = Notification::sent($account, ResetPasswordCalonSiswa::class)->first();
        $token = $notification->token;

        $this->get('/ppdb/reset-sandi/'.$token.'?email='.urlencode($account->email))
            ->assertOk()
            ->assertSee('Buat kata sandi baru');

        $this->post('/ppdb/reset-sandi', [
            'token' => $token,
            'email' => $account->email,
            'password' => 'SandiBaru2026',
            'password_confirmation' => 'SandiBaru2026',
        ])->assertRedirect(route('ppdb.portal'));

        $this->assertTrue(Hash::check('SandiBaru2026', $account->fresh()->password));
        $this->assertAuthenticatedAs($account, 'ppdb');
    }

    public function test_email_tidak_terdaftar_tetap_mendapat_pesan_netral_tanpa_membocorkan_status(): void
    {
        Notification::fake();

        $this->post('/admin/lupa-sandi', ['email' => 'tidak-ada@example.com'])
            ->assertRedirect()
            ->assertSessionHas('status', fn (string $status): bool => str_contains($status, 'Kalau email itu terdaftar'));

        Notification::assertNothingSent();
    }

    public function test_token_palsu_ditolak_dan_kata_sandi_tidak_berubah(): void
    {
        $user = User::factory()->create([
            'email' => 'petugas2@smppgri4waru.id',
            'password' => 'SandiLama123',
        ]);

        $this->post('/admin/reset-sandi', [
            'token' => str_repeat('a', 64),
            'email' => $user->email,
            'password' => 'RahasiaBaru2026',
            'password_confirmation' => 'RahasiaBaru2026',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('SandiLama123', $user->fresh()->password));
    }

    public function test_token_petugas_tidak_berlaku_di_portal_ppdb(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'petugas3@smppgri4waru.id']);
        $account = PpdbAccount::factory()->create([
            'email' => 'siswa3@example.com',
            'password' => 'SandiLama123',
        ]);

        $user->sendPasswordResetNotification('token-petugas-123');

        $this->post('/ppdb/reset-sandi', [
            'token' => 'token-petugas-123',
            'email' => $account->email,
            'password' => 'SandiBaru2026',
            'password_confirmation' => 'SandiBaru2026',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('SandiLama123', $account->fresh()->password));
    }

    public function test_kata_sandi_baru_wajib_kuat(): void
    {
        Notification::fake();

        $account = PpdbAccount::factory()->create(['email' => 'siswa4@example.com']);

        $this->post('/ppdb/lupa-sandi', ['email' => $account->email]);
        $token = Notification::sent($account, ResetPasswordCalonSiswa::class)->first()->token;

        $this->post('/ppdb/reset-sandi', [
            'token' => $token,
            'email' => $account->email,
            'password' => 'pendek',
            'password_confirmation' => 'pendek',
        ])->assertSessionHasErrors('password');
    }
}
