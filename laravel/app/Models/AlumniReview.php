<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'nama',
    'angkatan',
    'pekerjaan',
    'ulasan',
    'foto_path',
    'status',
])]
class AlumniReview extends Model
{
    protected $table = 'alumni_reviews';
}
