<?php

namespace App\Http\Controllers;

use App\Models\PpdbAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PpdbAccountController extends Controller
{
    public function edit(): View
    {
        $account = $this->currentAccount();

        return view('pages.ppdb.portal.akun', [
            'account' => $account,
            'pendaftaran' => $account->pendaftaran,
        ]);
    }

    /**
     * Calon siswa mengganti kata sandinya sendiri. Sandi lama wajib benar
     * supaya perangkat yang tertinggal login tidak bisa mengunci pemilik akun.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $account = $this->currentAccount();

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password:ppdb'],
            'password' => ['required', 'string', 'min:8', 'max:16', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'current_password.required' => 'Masukkan kata sandi yang sekarang dipakai.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.required' => 'Masukkan kata sandi baru.',
            'password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.max' => 'Kata sandi baru maksimal 16 karakter.',
            'password.regex' => 'Kata sandi baru harus memuat huruf besar, huruf kecil, dan minimal satu angka.',
        ]);

        if (Hash::check($validated['password'], (string) $account->password)) {
            throw ValidationException::withMessages([
                'password' => 'Kata sandi baru tidak boleh sama dengan kata sandi yang sekarang.',
            ]);
        }

        $account->forceFill([
            'password' => $validated['password'],
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();

        Log::info('Kata sandi akun PPDB diubah sendiri.', [
            'account_id' => $account->id,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('ppdb.portal.akun')
            ->with('ppdb_status', 'Kata sandi Anda berhasil diperbarui. Gunakan kata sandi baru saat masuk berikutnya.');
    }

    private function currentAccount(): PpdbAccount
    {
        /** @var PpdbAccount $account */
        $account = Auth::guard('ppdb')->user();

        return $account;
    }
}
