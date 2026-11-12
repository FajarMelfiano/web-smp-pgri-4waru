<?php

namespace Database\Seeders;

use App\Models\PpdbJalur;
use Illuminate\Database\Seeder;

class PpdbJalurSeeder extends Seeder
{
    public function run(): void
    {
        PpdbJalur::query()->updateOrCreate(
            ['kode' => 'inden'],
            [
                'nama' => 'Jalur Inden',
                'deskripsi' => 'Gelombang pendaftaran awal bagi lulusan SD/MI di kawasan Waru dan Sidoarjo dengan verifikasi prioritas.',
                'persyaratan' => 'Mengisi biodata online dan melampirkan KK, Akta Kelahiran, fotokopi rapor/surat keterangan kelas VI, serta pas foto 3x4.',
                'tanggal_mulai' => '2026-10-01',
                'tanggal_selesai' => '2027-02-28',
                'biaya_daftar' => 0,
                'kuota' => 60,
                'dibuka' => true,
                'urutan' => 1,
            ]
        );

        PpdbJalur::query()->updateOrCreate(
            ['kode' => 'prestasi'],
            [
                'nama' => 'Jalur Prestasi',
                'deskripsi' => 'Jalur penerimaan bagi calon siswa berprestasi akademik maupun non-akademik tingkat sekolah atau kecamatan.',
                'persyaratan' => 'Melampirkan KK, Akta Kelahiran, SKL/Rapor SD, pas foto 3x4, dan bukti sertifikat prestasi saat verifikasi di sekolah.',
                'tanggal_mulai' => '2026-11-01',
                'tanggal_selesai' => '2027-04-30',
                'biaya_daftar' => 0,
                'kuota' => 40,
                'dibuka' => true,
                'urutan' => 2,
            ]
        );

        PpdbJalur::query()->updateOrCreate(
            ['kode' => 'reguler'],
            [
                'nama' => 'Jalur Reguler',
                'deskripsi' => 'Penerimaan peserta didik baru gelombang reguler Tahun Ajaran 2026/2027 SMP PGRI 4 Waru.',
                'persyaratan' => 'Melampirkan KK, Akta Kelahiran, SKL/Ijazah SD sederajat, dan pas foto terbaru.',
                'tanggal_mulai' => '2026-11-01',
                'tanggal_selesai' => '2027-07-15',
                'biaya_daftar' => 100000,
                'kuota' => 120,
                'dibuka' => true,
                'urutan' => 3,
            ]
        );
    }
}
