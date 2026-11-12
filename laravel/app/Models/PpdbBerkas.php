<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'ppdb_berkas')]
#[Fillable([
    'ppdb_pendaftaran_id',
    'jenis',
    'file_path',
    'original_name',
    'mime_type',
    'file_size',
    'status',
    'catatan_admin',
    'verified_by',
    'verified_at',
])]
class PpdbBerkas extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
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

    public function label(): string
    {
        return PpdbPendaftaran::BERKAS_LABELS[$this->jenis] ?? PpdbPendaftaran::REQUIRED_BERKAS[$this->jenis] ?? strtoupper($this->jenis);
    }

    public function jenisLabel(): string
    {
        return $this->label();
    }

    public function fileSizeLabel(): string
    {
        if ($this->file_size < 1024) {
            return $this->file_size.' B';
        }

        if ($this->file_size < 1048576) {
            return number_format($this->file_size / 1024, 1, ',', '.').' KB';
        }

        return number_format($this->file_size / 1048576, 2, ',', '.').' MB';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'valid' => 'Valid',
            'ditolak', 'revisi' => 'Perlu Perbaikan',
            default => 'Menunggu Verifikasi',
        };
    }
}
