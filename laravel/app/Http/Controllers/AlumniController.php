<?php

namespace App\Http\Controllers;

use App\Models\AlumniReview;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AlumniController extends Controller
{
    public function index(): View
    {
        $ulasan = AlumniReview::query()
            ->where('status', 'terbit')
            ->latest()
            ->get();

        return view('pages.alumni', compact('ulasan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'angkatan' => ['required', 'string', 'max:20'],
            'pekerjaan' => ['nullable', 'string', 'max:150'],
            'ulasan' => ['required', 'string', 'min:10', 'max:1200'],
            'foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'website_url' => ['nullable', 'max:0'],
        ], [
            'website_url.max' => 'Pengiriman ditolak oleh sistem keamanan.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'alumni-'.Str::uuid()->toString().'.'.$file->extension();
            $fotoPath = $file->storeAs('alumni', $filename, 'public');
        }

        AlumniReview::query()->create([
            'nama' => $validated['nama'],
            'angkatan' => $validated['angkatan'],
            'pekerjaan' => $validated['pekerjaan'] ?? null,
            'ulasan' => $validated['ulasan'],
            'foto_path' => $fotoPath,
            'status' => 'menunggu',
        ]);

        return redirect()
            ->route('alumni.index')
            ->with('status', 'Terima kasih! Ulasan alumni Anda telah dikirim dan akan ditampilkan setelah diverifikasi oleh pihak sekolah.');
    }
}
