<?php

namespace App\Http\Controllers;

use App\Models\PpdbPendaftaran;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminAccountController extends Controller
{
    public function edit(Request $request): View
    {
        return view('pages.admin.akun', [
            'petugas' => $request->user('web'),
        ]);
    }

    /**
     * Ganti kata sandi akun yang sedang masuk. Sandi lama wajib benar supaya
     * sesi yang dibajak tidak bisa memindahkan akun ke penyerang.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $petugas = $request->user('web');

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.required' => 'Masukkan kata sandi yang sekarang dipakai.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.required' => 'Masukkan kata sandi baru.',
            'password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.max' => 'Kata sandi baru maksimal 16 karakter.',
            'password.regex' => 'Kata sandi baru harus memuat huruf besar, huruf kecil, dan minimal satu angka.',
        ]);

        $this->guardAgainstReuse($validated['password'], $petugas->password);

        $petugas->forceFill([
            'password' => $validated['password'],
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();

        Log::info('Kata sandi petugas diubah sendiri.', [
            'user_id' => $petugas->id,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('admin.account')
            ->with('status', 'Kata sandi Anda berhasil diperbarui. Gunakan kata sandi baru saat masuk berikutnya.');
    }

    /**
     * Admin menetapkan kata sandi baru untuk akun petugas lain. Dipakai saat
     * petugas kehilangan akses email sehingga tautan lupa-sandi tidak sampai.
     */
    public function resetPetugasPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'new_password' => $this->passwordRules(),
        ], [
            'new_password.required' => 'Masukkan kata sandi baru untuk akun ini.',
            'new_password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.max' => 'Kata sandi baru maksimal 16 karakter.',
            'new_password.regex' => 'Kata sandi baru harus memuat huruf besar, huruf kecil, dan minimal satu angka.',
        ]);

        $this->guardAgainstReuse($validated['new_password'], $user->password, 'new_password');

        $user->forceFill([
            'password' => $validated['new_password'],
            'remember_token' => Str::random(60),
        ])->save();

        Log::warning('Kata sandi petugas direset oleh admin.', [
            'actor_id' => $request->user('web')?->id,
            'target_user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('admin.petugas')
            ->with('status', 'Kata sandi akun '.$user->name.' sudah diganti. Beri tahu yang bersangkutan untuk segera memakai kata sandi baru.');
    }

    /**
     * Admin menetapkan kata sandi baru untuk akun calon siswa PPDB. Akun
     * diambil dari relasi pendaftaran, bukan dari parameter terpisah, supaya
     * tidak bisa diarahkan ke akun lain.
     */
    public function resetPpdbPassword(Request $request, PpdbPendaftaran $pendaftaran): RedirectResponse
    {
        $akun = $pendaftaran->account;

        $validated = $request->validate([
            'new_password' => $this->passwordRules(),
        ], [
            'new_password.required' => 'Masukkan kata sandi baru untuk akun siswa ini.',
            'new_password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.max' => 'Kata sandi baru maksimal 16 karakter.',
            'new_password.regex' => 'Kata sandi baru harus memuat huruf besar, huruf kecil, dan minimal satu angka.',
        ]);

        $this->guardAgainstReuse($validated['new_password'], $akun->password, 'new_password');

        $akun->forceFill([
            'password' => $validated['new_password'],
            'remember_token' => Str::random(60),
        ])->save();

        Log::warning('Kata sandi akun PPDB direset oleh admin.', [
            'actor_id' => $request->user('web')?->id,
            'target_account_id' => $akun->id,
            'pendaftaran_id' => $pendaftaran->id,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('admin.ppdb.online.show', $pendaftaran)
            ->with('status', 'Kata sandi portal '.$akun->nama_lengkap.' sudah diganti.');
    }

    /**
     * Satu kebijakan kata sandi untuk seluruh aplikasi: 8-16 karakter dengan
     * huruf besar, huruf kecil, dan angka, sama seperti formulir pendaftaran.
     *
     * @return array<int, string>
     */
    private function passwordRules(): array
    {
        return ['required', 'string', 'min:8', 'max:16', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'confirmed'];
    }

    private function guardAgainstReuse(string $newPassword, ?string $currentHash, string $field = 'password'): void
    {
        if ($currentHash !== null && Hash::check($newPassword, $currentHash)) {
            throw ValidationException::withMessages([
                $field => 'Kata sandi baru tidak boleh sama dengan kata sandi yang sekarang.',
            ]);
        }
    }
}
