<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'ppdb_pembayaran')]
#[Fillable([
    'ppdb_pendaftaran_id',
    'kode_invoice',
    'nominal',
    'metode',
    'status',
    'bukti_path',
    'catatan_siswa',
    'catatan_admin',
    'verified_by',
    'verified_at',
])]
class PpdbPembayaran extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<PpdbPendaftaran, $this>
     */
    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(PpdbPendaftaran::class, 'ppdb_pendaftaran_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function formattedNominal(): string
    {
        if ($this->nominal <= 0) {
            return 'Gratis (Rp 0)';
        }

        return 'Rp '.number_format($this->nominal, 0, ',', '.');
    }

    public function nominalLabel(): string
    {
        return $this->formattedNominal();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'menunggu', 'menunggu_verifikasi' => 'Menunggu Verifikasi TU',
            'lunas' => 'Terkonfirmasi / Lunas',
            'ditolak' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function metodeLabel(): string
    {
        return match ($this->metode) {
            'gratis' => 'Bebas Biaya Pendaftaran',
            'transfer' => 'Transfer Rekening Sekolah',
            'kantor', 'tunai_tu' => 'Bayar di Kantor TU Sekolah',
            default => ucfirst($this->metode),
        };
    }
}
