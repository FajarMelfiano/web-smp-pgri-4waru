<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AdminPasswordResetController extends Controller
{
    /**
     * Pesan netral: dipakai baik saat email terdaftar maupun tidak, supaya
     * halaman ini tidak bisa dipakai menebak email petugas yang ada.
     */
    private const NEUTRAL_STATUS = 'Kalau email itu terdaftar sebagai akun petugas, tautan pengaturan kata sandi sudah dikirim. Periksa kotak masuk dan folder spam.';

    public function showRequestForm(): View
    {
        return view('pages.auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ], [
            'email.required' => 'Masukkan alamat email akun petugas.',
            'email.email' => 'Format alamat email belum valid.',
            'email.max' => 'Alamat email maksimal 150 karakter.',
        ]);

        Password::broker('users')->sendResetLink([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        return back()->with('status', self::NEUTRAL_STATUS);
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('pages.auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ], [
            'password.required' => 'Masukkan kata sandi baru.',
            'password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.letters' => 'Kata sandi harus memuat huruf.',
            'password.numbers' => 'Kata sandi harus memuat minimal satu angka.',
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password): void {
                $user->forceFill(['password' => $password])->save();
                Auth::guard('web')->login($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $request->session()->regenerate();

            return redirect()
                ->route('admin')
                ->with('status', 'Kata sandi berhasil diperbarui. Anda sudah masuk ke panel petugas.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => $status === Password::INVALID_TOKEN
                    ? 'Tautan pengaturan kata sandi sudah kedaluwarsa atau tidak valid. Minta tautan baru.'
                    : 'Email tidak cocok dengan tautan pengaturan kata sandi ini.',
            ]);
    }
}
