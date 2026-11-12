<?php

namespace App\Models;

use Database\Factories\PpdbPendaftaranFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

#[Table(name: 'ppdb_pendaftaran')]
#[Fillable([
    'ppdb_account_id',
    'ppdb_jalur_id',
    'no_pendaftaran',
    'tahun_ajaran',
    'nisn',
    'jenis_kelamin',
    'tempat_lahir',
    'agama',
    'alamat_lengkap',
    'kelurahan_desa',
    'kecamatan',
    'kabupaten_kota',
    'asal_sd',
    'npsn_sd',
    'tahun_lulus_sd',
    'anak_ke',
    'jumlah_saudara',
    'nama_ayah',
    'pekerjaan_ayah',
    'no_hp_ayah',
    'nama_ibu',
    'pekerjaan_ibu',
    'no_hp_ibu',
    'nama_wali',
    'hubungan_wali',
    'penghasilan_ortu',
    'kuesioner',
    'jalur_dipilih_at',
    'data_diri_lengkap_at',
    'berkas_lengkap_at',
    'kuesioner_selesai_at',
    'status_seleksi',
    'catatan_admin',
    'pengumuman_at',
])]
class PpdbPendaftaran extends Model
{
    /** @use HasFactory<PpdbPendaftaranFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    public const REQUIRED_BERKAS = [
        'kk' => 'Kartu Keluarga (KK)',
        'akta' => 'Akta Kelahiran',
        'skl' => 'SKL / Rapor SD / Ijazah',
        'foto' => 'Pas Foto Terbaru 3x4',
    ];

    /**
     * @var array<string, string>
     */
    public const BERKAS_LABELS = [
        'kk' => 'Kartu Keluarga (KK)',
        'akta' => 'Akta Kelahiran',
        'skl' => 'SKL / Rapor SD / Ijazah',
        'foto' => 'Pas Foto Terbaru 3x4',
        'prestasi' => 'Sertifikat / Piagam Prestasi (Opsional)',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kuesioner' => 'array',
            'anak_ke' => 'integer',
            'jumlah_saudara' => 'integer',
            'jalur_dipilih_at' => 'datetime',
            'data_diri_lengkap_at' => 'datetime',
            'berkas_lengkap_at' => 'datetime',
            'kuesioner_selesai_at' => 'datetime',
            'pengumuman_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<PpdbAccount, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(PpdbAccount::class, 'ppdb_account_id');
    }

    /**
     * @return BelongsTo<PpdbJalur, $this>
     */
    public function jalur(): BelongsTo
    {
        return $this->belongsTo(PpdbJalur::class, 'ppdb_jalur_id');
    }

    /**
     * @return HasOne<PpdbPembayaran, $this>
     */
    public function pembayaran(): HasOne
    {
        return $this->hasOne(PpdbPembayaran::class, 'ppdb_pendaftaran_id')->latestOfMany('id');
    }

    /**
     * @return HasMany<PpdbPembayaran, $this>
     */
    public function riwayatPembayaran(): HasMany
    {
        return $this->hasMany(PpdbPembayaran::class, 'ppdb_pendaftaran_id')->latest('id');
    }

    /**
     * @return HasOne<PpdbPembayaran, $this>
     */
    public function latestPembayaran(): HasOne
    {
        return $this->hasOne(PpdbPembayaran::class, 'ppdb_pendaftaran_id')->latestOfMany('id');
    }

    /**
     * @return HasMany<PpdbBerkas, $this>
     */
    public function berkas(): HasMany
    {
        return $this->hasMany(PpdbBerkas::class, 'ppdb_pendaftaran_id');
    }

    public function getCatatanPanitiaAttribute(): ?string
    {
        return $this->catatan_admin;
    }

    public function getKuesionerLengkapAtAttribute(): ?Carbon
    {
        return $this->kuesioner_selesai_at;
    }

    public function hasSelectedJalur(): bool
    {
        return $this->ppdb_jalur_id !== null;
    }

    public function hasChosenJalur(): bool
    {
        return $this->hasSelectedJalur();
    }

    public function activePayment(): ?PpdbPembayaran
    {
        if ($this->relationLoaded('pembayaran')) {
            return $this->pembayaran;
        }

        if ($this->relationLoaded('latestPembayaran')) {
            return $this->latestPembayaran;
        }

        return $this->pembayaran()->first();
    }

    public function isPaymentConfirmed(): bool
    {
        if ($this->jalur && $this->jalur->isGratis()) {
            return true;
        }

        if ($this->relationLoaded('pembayaran') && $this->pembayaran !== null) {
            return $this->pembayaran->status === 'lunas';
        }

        return $this->riwayatPembayaran()->where('status', 'lunas')->exists();
    }

    public function hasCompletedPaymentStep(): bool
    {
        if (! $this->hasSelectedJalur()) {
            return false;
        }

        if ($this->isPaymentConfirmed()) {
            return true;
        }

        $payment = $this->activePayment();

        return $payment !== null && in_array($payment->status, ['menunggu', 'menunggu_verifikasi', 'lunas'], true);
    }

    public function isPaymentPendingVerification(): bool
    {
        if ($this->isPaymentConfirmed()) {
            return false;
        }

        return in_array($this->activePayment()?->status, ['menunggu', 'menunggu_verifikasi'], true);
    }

    public function canAccessBiodata(): bool
    {
        return $this->hasCompletedPaymentStep();
    }

    public function isDataDiriComplete(): bool
    {
        return $this->canAccessBiodata() && $this->data_diri_lengkap_at !== null;
    }

    public function hasCompletedBiodata(): bool
    {
        return $this->isDataDiriComplete();
    }

    public function canAccessBerkas(): bool
    {
        return $this->hasCompletedBiodata();
    }

    public function hasRejectedBerkas(): bool
    {
        $berkasList = $this->relationLoaded('berkas') ? $this->berkas : $this->berkas()->get();

        return $berkasList->contains(fn (PpdbBerkas $b): bool => in_array($b->status, ['ditolak', 'revisi'], true));
    }

    public function hasUploadedAllRequiredBerkas(): bool
    {
        $berkasList = $this->relationLoaded('berkas') ? $this->berkas : $this->berkas()->get();
        $indexed = $berkasList->keyBy('jenis');

        foreach (array_keys(self::REQUIRED_BERKAS) as $jenis) {
            $item = $indexed->get($jenis);
            if ($item === null || in_array($item->status, ['ditolak', 'revisi'], true)) {
                return false;
            }
        }

        return true;
    }

    public function hasCompletedBerkas(): bool
    {
        return $this->hasUploadedAllRequiredBerkas();
    }

    public function canAccessKuesioner(): bool
    {
        return $this->hasCompletedBerkas();
    }

    public function areAllBerkasVerified(): bool
    {
        $berkasList = $this->relationLoaded('berkas') ? $this->berkas : $this->berkas()->get();
        $indexed = $berkasList->keyBy('jenis');

        foreach (array_keys(self::REQUIRED_BERKAS) as $jenis) {
            $item = $indexed->get($jenis);
            if ($item === null || $item->status !== 'valid') {
                return false;
            }
        }

        return true;
    }

    public function isKuesionerComplete(): bool
    {
        return $this->hasUploadedAllRequiredBerkas() && $this->kuesioner_selesai_at !== null;
    }

    public function hasCompletedKuesioner(): bool
    {
        return $this->isKuesionerComplete();
    }

    public function isKartuPesertaUnlocked(): bool
    {
        return $this->isKuesionerComplete();
    }

    public function canAccessKartuPeserta(): bool
    {
        return $this->isKartuPesertaUnlocked();
    }

    public function statusSeleksiLabel(): string
    {
        return match ($this->status_seleksi) {
            'terverifikasi' => 'Berkas Terverifikasi',
            'diterima' => 'Diterima',
            'ditolak' => 'Belum Diterima',
            default => 'Dalam Proses Verifikasi',
        };
    }

    /**
     * @return array<int, array{
     *     step: int,
     *     number: int,
     *     key: string,
     *     label: string,
     *     title: string,
     *     state: string,
     *     summary: string,
     *     subtitle: string,
     *     route: string
     * }>
     */
    public function stepStates(): array
    {
        $jalurDone = $this->hasSelectedJalur();
        $payment = $this->activePayment();
        $payDone = $this->hasCompletedPaymentStep();
        $payConfirmed = $this->isPaymentConfirmed();
        $payRejected = ! $payConfirmed && $payment?->status === 'ditolak';
        $dataDone = $this->isDataDiriComplete();
        $berkasUploaded = $this->hasUploadedAllRequiredBerkas();
        $berkasRejected = $this->hasRejectedBerkas();
        $kuesionerDone = $this->isKuesionerComplete();

        $step1State = $jalurDone ? 'completed' : 'active';

        if (! $jalurDone) {
            $step2State = 'locked';
            $step2Summary = 'Pilih jalur pendaftaran terlebih dahulu.';
        } elseif ($payRejected) {
            $step2State = 'active';
            $step2Summary = 'Bukti ditolak panitia. Unggah ulang atau konfirmasi ke TU.';
        } elseif ($payDone) {
            $step2State = 'completed';
            $step2Summary = $payConfirmed
                ? ($payment?->metode === 'gratis' ? 'Gratis / terkonfirmasi otomatis.' : 'Pembayaran telah dikonfirmasi.')
                : 'Konfirmasi pembayaran terkirim (menunggu verifikasi TU).';
        } else {
            $step2State = 'active';
            $step2Summary = 'Selesaikan konfirmasi pembayaran pendaftaran.';
        }

        $step3State = ! $payDone ? 'locked' : ($dataDone ? 'completed' : 'active');
        $step3Summary = ! $payDone
            ? 'Terbuka setelah konfirmasi pembayaran.'
            : ($dataDone ? 'Biodata siswa dan orang tua lengkap.' : 'Lengkapi data diri, orang tua, dan SD asal.');

        if (! $dataDone) {
            $step4State = 'locked';
            $step4Summary = 'Terbuka setelah biodata diisi lengkap.';
        } elseif ($berkasRejected) {
            $step4State = 'active';
            $step4Summary = 'Ada berkas perlu diperbaiki / diunggah ulang.';
        } elseif ($berkasUploaded) {
            $step4State = 'completed';
            $step4Summary = $this->areAllBerkasVerified() ? 'Seluruh berkas telah divalidasi panitia.' : '4 berkas terunggah (menunggu verifikasi panitia).';
        } else {
            $step4State = 'active';
            $step4Summary = 'Unggah KK, Akta, SKL/Rapor, dan Pas Foto.';
        }

        $step5State = ! $berkasUploaded ? 'locked' : ($kuesionerDone ? 'completed' : 'active');
        $step5Summary = ! $berkasUploaded
            ? 'Terbuka setelah 4 berkas utama diunggah.'
            : ($kuesionerDone ? 'Kuesioner selesai. Kartu Peserta tersedia.' : 'Isi kuesioner singkat untuk menyelesaikan pendaftaran.');

        if (! $kuesionerDone) {
            $step6State = 'locked';
            $step6Summary = 'Tersedia setelah seluruh tahap pendaftaran selesai.';
        } elseif (in_array($this->status_seleksi, ['diterima', 'ditolak', 'terverifikasi'], true)) {
            $step6State = 'completed';
            $step6Summary = 'Hasil verifikasi/seleksi telah diperbarui panitia.';
        } else {
            $step6State = 'completed';
            $step6Summary = 'Kartu Peserta siap dicetak & pantau pengumuman seleksi.';
        }

        return [
            1 => [
                'step' => 1,
                'number' => 1,
                'key' => 'jalur',
                'label' => 'Pilih Jalur',
                'title' => 'Pendaftaran Jalur',
                'state' => $step1State,
                'summary' => $jalurDone ? ($this->jalur?->nama ?? 'Jalur dipilih') : 'Pilih jalur pendaftaran yang sedang dibuka.',
                'subtitle' => $jalurDone ? ($this->jalur?->nama ?? 'Jalur dipilih') : 'Pilih jalur pendaftaran yang sedang dibuka.',
                'route' => 'ppdb.portal.jalur',
            ],
            2 => [
                'step' => 2,
                'number' => 2,
                'key' => 'pembayaran',
                'label' => 'Bayar Pendaftaran',
                'title' => 'Pembayaran / Konfirmasi',
                'state' => $step2State,
                'summary' => $step2Summary,
                'subtitle' => $step2Summary,
                'route' => 'ppdb.portal.pembayaran',
            ],
            3 => [
                'step' => 3,
                'number' => 3,
                'key' => 'data-diri',
                'label' => 'Isi Data Diri',
                'title' => 'Pengisian Data Diri',
                'state' => $step3State,
                'summary' => $step3Summary,
                'subtitle' => $step3Summary,
                'route' => 'ppdb.portal.data-diri',
            ],
            4 => [
                'step' => 4,
                'number' => 4,
                'key' => 'berkas',
                'label' => 'Unggah Berkas',
                'title' => 'Unggah Berkas Persyaratan',
                'state' => $step4State,
                'summary' => $step4Summary,
                'subtitle' => $step4Summary,
                'route' => 'ppdb.portal.berkas',
            ],
            5 => [
                'step' => 5,
                'number' => 5,
                'key' => 'kuesioner',
                'label' => 'Kuesioner',
                'title' => 'Kuesioner Calon Siswa',
                'state' => $step5State,
                'summary' => $step5Summary,
                'subtitle' => $step5Summary,
                'route' => 'ppdb.portal.kuesioner',
            ],
            6 => [
                'step' => 6,
                'number' => 6,
                'key' => 'pengumuman',
                'label' => 'Pengumuman',
                'title' => 'Kartu Peserta & Pengumuman',
                'state' => $step6State,
                'summary' => $step6Summary,
                'subtitle' => $step6Summary,
                'route' => 'ppdb.portal.kartu-peserta',
            ],
        ];
    }

    /**
     * @return array{step: int, number: int, key: string, label: string, title: string, state: string, summary: string, subtitle: string, route: string}
     */
    public function nextActionStep(): array
    {
        $steps = $this->stepStates();

        foreach ($steps as $item) {
            if ($item['state'] !== 'completed') {
                return $item;
            }
        }

        return $steps[6];
    }

    public static function generateNoPendaftaran(int $id): string
    {
        return 'PPDB-'.now()->year.'-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }
}
