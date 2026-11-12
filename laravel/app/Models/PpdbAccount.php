<?php

namespace App\Models;

use Database\Factories\PpdbAccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Table(name: 'ppdb_accounts')]
#[Fillable([
    'nama_lengkap',
    'email',
    'nik',
    'tanggal_lahir',
    'whatsapp',
    'password',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class PpdbAccount extends Authenticatable
{
    /** @use HasFactory<PpdbAccountFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasOne<PpdbPendaftaran, $this>
     */
    public function pendaftaran(): HasOne
    {
        return $this->hasOne(PpdbPendaftaran::class, 'ppdb_account_id');
    }

    public function whatsappLink(): string
    {
        $digits = (string) preg_replace('/[^0-9]/', '', $this->whatsapp);

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62'.$digits;
        }

        return 'https://wa.me/'.$digits;
    }
}
