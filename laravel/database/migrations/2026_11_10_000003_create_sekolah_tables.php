<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftar', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa');
            $table->string('nama_wali');
            $table->string('asal_sd');
            $table->string('whatsapp', 20);
            $table->text('catatan')->nullable();
            $table->string('status')->default('baru')->index();
            $table->string('sumber')->default('web');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('isi');
            $table->string('cover_path')->nullable();
            $table->string('status')->default('draf')->index();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('pendaftar');
    }
};
