<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeri', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori', 80)->default('Kegiatan Sekolah');
            $table->text('keterangan')->nullable();
            $table->string('image_path');
            $table->boolean('tampil_beranda')->default(true);
            $table->timestamps();
        });

        Schema::create('alumni_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120);
            $table->string('angkatan', 20);
            $table->string('pekerjaan', 150)->nullable();
            $table->text('ulasan');
            $table->string('foto_path')->nullable();
            $table->string('status', 30)->default('menunggu')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_reviews');
        Schema::dropIfExists('galeri');
    }
};
