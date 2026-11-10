<?php

namespace App\Models;

use Database\Factories\PendaftarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table(name: 'pendaftar')]
#[Fillable([
    'nama_siswa',
    'nama_wali',
    'asal_sd',
    'whatsapp',
    'catatan',
    'status',
    'sumber',
    'ip',
])]
class Pendaftar extends Model
{
    /** @use HasFactory<PendaftarFactory> */
    use HasFactory, SoftDeletes;
}
