<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\PpdbJalurFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'ppdb_jalur')]
#[Fillable([
    'nama',
    'kode',
    'deskripsi',
    'persyaratan',
    'tanggal_mulai',
    'tanggal_selesai',
    'biaya_daftar',
    'kuota',
    'dibuka',
    'urutan',
])]
class PpdbJalur extends Model
{
    /** @use HasFactory<PpdbJalurFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    private const BULAN_ID = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'biaya_daftar' => 'integer',
            'kuota' => 'integer',
            'dibuka' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    /**
     * @return HasMany<PpdbPendaftaran, $this>
     */
    public function pendaftaran(): HasMany
    {
        return $this->hasMany(PpdbPendaftaran::class, 'ppdb_jalur_id');
    }

    public function terisiCount(): int
    {
        if (array_key_exists('pendaftaran_count', $this->attributes)) {
            return (int) $this->attributes['pendaftaran_count'];
        }

        return $this->pendaftaran()->count();
    }

    public function isQuotaFull(): bool
    {
        if ($this->kuota === null) {
            return false;
        }

        return $this->terisiCount() >= $this->kuota;
    }

    public function isWithinPeriod(): bool
    {
        $today = now()->toDateString();

        return $this->tanggal_mulai !== null
            && $this->tanggal_selesai !== null
            && $today >= $this->tanggal_mulai->toDateString()
            && $today <= $this->tanggal_selesai->toDateString();
    }

    public function isOpenNow(): bool
    {
        return $this->dibuka && $this->isWithinPeriod() && ! $this->isQuotaFull();
    }

    public function statusLabel(): string
    {
        if (! $this->dibuka) {
            return 'Ditutup';
        }

        $today = now()->toDateString();

        if ($this->tanggal_mulai !== null && $today < $this->tanggal_mulai->toDateString()) {
            return 'Segera Dibuka';
        }

        if ($this->tanggal_selesai !== null && $today > $this->tanggal_selesai->toDateString()) {
            return 'Ditutup';
        }

        if ($this->isQuotaFull()) {
            return 'Kuota Penuh';
        }

        return 'Dibuka';
    }

    public function isGratis(): bool
    {
        return $this->biaya_daftar <= 0;
    }

    public function formattedBiaya(): string
    {
        if ($this->biaya_daftar <= 0) {
            return 'Gratis (Rp 0)';
        }

        return 'Rp '.number_format($this->biaya_daftar, 0, ',', '.');
    }

    public function biayaLabel(): string
    {
        return $this->formattedBiaya();
    }

    public function formattedPeriode(): string
    {
        if ($this->tanggal_mulai === null || $this->tanggal_selesai === null) {
            return '-';
        }

        return self::formatTanggal($this->tanggal_mulai).' s.d. '.self::formatTanggal($this->tanggal_selesai);
    }

    public function periodeLabel(): string
    {
        return $this->formattedPeriode();
    }

    public static function formatTanggal(CarbonInterface $tanggal): string
    {
        $bulan = self::BULAN_ID[$tanggal->month] ?? $tanggal->format('F');

        return $tanggal->day.' '.$bulan.' '.$tanggal->year;
    }
}
