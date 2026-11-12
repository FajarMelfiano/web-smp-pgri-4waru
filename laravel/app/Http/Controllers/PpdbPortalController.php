<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePpdbBerkasRequest;
use App\Http\Requests\StorePpdbKuesionerRequest;
use App\Http\Requests\StorePpdbPembayaranRequest;
use App\Http\Requests\UpdatePpdbBiodataRequest;
use App\Models\PpdbAccount;
use App\Models\PpdbBerkas;
use App\Models\PpdbJalur;
use App\Models\PpdbPembayaran;
use App\Models\PpdbPendaftaran;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PpdbPortalController extends Controller
{
    private function currentAccount(): PpdbAccount
    {
        /** @var PpdbAccount $account */
        $account = Auth::guard('ppdb')->user();

        return $account;
    }

    private function currentPendaftaran(): PpdbPendaftaran
    {
        $account = $this->currentAccount();

        $pendaftaran = $account->pendaftaran()
            ->with(['jalur', 'pembayaran', 'latestPembayaran', 'berkas'])
            ->first();

        if ($pendaftaran === null) {
            $tahunAjaran = Setting::getValue('tahun_ajaran', '2026/2027') ?? '2026/2027';

            $pendaftaran = PpdbPendaftaran::query()->create([
                'ppdb_account_id' => $account->id,
                'tahun_ajaran' => $tahunAjaran,
                'status_seleksi' => 'proses',
            ]);

            $pendaftaran->update([
                'no_pendaftaran' => PpdbPendaftaran::generateNoPendaftaran($pendaftaran->id),
            ]);

            $pendaftaran->load(['jalur', 'pembayaran', 'latestPembayaran', 'berkas']);
        } elseif ($pendaftaran->no_pendaftaran === null) {
            $pendaftaran->update([
                'no_pendaftaran' => PpdbPendaftaran::generateNoPendaftaran($pendaftaran->id),
            ]);
        }

        return $pendaftaran;
    }

    public function beranda(): View
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();
        $steps = $pendaftaran->stepStates();
        $nextAction = $pendaftaran->nextActionStep();
        $nextStep = $nextAction;

        return view('pages.ppdb.portal.beranda', compact('account', 'pendaftaran', 'steps', 'nextAction', 'nextStep'));
    }

    public function jalur(Request $request): View
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();
        $statusFilter = (string) $request->string('status', 'semua');
        if (! in_array($statusFilter, ['semua', 'dibuka', 'ditutup'], true)) {
            $statusFilter = 'semua';
        }

        $jalurQuery = PpdbJalur::query()
            ->withCount('pendaftaran')
            ->orderBy('urutan')
            ->orderBy('id');

        if ($statusFilter === 'dibuka') {
            $jalurQuery->where('dibuka', true);
        } elseif ($statusFilter === 'ditutup') {
            $jalurQuery->where('dibuka', false);
        }

        $jalurList = $jalurQuery->get();

        return view('pages.ppdb.portal.jalur', compact('account', 'pendaftaran', 'jalurList', 'statusFilter'));
    }

    public function storeJalur(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ppdb_jalur_id' => ['required', 'integer', 'exists:ppdb_jalur,id'],
        ], [
            'ppdb_jalur_id.required' => 'Pilih salah satu jalur pendaftaran yang dibuka.',
        ]);

        $pendaftaran = $this->currentPendaftaran();

        if ($pendaftaran->isPaymentConfirmed()) {
            return redirect()
                ->route('ppdb.portal.jalur')
                ->with('error', 'Jalur pendaftaran tidak dapat diubah setelah pembayaran terkonfirmasi.');
        }

        if ($pendaftaran->isPaymentPendingVerification()) {
            return redirect()
                ->route('ppdb.portal.pembayaran')
                ->with('error', 'Bukti pembayaran sedang diverifikasi petugas. Tunggu konfirmasi sebelum mengganti jalur.');
        }

        $jalur = PpdbJalur::query()
            ->withCount('pendaftaran')
            ->findOrFail((int) $validated['ppdb_jalur_id']);

        if (! $jalur->isOpenNow()) {
            return redirect()
                ->route('ppdb.portal.jalur')
                ->with('error', 'Jalur yang dipilih sedang ditutup, di luar periode, atau kuotanya telah terpenuhi.');
        }

        DB::transaction(function () use ($pendaftaran, $jalur): void {
            $noPendaftaran = $pendaftaran->no_pendaftaran
                ?? PpdbPendaftaran::generateNoPendaftaran($pendaftaran->id);

            $pendaftaran->update([
                'ppdb_jalur_id' => $jalur->id,
                'no_pendaftaran' => $noPendaftaran,
                'jalur_dipilih_at' => now(),
            ]);

            $pendaftaran->riwayatPembayaran()
                ->whereIn('status', ['menunggu_pembayaran', 'ditolak'])
                ->delete();

            $invoice = 'INV-'.now()->format('Ymd').'-'.str_pad((string) $pendaftaran->id, 4, '0', STR_PAD_LEFT).'-'.Str::upper(Str::random(4));
            $isFree = $jalur->biaya_daftar <= 0;

            PpdbPembayaran::query()->create([
                'ppdb_pendaftaran_id' => $pendaftaran->id,
                'kode_invoice' => $invoice,
                'nominal' => $jalur->biaya_daftar,
                'metode' => $isFree ? 'gratis' : 'transfer',
                'status' => $isFree ? 'lunas' : 'menunggu_pembayaran',
                'catatan_admin' => $isFree ? 'Terkonfirmasi otomatis (jalur bebas biaya pendaftaran).' : null,
                'verified_at' => $isFree ? now() : null,
            ]);
        });

        if ($jalur->biaya_daftar <= 0) {
            $msg = 'Jalur '.$jalur->nama.' berhasil dipilih. Biaya pendaftaran Rp 0 (terkonfirmasi otomatis), silakan lengkapi data diri.';

            return redirect()
                ->route('ppdb.portal.data-diri')
                ->with('status', $msg)
                ->with('ppdb_status', $msg);
        }

        $msg = 'Jalur '.$jalur->nama.' berhasil dipilih. Silakan lanjutkan konfirmasi pembayaran.';

        return redirect()
            ->route('ppdb.portal.pembayaran')
            ->with('status', $msg)
            ->with('ppdb_status', $msg);
    }

    public function pembayaran(): View|RedirectResponse
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();

        if (! $pendaftaran->hasSelectedJalur()) {
            return redirect()
                ->route('ppdb.portal.jalur')
                ->with('error', 'Pilih jalur pendaftaran terlebih dahulu sebelum melakukan pembayaran.')
                ->with('ppdb_warning', 'Pilih jalur pendaftaran terlebih dahulu sebelum melakukan pembayaran.');
        }

        $payment = $pendaftaran->activePayment();
        $bankInfo = [
            'nama_bank' => Setting::getValue('ppdb_bank_nama', 'Bank Jatim / Loket TU Sekolah') ?? 'Bank Jatim / Loket TU Sekolah',
            'nomor_rekening' => Setting::getValue('ppdb_bank_rekening', '0262041979') ?? '0262041979',
            'atas_nama' => Setting::getValue('ppdb_bank_atas_nama', 'SMP PGRI 4 Waru') ?? 'SMP PGRI 4 Waru',
        ];

        return view('pages.ppdb.portal.pembayaran', compact('account', 'pendaftaran', 'payment', 'bankInfo'));
    }

    public function storePembayaran(StorePpdbPembayaranRequest $request): RedirectResponse
    {
        $pendaftaran = $this->currentPendaftaran();

        if (! $pendaftaran->hasSelectedJalur()) {
            return redirect()
                ->route('ppdb.portal.jalur')
                ->with('error', 'Pilih jalur pendaftaran terlebih dahulu.');
        }

        if ($pendaftaran->isPaymentConfirmed()) {
            return redirect()
                ->route('ppdb.portal.data-diri')
                ->with('status', 'Pembayaran Anda sudah terkonfirmasi lunas.')
                ->with('ppdb_status', 'Pembayaran Anda sudah terkonfirmasi lunas.');
        }

        $validated = $request->validated();
        $payment = $pendaftaran->activePayment();

        if ($payment === null) {
            $nominal = $pendaftaran->jalur?->biaya_daftar ?? 0;
            $payment = PpdbPembayaran::query()->create([
                'ppdb_pendaftaran_id' => $pendaftaran->id,
                'kode_invoice' => 'INV-'.now()->format('Ymd').'-'.str_pad((string) $pendaftaran->id, 4, '0', STR_PAD_LEFT).'-'.Str::upper(Str::random(4)),
                'nominal' => $nominal,
                'metode' => $validated['metode'],
                'status' => 'menunggu_pembayaran',
            ]);
        }

        $buktiPath = $payment->bukti_path;
        $uploadedFile = $request->file('bukti') ?? $request->file('bukti_bayar');

        if ($validated['metode'] === 'transfer' && $uploadedFile !== null) {
            if ($buktiPath !== null) {
                Storage::disk('local')->delete($buktiPath);
            }

            $filename = 'bukti-'.$pendaftaran->id.'-'.Str::uuid()->toString().'.'.$uploadedFile->extension();
            $buktiPath = $uploadedFile->storeAs('ppdb/pembayaran', $filename, 'local');
        } elseif (in_array($validated['metode'], ['kantor', 'tunai_tu'], true) && $buktiPath !== null) {
            Storage::disk('local')->delete($buktiPath);
            $buktiPath = null;
        }

        $payment->update([
            'metode' => $validated['metode'],
            'status' => 'menunggu',
            'bukti_path' => $buktiPath,
            'catatan_siswa' => $validated['catatan_siswa'] ?? null,
            'catatan_admin' => null,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        $message = $validated['metode'] === 'transfer'
            ? 'Bukti transfer berhasil dikirim. Silakan lengkapi Data Diri sembari menunggu verifikasi Tata Usaha.'
            : 'Permintaan bayar di kantor TU tercatat. Silakan lengkapi Data Diri dan datang ke kantor sekolah Jl. Anggrek No. 109.';

        return redirect()
            ->route('ppdb.portal.data-diri')
            ->with('status', $message)
            ->with('ppdb_status', $message);
    }

    public function downloadBuktiBayar(PpdbPembayaran $pembayaran): StreamedResponse
    {
        $isAdmin = Auth::guard('web')->check();
        $account = Auth::guard('ppdb')->user();

        if (! $isAdmin && ($account === null || $pembayaran->pendaftaran?->ppdb_account_id !== $account->id)) {
            abort(403, 'Akses ditolak.');
        }

        if ($pembayaran->bukti_path === null || ! Storage::disk('local')->exists($pembayaran->bukti_path)) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        return Storage::disk('local')->response($pembayaran->bukti_path);
    }

    public function riwayatPembayaran(): View
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();
        $riwayat = $pendaftaran->riwayatPembayaran()->get();

        return view('pages.ppdb.portal.riwayat-pembayaran', compact('account', 'pendaftaran', 'riwayat'));
    }

    public function dataDiri(): View|RedirectResponse
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();

        if (! $pendaftaran->canAccessBiodata()) {
            return redirect()
                ->route($pendaftaran->hasSelectedJalur() ? 'ppdb.portal.pembayaran' : 'ppdb.portal.jalur')
                ->with('error', 'Tahap Isi Data Diri terbuka setelah konfirmasi pembayaran pendaftaran.')
                ->with('ppdb_warning', 'Tahap Isi Data Diri terbuka setelah konfirmasi pembayaran pendaftaran.');
        }

        return view('pages.ppdb.portal.data-diri', compact('account', 'pendaftaran'));
    }

    public function updateDataDiri(UpdatePpdbBiodataRequest $request): RedirectResponse
    {
        $pendaftaran = $this->currentPendaftaran();

        if (! $pendaftaran->canAccessBiodata()) {
            return redirect()
                ->route($pendaftaran->hasSelectedJalur() ? 'ppdb.portal.pembayaran' : 'ppdb.portal.jalur')
                ->with('error', 'Selesaikan konfirmasi pembayaran terlebih dahulu.');
        }

        $validated = $request->validated();
        if (($validated['jenis_kelamin'] ?? null) === 'L') {
            $validated['jenis_kelamin'] = 'Laki-laki';
        } elseif (($validated['jenis_kelamin'] ?? null) === 'P') {
            $validated['jenis_kelamin'] = 'Perempuan';
        }

        $pendaftaran->update([
            ...$validated,
            'data_diri_lengkap_at' => $pendaftaran->data_diri_lengkap_at ?? now(),
        ]);

        $msg = 'Biodata calon siswa dan orang tua berhasil disimpan. Silakan unggah 4 berkas persyaratan.';

        return redirect()
            ->route('ppdb.portal.berkas')
            ->with('status', $msg)
            ->with('ppdb_status', $msg);
    }

    public function berkas(): View|RedirectResponse
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();

        if (! $pendaftaran->isDataDiriComplete()) {
            return redirect()
                ->route('ppdb.portal.data-diri')
                ->with('error', 'Lengkapi formulir Data Diri terlebih dahulu sebelum mengunggah berkas.')
                ->with('ppdb_warning', 'Lengkapi formulir Data Diri terlebih dahulu sebelum mengunggah berkas.');
        }

        $berkasByJenis = $pendaftaran->berkas->keyBy('jenis');
        $berkasMap = $berkasByJenis;
        $requiredBerkas = PpdbPendaftaran::REQUIRED_BERKAS;

        return view('pages.ppdb.portal.berkas', compact('account', 'pendaftaran', 'berkasByJenis', 'berkasMap', 'requiredBerkas'));
    }

    public function storeBerkas(StorePpdbBerkasRequest $request): RedirectResponse
    {
        $pendaftaran = $this->currentPendaftaran();

        if (! $pendaftaran->isDataDiriComplete()) {
            return redirect()
                ->route('ppdb.portal.data-diri')
                ->with('error', 'Lengkapi Data Diri terlebih dahulu sebelum mengunggah berkas.');
        }

        $validated = $request->validated();
        $jenis = $validated['jenis'];
        $file = $request->file('file');

        $existing = $pendaftaran->berkas()->where('jenis', $jenis)->first();
        if ($existing !== null) {
            Storage::disk('local')->delete($existing->file_path);
        }

        $filename = $jenis.'-'.Str::uuid()->toString().'.'.$file->extension();
        $storedPath = $file->storeAs('ppdb/berkas/'.$pendaftaran->id, $filename, 'local');

        PpdbBerkas::query()->updateOrCreate(
            [
                'ppdb_pendaftaran_id' => $pendaftaran->id,
                'jenis' => $jenis,
            ],
            [
                'file_path' => $storedPath,
                'original_name' => Str::limit($file->getClientOriginalName(), 170, ''),
                'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                'file_size' => (int) $file->getSize(),
                'status' => 'menunggu',
                'catatan_admin' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]
        );

        $pendaftaran->load('berkas');
        $pendaftaran->update([
            'berkas_lengkap_at' => $pendaftaran->hasUploadedAllRequiredBerkas() ? ($pendaftaran->berkas_lengkap_at ?? now()) : null,
        ]);

        $label = PpdbPendaftaran::BERKAS_LABELS[$jenis] ?? PpdbPendaftaran::REQUIRED_BERKAS[$jenis] ?? strtoupper($jenis);

        return redirect()
            ->route('ppdb.portal.berkas')
            ->with('status', 'Berkas '.$label.' berhasil diunggah.')
            ->with('ppdb_status', 'Berkas '.$label.' berhasil diunggah.');
    }

    public function downloadBerkas(PpdbBerkas $berkas): StreamedResponse
    {
        $isAdmin = Auth::guard('web')->check();
        $account = Auth::guard('ppdb')->user();

        if (! $isAdmin && ($account === null || $berkas->pendaftaran?->ppdb_account_id !== $account->id)) {
            abort(403, 'Anda tidak berhak mengakses berkas ini.');
        }

        if (! Storage::disk('local')->exists($berkas->file_path)) {
            abort(404, 'File berkas tidak ditemukan.');
        }

        return Storage::disk('local')->response($berkas->file_path, $berkas->original_name);
    }

    public function kuesioner(): View|RedirectResponse
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();

        if (! $pendaftaran->hasUploadedAllRequiredBerkas()) {
            return redirect()
                ->route('ppdb.portal.berkas')
                ->with('error', 'Unggah seluruh 4 berkas wajib terlebih dahulu sebelum mengisi kuesioner.')
                ->with('ppdb_warning', 'Unggah seluruh 4 berkas wajib terlebih dahulu sebelum mengisi kuesioner.');
        }

        return view('pages.ppdb.portal.kuesioner', compact('account', 'pendaftaran'));
    }

    public function storeKuesioner(StorePpdbKuesionerRequest $request): RedirectResponse
    {
        $pendaftaran = $this->currentPendaftaran();

        if (! $pendaftaran->hasUploadedAllRequiredBerkas()) {
            return redirect()
                ->route('ppdb.portal.berkas')
                ->with('error', 'Unggah seluruh 4 berkas wajib terlebih dahulu sebelum mengisi kuesioner.');
        }

        $validated = $request->validated();

        $pendaftaran->update([
            'kuesioner' => [
                'sumber_info' => $validated['sumber_info'],
                'alasan_memilih' => $validated['alasan_memilih'],
                'minat_ekstrakurikuler' => $validated['minat_ekstrakurikuler'] ?? [],
                'siap_tata_tertib' => true,
                'saran' => $validated['saran'] ?? null,
            ],
            'kuesioner_selesai_at' => $pendaftaran->kuesioner_selesai_at ?? now(),
        ]);

        $msg = 'Kuesioner tersimpan dan pendaftaran Anda telah lengkap. Kartu Peserta siap dicetak.';

        return redirect()
            ->route('ppdb.portal.kartu-peserta')
            ->with('status', $msg)
            ->with('ppdb_status', $msg);
    }

    public function kartuPeserta(): View|RedirectResponse
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();

        if (! $pendaftaran->isKartuPesertaUnlocked()) {
            $target = $pendaftaran->nextActionStep()['route'];

            return redirect()
                ->route($target)
                ->with('error', 'Kartu Peserta tersedia setelah seluruh tahap hingga Kuesioner diselesaikan.')
                ->with('ppdb_warning', 'Kartu Peserta tersedia setelah seluruh tahap hingga Kuesioner diselesaikan.');
        }

        $fotoBerkas = $pendaftaran->berkas->firstWhere('jenis', 'foto');

        return view('pages.ppdb.portal.kartu-peserta', compact('account', 'pendaftaran', 'fotoBerkas'));
    }

    public function pengumuman(): View
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();
        $steps = $pendaftaran->stepStates();

        return view('pages.ppdb.portal.pengumuman', compact('account', 'pendaftaran', 'steps'));
    }

    public function bantuan(): View
    {
        $account = $this->currentAccount();
        $pendaftaran = $this->currentPendaftaran();

        return view('pages.ppdb.portal.bantuan', compact('account', 'pendaftaran'));
    }
}
