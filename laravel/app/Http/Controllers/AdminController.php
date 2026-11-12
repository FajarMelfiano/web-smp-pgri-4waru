<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StorePpdbJalurRequest;
use App\Models\AlumniReview;
use App\Models\Galeri;
use App\Models\Pendaftar;
use App\Models\Post;
use App\Models\PpdbBerkas;
use App\Models\PpdbJalur;
use App\Models\PpdbPembayaran;
use App\Models\PpdbPendaftaran;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * @return array<string, int>
     */
    private function sharedStats(): array
    {
        return [
            'total' => Pendaftar::query()->count(),
            'baru' => Pendaftar::query()->where('status', 'baru')->count(),
            'dihubungi' => Pendaftar::query()->where('status', 'dihubungi')->count(),
            'diterima' => Pendaftar::query()->where('status', 'diterima')->count(),
            'ditolak' => Pendaftar::query()->where('status', 'ditolak')->count(),
            'menunggu' => Pendaftar::query()->whereIn('status', ['baru', 'dihubungi'])->count(),
            'online_total' => PpdbPendaftaran::query()->count(),
            'online_menunggu_bayar' => PpdbPembayaran::query()->whereIn('status', ['menunggu', 'menunggu_verifikasi'])->count(),
            'online_diterima' => PpdbPendaftaran::query()->where('status_seleksi', 'diterima')->count(),
            'jalur_dibuka' => PpdbJalur::query()->where('dibuka', true)->count(),
            'berita_total' => Post::query()->count(),
            'berita_terbit' => Post::query()->where('status', 'terbit')->count(),
            'berita_draf' => Post::query()->where('status', 'draf')->count(),
            'galeri_total' => Galeri::query()->count(),
            'alumni_total' => AlumniReview::query()->count(),
            'alumni_terbit' => AlumniReview::query()->where('status', 'terbit')->count(),
            'alumni_menunggu' => AlumniReview::query()->where('status', 'menunggu')->count(),
            'petugas_total' => User::query()->count(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function defaultSettings(): array
    {
        return [
            'nama_sekolah' => 'SMP PGRI 4 Waru',
            'tagline' => 'Kureksari, Sidoarjo',
            'npsn' => '20540020',
            'naungan' => 'YPLP DASMEN PGRI Jawa Timur',
            'sk_pendirian' => '421.3/2829/404.3.1/1979',
            'tahun_berdiri' => '1979',
            'luas_tanah' => '660 m²',
            'waktu_belajar' => 'Pagi hari, enam hari dalam seminggu',
            'telepon' => '(031) 8538447',
            'email' => 'smp.pgri4waru@gmail.com',
            'instagram' => '@smppgri4waru',
            'alamat' => 'Jl. Anggrek No. 109, Kureksari, Kec. Waru, Kab. Sidoarjo',
            'kode_pos' => '61256',
            'koordinat' => '-7.3619, 112.7327',
            'jam_layanan' => 'Senin sampai Sabtu, pukul 07.00 sampai 14.00 WIB',
            'hero_eyebrow' => 'Selamat datang di SMP PGRI 4 Waru',
            'hero_judul' => 'Sekolah menengah pertama untuk keluarga Kureksari sejak 1979.',
            'hero_deskripsi' => 'Sekolah swasta di bawah YPLP DASMEN PGRI Jawa Timur. Alamatnya tidak pernah pindah dari Jalan Anggrek No. 109, dan kegiatan belajar berlangsung pagi hari, enam hari dalam seminggu.',
            'visi_sekolah' => 'Menjadi sekolah menengah pertama yang dekat dengan warganya, tempat siswa Kureksari belajar dengan tertib, percaya diri, dan siap melanjutkan pendidikan.',
            'pengumuman_ppdb' => 'Pendaftaran PPDB Online Tahun Ajaran 2026/2027 telah dibuka. Buat akun dan lengkapi berkas secara daring atau kunjungi kantor sekolah Jl. Anggrek No. 109.',
            'status_ppdb' => 'Dibuka: Online & Kantor Sekolah',
            'tahun_ajaran' => '2026/2027',
            'ppdb_bank_nama' => 'Bank Jatim / Loket TU Sekolah',
            'ppdb_bank_rekening' => 'Konfirmasi nomor rekening ke TU (031) 8538447',
            'ppdb_bank_atas_nama' => 'SMP PGRI 4 Waru',
            'ppdb_catatan_bayar' => 'Pembayaran dapat dilakukan melalui transfer ke rekening sekolah atau tunai langsung di loket Tata Usaha Jl. Anggrek No. 109 (Senin sampai Sabtu, pukul 07.00 sampai 13.00 WIB).',
            'logo_path' => '',
            'hero_image_path' => '',
            'brosur_ppdb_path' => '',
        ];
    }

    public function dashboard(): View
    {
        $stats = $this->sharedStats();
        $pendaftarTerbaru = Pendaftar::query()->latest()->limit(5)->get();
        $onlineTerbaru = PpdbPendaftaran::query()
            ->with(['account', 'jalur', 'latestPembayaran'])
            ->latest()
            ->limit(5)
            ->get();
        $beritaTerbaru = Post::query()->latest()->limit(4)->get();
        $settings = array_merge(
            $this->defaultSettings(),
            Setting::query()->pluck('value', 'key')->all()
        );

        return view('pages.admin', compact('stats', 'pendaftarTerbaru', 'onlineTerbaru', 'beritaTerbaru', 'settings'));
    }

    public function ppdb(Request $request): View
    {
        $query = Pendaftar::query()->latest();

        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where(function ($sub) use ($q): void {
                $sub->where('nama_siswa', 'like', "%{$q}%")
                    ->orWhere('nama_wali', 'like', "%{$q}%")
                    ->orWhere('asal_sd', 'like', "%{$q}%")
                    ->orWhere('whatsapp', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->string('status'));
        }

        $pendaftar = $query->get();

        $onlineQuery = PpdbPendaftaran::query()
            ->with(['account', 'jalur', 'pembayaran', 'latestPembayaran', 'berkas'])
            ->latest();

        $searchOnline = $request->filled('online_q')
            ? (string) $request->string('online_q')
            : ($request->filled('q') ? (string) $request->string('q') : '');

        if ($searchOnline !== '') {
            $onlineQuery->where(function ($sub) use ($searchOnline): void {
                $sub->where('no_pendaftaran', 'like', "%{$searchOnline}%")
                    ->orWhere('asal_sd', 'like', "%{$searchOnline}%")
                    ->orWhereHas('account', function ($acc) use ($searchOnline): void {
                        $acc->where('nama_lengkap', 'like', "%{$searchOnline}%")
                            ->orWhere('email', 'like', "%{$searchOnline}%")
                            ->orWhere('nik', 'like', "%{$searchOnline}%");
                    });
            });
        }

        if ($request->filled('jalur_id')) {
            $onlineQuery->where('ppdb_jalur_id', (int) $request->input('jalur_id'));
        }

        $statusOnline = $request->filled('online_status')
            ? (string) $request->string('online_status')
            : ($request->filled('status_seleksi') ? (string) $request->string('status_seleksi') : '');

        if ($statusOnline !== '') {
            $onlineQuery->where('status_seleksi', $statusOnline);
        }

        $onlinePendaftaran = $onlineQuery->get();
        $jalurList = PpdbJalur::query()
            ->withCount('pendaftaran')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();
        $editJalur = $request->filled('edit_jalur')
            ? PpdbJalur::query()->find((int) $request->input('edit_jalur'))
            : null;
        $stats = $this->sharedStats();

        return view('pages.admin.ppdb', compact(
            'pendaftar',
            'onlinePendaftaran',
            'jalurList',
            'editJalur',
            'stats'
        ));
    }

    public function showOnlinePpdb(PpdbPendaftaran $pendaftaran): View
    {
        $pendaftaran->load(['account', 'jalur', 'pembayaran.verifier', 'latestPembayaran', 'berkas.verifier']);
        $stats = $this->sharedStats();
        $requiredBerkas = PpdbPendaftaran::REQUIRED_BERKAS;
        $berkasByJenis = $pendaftaran->berkas->keyBy('jenis');

        return view('pages.admin.ppdb-online-show', compact(
            'pendaftaran',
            'stats',
            'requiredBerkas',
            'berkasByJenis'
        ));
    }

    public function storePpdbJalur(StorePpdbJalurRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        PpdbJalur::query()->create([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'persyaratan' => $validated['persyaratan'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'biaya_daftar' => $validated['biaya_daftar'],
            'kuota' => $validated['kuota'] ?? null,
            'dibuka' => $request->boolean('dibuka', true),
            'urutan' => $validated['urutan'] ?? 1,
        ]);

        return redirect()->route('admin.ppdb', ['tab' => 'jalur'])->with('status', 'Jalur PPDB baru berhasil ditambahkan.');
    }

    public function updatePpdbJalur(StorePpdbJalurRequest $request, PpdbJalur $jalur): RedirectResponse
    {
        $validated = $request->validated();

        $jalur->update([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'persyaratan' => $validated['persyaratan'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'biaya_daftar' => $validated['biaya_daftar'],
            'kuota' => $validated['kuota'] ?? null,
            'dibuka' => $request->boolean('dibuka'),
            'urutan' => $validated['urutan'] ?? $jalur->urutan,
        ]);

        return redirect()->route('admin.ppdb', ['tab' => 'jalur'])->with('status', 'Data jalur '.$jalur->nama.' berhasil diperbarui.');
    }

    public function destroyPpdbJalur(PpdbJalur $jalur): RedirectResponse
    {
        if ($jalur->pendaftaran()->exists()) {
            return redirect()
                ->route('admin.ppdb', ['tab' => 'jalur'])
                ->with('status', 'Jalur tidak dapat dihapus karena sudah dipilih pendaftar. Ubah status menjadi ditutup.');
        }

        $jalur->delete();

        return redirect()->route('admin.ppdb', ['tab' => 'jalur'])->with('status', 'Jalur PPDB berhasil dihapus.');
    }

    public function updateOnlinePembayaran(Request $request, PpdbPendaftaran $pendaftaran): RedirectResponse
    {
        $payment = $pendaftaran->latestPembayaran()->first();
        if ($payment === null) {
            return redirect()
                ->route('admin.ppdb.online.show', $pendaftaran)
                ->with('status', 'Belum ada tagihan pembayaran untuk pendaftar ini.');
        }

        return $this->verifikasiPembayaranPpdb($request, $payment);
    }

    public function verifikasiPembayaranPpdb(Request $request, PpdbPembayaran $pembayaran): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:lunas,ditolak,menunggu,menunggu_verifikasi'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $pembayaran->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?? null,
            'verified_by' => $request->user()?->id,
            'verified_at' => in_array($validated['status'], ['menunggu', 'menunggu_verifikasi'], true) ? null : now(),
        ]);

        return redirect()
            ->route('admin.ppdb.online.show', $pembayaran->ppdb_pendaftaran_id)
            ->with('status', 'Status pembayaran diperbarui menjadi '.$pembayaran->statusLabel().'.');
    }

    public function updateOnlineBerkas(Request $request, PpdbPendaftaran $pendaftaran, PpdbBerkas $berkas): RedirectResponse
    {
        abort_unless($berkas->ppdb_pendaftaran_id === $pendaftaran->id, 404);

        return $this->verifikasiBerkasPpdb($request, $berkas);
    }

    public function verifikasiBerkasPpdb(Request $request, PpdbBerkas $berkas): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:valid,ditolak,revisi,menunggu'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $berkas->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?? null,
            'verified_by' => $request->user()?->id,
            'verified_at' => $validated['status'] === 'menunggu' ? null : now(),
        ]);

        $pendaftaran = $berkas->pendaftaran;
        if ($pendaftaran !== null) {
            $pendaftaran->load('berkas');
            $pendaftaran->update([
                'berkas_lengkap_at' => $pendaftaran->hasUploadedAllRequiredBerkas() ? ($pendaftaran->berkas_lengkap_at ?? now()) : null,
            ]);
        }

        return redirect()
            ->route('admin.ppdb.online.show', $berkas->ppdb_pendaftaran_id)
            ->with('status', 'Status berkas '.$berkas->label().' diperbarui.');
    }

    public function updateOnlineStatus(Request $request, PpdbPendaftaran $pendaftaran): RedirectResponse
    {
        if (! $request->filled('catatan_admin') && $request->filled('catatan_panitia')) {
            $request->merge(['catatan_admin' => $request->input('catatan_panitia')]);
        }

        $validated = $request->validate([
            'status_seleksi' => ['required', 'in:proses,terverifikasi,diterima,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $pendaftaran->update([
            'status_seleksi' => $validated['status_seleksi'],
            'catatan_admin' => $validated['catatan_admin'] ?? null,
            'pengumuman_at' => in_array($validated['status_seleksi'], ['diterima', 'ditolak'], true)
                ? ($pendaftaran->pengumuman_at ?? now())
                : null,
        ]);

        return redirect()
            ->route('admin.ppdb.online.show', $pendaftaran)
            ->with('status', 'Status seleksi pendaftar diperbarui menjadi '.$pendaftaran->statusSeleksiLabel().'.');
    }

    public function destroyOnlinePpdb(PpdbPendaftaran $pendaftaran): RedirectResponse
    {
        foreach ($pendaftaran->riwayatPembayaran as $pay) {
            if ($pay->bukti_path !== null) {
                Storage::disk('local')->delete($pay->bukti_path);
            }
        }

        foreach ($pendaftaran->berkas as $berkas) {
            Storage::disk('local')->delete($berkas->file_path);
        }

        $account = $pendaftaran->account;
        $pendaftaran->delete();
        $account?->delete();

        return redirect()
            ->route('admin.ppdb', ['tab' => 'online'])
            ->with('status', 'Data pendaftar PPDB online berhasil dihapus.');
    }

    public function downloadOnlineBerkas(PpdbPendaftaran $pendaftaran, PpdbBerkas $berkas): StreamedResponse
    {
        abort_unless($berkas->ppdb_pendaftaran_id === $pendaftaran->id, 404);
        abort_unless(Storage::disk('local')->exists($berkas->file_path), 404);

        return Storage::disk('local')->response($berkas->file_path, $berkas->original_name);
    }

    public function downloadOnlineBuktiBayar(PpdbPendaftaran $pendaftaran, PpdbPembayaran $pembayaran): StreamedResponse
    {
        abort_unless($pembayaran->ppdb_pendaftaran_id === $pendaftaran->id, 404);
        abort_unless($pembayaran->bukti_path !== null && Storage::disk('local')->exists($pembayaran->bukti_path), 404);

        return Storage::disk('local')->response($pembayaran->bukti_path);
    }

    public function exportOnlineCsv(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                'No Pendaftaran',
                'Nama Lengkap',
                'Email',
                'NIK',
                'Tanggal Lahir',
                'WhatsApp',
                'Jalur',
                'Status Bayar',
                'Asal SD',
                'Status Seleksi',
                'Waktu Daftar',
            ]);

            $rows = PpdbPendaftaran::query()
                ->with(['account', 'jalur', 'latestPembayaran'])
                ->orderBy('id')
                ->cursor();

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->no_pendaftaran ?? '-',
                    $row->account?->nama_lengkap ?? '-',
                    $row->account?->email ?? '-',
                    $row->account?->nik ?? '-',
                    $row->account?->tanggal_lahir?->toDateString() ?? '-',
                    $row->account?->whatsapp ?? '-',
                    $row->jalur?->nama ?? '-',
                    $row->latestPembayaran?->statusLabel() ?? 'Belum Memilih Jalur',
                    $row->asal_sd ?? '-',
                    $row->statusSeleksiLabel(),
                    $row->created_at?->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, 'ppdb-online-smp-pgri-4-waru.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function berita(Request $request): View
    {
        $query = Post::query()->latest();

        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where(function ($sub) use ($q): void {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('ringkasan', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->string('status'));
        }

        $posts = $query->get();
        $stats = $this->sharedStats();

        $editPost = null;
        if ($request->filled('edit')) {
            $editPost = Post::query()->find((int) $request->input('edit'));
        }

        $galeri = Galeri::query()->latest()->limit(8)->get();

        return view('pages.admin.berita', compact('posts', 'stats', 'editPost', 'galeri'));
    }

    public function galeri(): View
    {
        $stats = $this->sharedStats();
        $galeri = Galeri::query()->latest()->get();

        return view('pages.admin.galeri', compact('stats', 'galeri'));
    }

    public function storeGaleri(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'max:80'],
            'keterangan' => ['nullable', 'string', 'max:350'],
            'foto' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'tampil_beranda' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('foto');
        $filename = 'galeri-'.Str::uuid()->toString().'.'.$file->extension();
        $imagePath = $file->storeAs('galeri', $filename, 'public');

        Galeri::query()->create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'keterangan' => $validated['keterangan'] ?? null,
            'image_path' => $imagePath,
            'tampil_beranda' => $request->boolean('tampil_beranda', true),
        ]);

        return redirect()->route('admin.galeri')->with('status', 'Foto dokumentasi berhasil ditambahkan ke galeri publik.');
    }

    public function destroyGaleri(Galeri $galeri): RedirectResponse
    {
        $galeri->delete();

        return redirect()->route('admin.galeri')->with('status', 'Foto berhasil dihapus dari galeri.');
    }

    public function alumni(Request $request): View
    {
        $query = AlumniReview::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', (string) $request->string('status'));
        }

        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where(function ($sub) use ($q): void {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('angkatan', 'like', "%{$q}%")
                    ->orWhere('pekerjaan', 'like', "%{$q}%")
                    ->orWhere('ulasan', 'like', "%{$q}%");
            });
        }

        $ulasan = $query->get();
        $stats = $this->sharedStats();

        return view('pages.admin.alumni', compact('ulasan', 'stats'));
    }

    public function storeAlumni(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'angkatan' => ['required', 'string', 'max:20'],
            'pekerjaan' => ['nullable', 'string', 'max:150'],
            'ulasan' => ['required', 'string', 'max:1200'],
            'status' => ['required', 'in:terbit,menunggu'],
            'foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.alumni')->with('status', 'Ulasan alumni berhasil disimpan.');
    }

    public function toggleAlumniStatus(AlumniReview $alumni): RedirectResponse
    {
        $nextStatus = $alumni->status === 'terbit' ? 'menunggu' : 'terbit';
        $alumni->update(['status' => $nextStatus]);

        return redirect()->route('admin.alumni')->with('status', 'Status penayangan ulasan alumni diperbarui.');
    }

    public function destroyAlumni(AlumniReview $alumni): RedirectResponse
    {
        $alumni->delete();

        return redirect()->route('admin.alumni')->with('status', 'Ulasan alumni berhasil dihapus.');
    }

    public function akademik(): View
    {
        $stats = $this->sharedStats();

        $mapel = [
            ['no' => '01', 'nama' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'Sosial', 'fokus' => 'Pembiasaan ibadah dan akhlak'],
            ['no' => '02', 'nama' => 'Pendidikan Pancasila', 'kelompok' => 'Sosial', 'fokus' => 'Kewargaan, hak, dan kewajiban'],
            ['no' => '03', 'nama' => 'Bahasa Indonesia', 'kelompok' => 'Bahasa', 'fokus' => 'Membaca, menulis, dan berbicara'],
            ['no' => '04', 'nama' => 'Matematika', 'kelompok' => 'Sains', 'fokus' => 'Bilangan, aljabar, geometri, dan data'],
            ['no' => '05', 'nama' => 'Ilmu Pengetahuan Alam', 'kelompok' => 'Sains', 'fokus' => 'Fisika, biologi, dan kimia dasar'],
            ['no' => '06', 'nama' => 'Ilmu Pengetahuan Sosial', 'kelompok' => 'Sosial', 'fokus' => 'Geografi, sejarah, ekonomi, sosiologi'],
            ['no' => '07', 'nama' => 'Bahasa Inggris', 'kelompok' => 'Bahasa', 'fokus' => 'Menyimak, berbicara, membaca, menulis'],
            ['no' => '08', 'nama' => 'Pendidikan Jasmani', 'kelompok' => 'Keterampilan', 'fokus' => 'Olahraga dan kesehatan'],
            ['no' => '09', 'nama' => 'Seni dan Budaya', 'kelompok' => 'Keterampilan', 'fokus' => 'Seni rupa, musik, atau tari'],
            ['no' => '10', 'nama' => 'Informatika', 'kelompok' => 'Keterampilan', 'fokus' => 'Berpikir komputasional dan literasi digital'],
            ['no' => '11', 'nama' => 'Prakarya', 'kelompok' => 'Keterampilan', 'fokus' => 'Kerajinan, budidaya, dan rekayasa'],
        ];

        $identitas = [
            'NPSN' => '20540020',
            'Status & Bentuk' => 'Swasta — Sekolah Menengah Pertama (SMP)',
            'Naungan Yayasan' => 'YPLP DASMEN PGRI Jawa Timur',
            'Tahun Berdiri' => '1979 (SK: 421.3/2829/404.3.1/1979)',
            'Waktu Belajar' => 'Pagi hari, 6 hari kerja (Senin–Sabtu)',
            'Luas Tanah' => '660 m²',
            'Alamat Resmi' => 'Jl. Anggrek No. 109, Desa Kureksari, Kec. Waru, Kab. Sidoarjo 61256',
            'Koordinat Peta' => '-7.3619, 112.7327',
        ];

        return view('pages.admin.akademik', compact('stats', 'mapel', 'identitas'));
    }

    public function petugas(): View
    {
        $stats = $this->sharedStats();
        $users = User::query()->orderBy('id')->get();

        return view('pages.admin.petugas', compact('stats', 'users'));
    }

    public function storePetugas(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', 'in:admin,operator'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.petugas')->with('status', 'Akun petugas baru berhasil ditambahkan.');
    }

    public function destroyPetugas(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->id === $user->id) {
            return redirect()->route('admin.petugas')->with('status', 'Akun yang sedang aktif digunakan tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('admin.petugas')->with('status', 'Akun petugas berhasil dihapus.');
    }

    public function pengaturan(): View
    {
        $stats = $this->sharedStats();
        $settings = array_merge(
            $this->defaultSettings(),
            Setting::query()->pluck('value', 'key')->all()
        );

        return view('pages.admin.pengaturan', compact('stats', 'settings'));
    }

    public function updateStatusPendaftar(Request $request, Pendaftar $pendaftar): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:baru,dihubungi,diterima,ditolak'],
        ]);

        $pendaftar->update(['status' => $validated['status']]);

        $redirectTo = $request->input('redirect_to') === 'dashboard'
            ? route('admin')
            : route('admin.ppdb');

        return redirect()->to($redirectTo)->with('status', 'Status pendaftar diperbarui.');
    }

    public function destroyPendaftar(Pendaftar $pendaftar): RedirectResponse
    {
        $pendaftar->delete();

        return redirect()->route('admin.ppdb')->with('status', 'Data pendaftar dihapus.');
    }

    public function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['ID', 'Nama Siswa', 'Asal SD', 'Nama Wali', 'WhatsApp', 'Status', 'Catatan', 'Waktu Daftar']);

            foreach (Pendaftar::query()->orderBy('id')->cursor() as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->nama_siswa,
                    $row->asal_sd,
                    $row->nama_wali,
                    $row->whatsapp,
                    $row->status,
                    $row->catatan,
                    $row->created_at?->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, 'pendaftar-smp-pgri-4-waru.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function storePost(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['judul']).'-'.Str::lower(Str::random(5));

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = Str::uuid()->toString().'.'.$file->extension();
            $coverPath = $file->storeAs('covers', $filename, 'public');
        } elseif (! empty($validated['cover_preset']) && str_starts_with((string) $validated['cover_preset'], '/images/')) {
            $coverPath = (string) $validated['cover_preset'];
        }

        if ($request->filled('post_id')) {
            $post = Post::query()->findOrFail((int) $request->input('post_id'));
            $post->update([
                'judul' => $validated['judul'],
                'ringkasan' => $validated['ringkasan'] ?? null,
                'isi' => $validated['isi'],
                'cover_path' => $coverPath ?? $post->cover_path,
                'status' => $validated['status'],
                'published_at' => $validated['status'] === 'terbit' ? ($post->published_at ?? now()) : null,
            ]);

            return redirect()->route('admin.berita')->with('status', 'Berita berhasil diperbarui.');
        }

        Post::query()->create([
            'judul' => $validated['judul'],
            'slug' => $slug,
            'ringkasan' => $validated['ringkasan'] ?? null,
            'isi' => $validated['isi'],
            'cover_path' => $coverPath,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'terbit' ? now() : null,
            'user_id' => $request->user()?->id,
        ]);

        return redirect()->route('admin.berita')->with('status', 'Berita berhasil disimpan.');
    }

    public function togglePostStatus(Post $post): RedirectResponse
    {
        $nextStatus = $post->status === 'terbit' ? 'draf' : 'terbit';
        $post->update([
            'status' => $nextStatus,
            'published_at' => $nextStatus === 'terbit' ? ($post->published_at ?? now()) : null,
        ]);

        return redirect()->route('admin.berita')->with('status', 'Status publikasi berita diperbarui.');
    }

    public function destroyPost(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.berita')->with('status', 'Berita berhasil dihapus.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_sekolah' => ['sometimes', 'required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:120'],
            'npsn' => ['nullable', 'string', 'max:30'],
            'naungan' => ['nullable', 'string', 'max:150'],
            'sk_pendirian' => ['nullable', 'string', 'max:120'],
            'tahun_berdiri' => ['nullable', 'string', 'max:20'],
            'luas_tanah' => ['nullable', 'string', 'max:40'],
            'waktu_belajar' => ['nullable', 'string', 'max:120'],
            'telepon' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:120'],
            'instagram' => ['nullable', 'string', 'max:80'],
            'alamat' => ['nullable', 'string', 'max:250'],
            'kode_pos' => ['nullable', 'string', 'max:20'],
            'koordinat' => ['nullable', 'string', 'max:80'],
            'jam_layanan' => ['required', 'string', 'max:180'],
            'hero_eyebrow' => ['nullable', 'string', 'max:120'],
            'hero_judul' => ['nullable', 'string', 'max:200'],
            'hero_deskripsi' => ['nullable', 'string', 'max:500'],
            'visi_sekolah' => ['nullable', 'string', 'max:500'],
            'pengumuman_ppdb' => ['nullable', 'string', 'max:500'],
            'status_ppdb' => ['nullable', 'string', 'max:80'],
            'tahun_ajaran' => ['nullable', 'string', 'max:30'],
            'ppdb_bank_nama' => ['nullable', 'string', 'max:120'],
            'ppdb_bank_rekening' => ['nullable', 'string', 'max:120'],
            'ppdb_bank_atas_nama' => ['nullable', 'string', 'max:120'],
            'ppdb_catatan_bayar' => ['nullable', 'string', 'max:500'],
            'logo_sekolah' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'hero_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'brosur_ppdb' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $fileMappings = [
            'logo_sekolah' => 'logo_path',
            'hero_image' => 'hero_image_path',
            'brosur_ppdb' => 'brosur_ppdb_path',
        ];

        foreach ($fileMappings as $inputKey => $settingKey) {
            if ($request->hasFile($inputKey)) {
                $file = $request->file($inputKey);
                $filename = $inputKey.'-'.Str::uuid()->toString().'.'.$file->extension();
                $storedPath = $file->storeAs('site', $filename, 'public');
                Setting::setValue($settingKey, $storedPath);
            }
            unset($validated[$inputKey]);
        }

        if ($request->boolean('hapus_logo')) {
            Setting::setValue('logo_path', '');
        }
        if ($request->boolean('hapus_hero')) {
            Setting::setValue('hero_image_path', '');
        }
        if ($request->boolean('hapus_brosur')) {
            Setting::setValue('brosur_ppdb_path', '');
        }

        foreach ($validated as $key => $value) {
            Setting::setValue($key, (string) ($value ?? ''));
        }

        return redirect()->route('admin.pengaturan')->with('status', 'Pengaturan dan kustomisasi situs berhasil diperbarui.');
    }
}
