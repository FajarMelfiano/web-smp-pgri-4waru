<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'judul',
    'kategori',
    'keterangan',
    'image_path',
    'tampil_beranda',
])]
class Galeri extends Model
{
    protected $table = 'galeri';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tampil_beranda' => 'boolean',
        ];
    }
}
