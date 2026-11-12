<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterPpdbAccountRequest;
use App\Models\PpdbAccount;
use App\Models\PpdbPendaftaran;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PpdbAuthController extends Controller
{
    public function showRegister(Request $request): View
    {
        return view('pages.ppdb.auth.register', [
            'draftStep1' => $request->session()->get('ppdb_reg_draft', []),
        ]);
    }

    public function saveRegisterStep1(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:150', 'unique:ppdb_accounts,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'confirmed',
            ],
        ], [
            'email.required' => 'Masukkan alamat email calon siswa.',
            'email.email' => 'Format alamat email belum valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk menggunakan akun Anda.',
            'password.required' => 'Buat kata sandi terlebih dahulu.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.max' => 'Kata sandi maksimal 16 karakter.',
            'password.regex' => 'Kata sandi harus memuat huruf besar, huruf kecil, dan minimal satu angka.',
            'password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
        ]);

        $email = strtolower(trim($validated['email']));
        $request->session()->put('ppdb_reg_draft', [
            'email' => $email,
            'password' => $validated['password'],
        ]);

        return response()->json([
            'ok' => true,
            'email' => $email,
        ]);
    }

    public function register(RegisterPpdbAccountRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $request->session()->forget('ppdb_reg_draft');
        $tahunAjaran = Setting::getValue('tahun_ajaran', '2026/2027') ?? '2026/2027';

        $account = DB::transaction(function () use ($validated, $tahunAjaran): PpdbAccount {
            $account = PpdbAccount::query()->create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'nik' => $validated['nik'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'whatsapp' => $validated['whatsapp'],
                'password' => $validated['password'],
            ]);

            $pendaftaran = PpdbPendaftaran::query()->create([
                'ppdb_account_id' => $account->id,
                'tahun_ajaran' => $tahunAjaran,
                'status_seleksi' => 'proses',
            ]);

            $pendaftaran->update([
                'no_pendaftaran' => PpdbPendaftaran::generateNoPendaftaran($pendaftaran->id),
            ]);

            return $account;
        });

        Auth::guard('ppdb')->login($account);
        $request->session()->regenerate();

        return redirect()
            ->route('ppdb.portal')
            ->with('status', 'Akun PPDB berhasil dibuat. Silakan pilih jalur pendaftaran.')
            ->with('ppdb_status', 'Akun PPDB berhasil dibuat. Silakan pilih jalur pendaftaran.');
    }

    public function showLogin(): View
    {
        return view('pages.ppdb.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        if (! $request->filled('identifier') && $request->filled('identitas')) {
            $request->merge(['identifier' => $request->input('identitas')]);
        }

        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string'],
        ], [
            'identifier.required' => 'Masukkan email atau NIK calon siswa.',
            'password.required' => 'Masukkan kata sandi Anda.',
        ]);

        $identitas = strtolower(trim($validated['identifier']));
        $nikDigits = (string) preg_replace('/[^0-9]/', '', $identitas);

        $account = PpdbAccount::query()
            ->where('email', $identitas)
            ->when(strlen($nikDigits) === 16, fn ($query) => $query->orWhere('nik', $nikDigits))
            ->first();

        // Support dual-entry login: if user enters an admin/operator account on /ppdb/masuk,
        // authenticate on the `web` guard and redirect straight to the Admin Panel.
        $staffUser = User::query()->where('email', $identitas)->first();
        if ($staffUser && Hash::check($validated['password'], $staffUser->password)) {
            Auth::guard('web')->login($staffUser, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->route('admin');
        }

        if (! $account || ! Hash::check($validated['password'], $account->password)) {
            return redirect()
                ->route('ppdb.masuk')
                ->withErrors([
                    'identifier' => 'Email/NIK atau kata sandi tidak sesuai.',
                    'identitas' => 'Email/NIK atau kata sandi tidak sesuai.',
                ])
                ->onlyInput('identifier', 'identitas');
        }

        Auth::guard('ppdb')->login($account, $request->boolean('remember'));
        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended', route('ppdb.portal'));
        if (str_contains($intended, '/admin') || str_contains($intended, '/login')) {
            $intended = route('ppdb.portal');
        }

        return redirect()->to($intended);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('ppdb')->logout();

        if (! Auth::guard('web')->check()) {
            $request->session()->invalidate();
        }

        $request->session()->regenerateToken();

        return redirect()
            ->route('ppdb.masuk')
            ->with('status', 'Anda telah keluar dari portal PPDB.')
            ->with('ppdb_status', 'Anda telah keluar dari portal PPDB.');
    }
}
