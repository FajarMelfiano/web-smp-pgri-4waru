<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PpdbPasswordResetController extends Controller
{
    private const NEUTRAL_STATUS = 'Kalau email itu terdaftar sebagai akun PPDB, tautan pengaturan kata sandi sudah dikirim. Periksa kotak masuk dan folder spam.';

    public function showRequestForm(): View
    {
        return view('pages.ppdb.auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ], [
            'email.required' => 'Masukkan email yang dipakai saat mendaftar akun PPDB.',
            'email.email' => 'Format alamat email belum valid.',
            'email.max' => 'Alamat email maksimal 150 karakter.',
        ]);

        Password::broker('ppdb')->sendResetLink([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        return back()->with('ppdb_status', self::NEUTRAL_STATUS);
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('pages.ppdb.auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->max(16)->mixedCase()->numbers()],
        ], [
            'password.required' => 'Masukkan kata sandi baru.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.max' => 'Kata sandi maksimal 16 karakter.',
            'password.mixed' => 'Kata sandi harus memuat huruf besar dan huruf kecil.',
            'password.numbers' => 'Kata sandi harus memuat minimal satu angka.',
            'password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
        ]);

        $status = Password::broker('ppdb')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password): void {
                $user->forceFill(['password' => $password])->save();
                Auth::guard('ppdb')->login($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $request->session()->regenerate();

            return redirect()
                ->route('ppdb.portal')
                ->with('ppdb_status', 'Kata sandi berhasil diperbarui. Lanjutkan pendaftaran Anda.');
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
