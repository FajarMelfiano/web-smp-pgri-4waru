<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePendaftarRequest;
use App\Mail\PendaftarBaruMail;
use App\Models\Pendaftar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class PpdbController extends Controller
{
    public function store(StorePendaftarRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $pendaftar = Pendaftar::query()->create([
            'nama_siswa' => $validated['nama_siswa'],
            'asal_sd' => $validated['asal_sd'],
            'nama_wali' => $validated['nama_wali'],
            'whatsapp' => $validated['whatsapp'],
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'baru',
            'sumber' => 'web',
            'ip' => $request->ip(),
        ]);

        Mail::to(config('sekolah.email', 'smp.pgri4waru@gmail.com'))
            ->send(new PendaftarBaruMail($pendaftar));

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'id' => $pendaftar->id,
                'message' => 'Data pendaftaran tercatat.',
            ], 201);
        }

        return redirect()->route('ppdb')->with('status', 'Data pendaftaran berhasil dikirim.');
    }
}
