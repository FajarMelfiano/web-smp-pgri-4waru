<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_jalur', function (Blueprint $table): void {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 40)->unique();
            $table->text('deskripsi')->nullable();
            $table->text('persyaratan')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('biaya_daftar')->default(0);
            $table->unsignedInteger('kuota')->nullable();
            $table->boolean('dibuka')->default(true)->index();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('ppdb_accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_lengkap', 120);
            $table->string('email', 150)->unique();
            $table->string('nik', 16)->unique();
            $table->date('tanggal_lahir');
            $table->string('whatsapp', 25);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('ppdb_pendaftaran', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ppdb_account_id')->unique()->constrained('ppdb_accounts')->cascadeOnDelete();
            $table->foreignId('ppdb_jalur_id')->nullable()->constrained('ppdb_jalur')->nullOnDelete();
            $table->string('no_pendaftaran', 30)->nullable()->unique();
            $table->string('tahun_ajaran', 20)->default('2026/2027');

            $table->string('nisn', 20)->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('tempat_lahir', 80)->nullable();
            $table->string('agama', 30)->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('kelurahan_desa', 80)->nullable();
            $table->string('kecamatan', 80)->nullable();
            $table->string('kabupaten_kota', 80)->nullable();
            $table->string('asal_sd', 140)->nullable();
            $table->string('npsn_sd', 20)->nullable();
            $table->string('tahun_lulus_sd', 10)->nullable();
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('jumlah_saudara')->nullable();

            $table->string('nama_ayah', 120)->nullable();
            $table->string('pekerjaan_ayah', 80)->nullable();
            $table->string('no_hp_ayah', 25)->nullable();
            $table->string('nama_ibu', 120)->nullable();
            $table->string('pekerjaan_ibu', 80)->nullable();
            $table->string('no_hp_ibu', 25)->nullable();
            $table->string('nama_wali', 120)->nullable();
            $table->string('hubungan_wali', 60)->nullable();
            $table->string('penghasilan_ortu', 60)->nullable();

            $table->json('kuesioner')->nullable();

            $table->timestamp('jalur_dipilih_at')->nullable();
            $table->timestamp('data_diri_lengkap_at')->nullable();
            $table->timestamp('berkas_lengkap_at')->nullable();
            $table->timestamp('kuesioner_selesai_at')->nullable();

            $table->string('status_seleksi', 30)->default('proses')->index();
            $table->text('catatan_admin')->nullable();
            $table->timestamp('pengumuman_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ppdb_pembayaran', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ppdb_pendaftaran_id')->constrained('ppdb_pendaftaran')->cascadeOnDelete();
            $table->string('kode_invoice', 40)->unique();
            $table->unsignedInteger('nominal')->default(0);
            $table->string('metode', 30)->default('transfer');
            $table->string('status', 30)->default('menunggu_pembayaran')->index();
            $table->string('bukti_path')->nullable();
            $table->text('catatan_siswa')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ppdb_berkas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ppdb_pendaftaran_id')->constrained('ppdb_pendaftaran')->cascadeOnDelete();
            $table->string('jenis', 30);
            $table->string('file_path');
            $table->string('original_name', 180);
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size')->default(0);
            $table->string('status', 30)->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['ppdb_pendaftaran_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_berkas');
        Schema::dropIfExists('ppdb_pembayaran');
        Schema::dropIfExists('ppdb_pendaftaran');
        Schema::dropIfExists('ppdb_accounts');
        Schema::dropIfExists('ppdb_jalur');
    }
};
