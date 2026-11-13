#!/usr/bin/env php
<?php
/**
 * Isi data demo (fiktif) ke database lokal untuk keperluan tangkapan layar
 * guidebook. Tidak menyentuh data yang sudah ada; semua akun demo memakai
 * domain @contoh.id, NIK/WA berpola nol, dan nama rekaan.
 *
 * Jalankan: php seed-demo.php   (dari folder guidebook/build)
 * Hapus:     php seed-demo.php --purge
 */

$root = '/home/melfiano/Documents/web-smp-pgri-4waru/laravel';
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PpdbAccount;
use App\Models\PpdbBerkas;
use App\Models\PpdbJalur;
use App\Models\PpdbPembayaran;
use App\Models\PpdbPendaftaran;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

const MARK = 'demo-guidebook';

if (in_array('--purge', $argv, true)) {
    $ids = PpdbAccount::where('email', 'like', '%@contoh.id')->pluck('id');
    $regs = PpdbPendaftaran::whereIn('ppdb_account_id', $ids)->pluck('id');
    PpdbBerkas::whereIn('ppdb_pendaftaran_id', $regs)->delete();
    PpdbPembayaran::whereIn('ppdb_pendaftaran_id', $regs)->delete();
    PpdbPendaftaran::whereIn('id', $regs)->delete();
    PpdbAccount::whereIn('id', $ids)->delete();
    echo "purged: accounts={$ids->count()} registrations={$regs->count()}\n";
    exit(0);
}

$docs = '/tmp/opencode/mockdocs';
$docFor = [
    'kk' => $docs.'/kk.png',
    'akta' => $docs.'/akta.png',
    'skl' => $docs.'/skl.png',
    'foto' => $docs.'/foto.png',
    'prestasi' => $docs.'/prestasi.png',
];
foreach ($docFor as $jenis => $path) {
    if (! is_file($path)) {
        fwrite(STDERR, "dokumen contoh hilang: {$path} (jalankan make-docs.py)\n");
        exit(1);
    }
}

$jalur = PpdbJalur::pluck('id', 'kode');

/** @var array<int, array<string, mixed>> $plan */
$plan = [
    [
        'nama' => 'Anindita Kirana Wardani', 'email' => 'anindita.kirana@contoh.id',
        'jk' => 'P', 'lahir' => 'Sidoarjo, 14 Maret 2013', 'wa' => '6281200000002',
        'nik' => '3515000000000002', 'jalur' => 'prestasi', 'sd' => 'SD Negeri Kureksari 1',
        'npsn_sd' => '20540031', 'tgl' => '2026-10-04',
        'kues' => ['sumber_info' => 'Brosur sekolah', 'alasan_memilih' => 'Lokasi dekat rumah dan pembinaan keagamaannya baik.'],
        'bayar' => ['status' => 'lunas', 'metode' => 'gratis', 'nominal' => 0, 'bukti' => false],
        'berkas' => ['kk', 'akta', 'skl', 'foto', 'prestasi'],
        'seleksi' => 'diterima', 'pengumuman' => '2026-11-05 09:00:00',
        'catatan' => 'Selamat! Silakan daftar ulang di Loket TU pada 18-20 November 2026 dengan membawa berkas asli.',
    ],
    [
        'nama' => 'Bagas Kurniawan Saputra', 'email' => 'bagas.kurniawan@contoh.id',
        'jk' => 'L', 'lahir' => 'Sidoarjo, 2 Juni 2013', 'wa' => '6281200000003',
        'nik' => '3515000000000003', 'jalur' => 'reguler', 'sd' => 'SD Negeri Waru 2',
        'npsn_sd' => '20540045', 'tgl' => '2026-10-06',
        'kues' => ['sumber_info' => 'Kakak kelas', 'alasan_memilih' => 'Banyak lulusan yang melanjutkan ke SMA negeri favorit.'],
        'bayar' => ['status' => 'lunas', 'metode' => 'transfer', 'nominal' => 100000, 'bukti' => true],
        'berkas' => ['kk', 'akta', 'skl', 'foto'],
        'seleksi' => 'terverifikasi', 'catatan' => 'Berkas lengkap, menunggu rapat seleksi.',
    ],
    [
        'nama' => 'Zahra Alifah Rahmawati', 'email' => 'zahra.alifah@contoh.id',
        'jk' => 'P', 'lahir' => 'Sidoarjo, 21 Juli 2013', 'wa' => '6281200000004',
        'nik' => '3515000000000004', 'jalur' => 'reguler', 'sd' => 'SD Negeri Kureksari 2',
        'npsn_sd' => '20540032', 'tgl' => '2026-10-18',
        'kues' => ['sumber_info' => 'Media sosial sekolah', 'alasan_memilih' => 'Program ekstrakurikuler tari dan paskibra yang aktif.'],
        'bayar' => ['status' => 'menunggu', 'metode' => 'transfer', 'nominal' => 100000, 'bukti' => true],
        'berkas' => ['kk', 'akta', 'skl', 'foto'],
        'seleksi' => 'proses',
    ],
    [
        'nama' => 'Rizky Wahyu Pratama', 'email' => 'rizky.wahyu@contoh.id',
        'jk' => 'L', 'lahir' => 'Sidoarjo, 9 Januari 2013', 'wa' => '6281200000005',
        'nik' => '3515000000000005', 'jalur' => 'inden', 'sd' => 'SD Negeri Tambakoso',
        'npsn_sd' => '20540051', 'tgl' => '2026-10-27',
        'kues' => ['sumber_info' => 'Tetangga', 'alasan_memilih' => 'Pendaftaran awal supaya kuota tidak habis.'],
        'bayar' => ['status' => 'lunas', 'metode' => 'gratis', 'nominal' => 0, 'bukti' => false],
        'berkas' => ['kk', 'akta'],
        'seleksi' => 'proses',
    ],
    [
        'nama' => 'Salsabila Maulida Husna', 'email' => 'salsabila.maulida@contoh.id',
        'jk' => 'P', 'lahir' => 'Sidoarjo, 30 September 2013', 'wa' => '6281200000006',
        'nik' => '3515000000000006', 'jalur' => 'prestasi', 'sd' => 'SD Islam Al-Azhar Waru',
        'npsn_sd' => '20540066', 'tgl' => '2026-10-02',
        'kues' => ['sumber_info' => 'Guru les', 'alasan_memilih' => 'Ingin melanjutkan prestasi olimpiade matematika.'],
        'bayar' => ['status' => 'lunas', 'metode' => 'gratis', 'nominal' => 0, 'bukti' => false],
        'berkas' => ['kk', 'akta', 'skl', 'foto', 'prestasi'],
        'seleksi' => 'ditolak', 'pengumuman' => '2026-11-05 09:00:00',
        'catatan' => 'Jarak domisili di luar prioritas zonasi tahun ini. Silakan menghubungi TU bila ingin mendaftar ulang jalur reguler.',
    ],
    [
        'nama' => 'Dimas Aditya Nugraha', 'email' => 'dimas.aditya@contoh.id',
        'jk' => 'L', 'lahir' => 'Sidoarjo, 11 November 2013', 'wa' => '6281200000007',
        'nik' => '3515000000000007', 'jalur' => 'reguler', 'sd' => '-',
        'npsn_sd' => '-', 'tgl' => '2026-11-09',
        'kues' => [], 'bayar' => null, 'berkas' => [], 'seleksi' => 'proses',
    ],
    [
        'nama' => 'Nabila Putri Ramadhani', 'email' => 'nabila.putri@contoh.id',
        'jk' => 'P', 'lahir' => 'Sidoarjo, 27 April 2013', 'wa' => '6281200000008',
        'nik' => '3515000000000008', 'jalur' => 'inden', 'sd' => 'SD Negeri Berbek 1',
        'npsn_sd' => '20540058', 'tgl' => '2026-09-28',
        'kues' => ['sumber_info' => 'Papan reklame', 'alasan_memilih' => 'Sudah mendaftar lebih awal agar dapat kelas pagi.'],
        'bayar' => ['status' => 'lunas', 'metode' => 'transfer', 'nominal' => 100000, 'bukti' => true],
        'berkas' => ['kk', 'akta', 'skl', 'foto'],
        'seleksi' => 'diterima', 'pengumuman' => '2026-11-05 09:00:00',
        'catatan' => 'Selamat! Silakan daftar ulang di Loket TU pada 18-20 November 2026.',
    ],
    [
        'nama' => 'Andre Setiawan Pratama', 'email' => 'andre.setiawan@contoh.id',
        'jk' => 'L', 'lahir' => 'Sidoarjo, 6 Agustus 2013', 'wa' => '6281200000009',
        'nik' => '3515000000000009', 'jalur' => 'reguler', 'sd' => 'SD Negeri Waru 1',
        'npsn_sd' => '20540044', 'tgl' => '2026-10-21',
        'kues' => ['sumber_info' => 'Brosur sekolah', 'alasan_memilih' => 'Bersama dua teman sekelas agar satu lingkungan belajar.'],
        'bayar' => ['status' => 'menunggu', 'metode' => 'transfer', 'nominal' => 100000, 'bukti' => true],
        'berkas' => ['kk', 'akta', 'skl', 'foto'],
        'seleksi' => 'proses',
    ],
];

$seq = 1;
foreach ($plan as $p) {
    $seq++;
    $akun = PpdbAccount::forceCreate([
        'nama_lengkap' => $p['nama'],
        'email' => $p['email'],
        'nik' => $p['nik'],
        'tanggal_lahir' => '2013-03-14',
        'whatsapp' => $p['wa'],
        'password' => Hash::make('DemoSiswa2026'),
    ]);

    $reg = PpdbPendaftaran::forceCreate([
        'ppdb_account_id' => $akun->id,
        'ppdb_jalur_id' => $jalur[$p['jalur']] ?? null,
        'no_pendaftaran' => sprintf('PPDB-2026-%04d', $seq),
        'tahun_ajaran' => '2026/2027',
        'nisn' => '00000000'.str_pad((string) $seq, 2, '0', STR_PAD_LEFT),
        'jenis_kelamin' => $p['jk'],
        'tempat_lahir' => $p['lahir'],
        'agama' => 'Islam',
        'alamat_lengkap' => 'Jl. Melati No. '.$seq.', Kureksari, Waru',
        'kelurahan_desa' => 'Kureksari',
        'kecamatan' => 'Waru',
        'kabupaten_kota' => 'Sidoarjo',
        'asal_sd' => $p['sd'],
        'npsn_sd' => $p['npsn_sd'],
        'tahun_lulus_sd' => '2026',
        'anak_ke' => 2,
        'jumlah_saudara' => 3,
        'nama_ayah' => 'Surya Wijaya Kusuma',
        'pekerjaan_ayah' => 'Karyawan Swasta',
        'no_hp_ayah' => '62812000010'.str_pad((string) $seq, 2, '0', STR_PAD_LEFT),
        'nama_ibu' => 'Ratna Dewi Anggraini',
        'pekerjaan_ibu' => 'Ibu Rumah Tangga',
        'no_hp_ibu' => '62812000020'.str_pad((string) $seq, 2, '0', STR_PAD_LEFT),
        'penghasilan_ortu' => 'Rp3.000.000 - Rp5.000.000',
        'kuesioner' => $p['kues'] ?: null,
        'jalur_dipilih_at' => $p['tgl'].' 08:10:00',
        'data_diri_lengkap_at' => $p['sd'] === '-' ? null : $p['tgl'].' 09:25:00',
        'berkas_lengkap_at' => count($p['berkas']) >= 4 ? $p['tgl'].' 10:05:00' : null,
        'kuesioner_selesai_at' => $p['kues'] ? $p['tgl'].' 10:30:00' : null,
        'status_seleksi' => $p['seleksi'],
        'catatan_admin' => $p['catatan'] ?? null,
        'pengumuman_at' => $p['pengumuman'] ?? null,
        'created_at' => $p['tgl'].' 08:00:00',
        'updated_at' => $p['tgl'].' 10:30:00',
    ]);

    foreach ($p['berkas'] as $jenis) {
        $rel = 'ppdb/berkas/'.$reg->id.'/'.$jenis.'-demo.png';
        Storage::disk('local')->put($rel, file_get_contents($docFor[$jenis]));
        PpdbBerkas::forceCreate([
            'ppdb_pendaftaran_id' => $reg->id,
            'jenis' => $jenis,
            'file_path' => $rel,
            'original_name' => $jenis === 'foto' ? 'pas-foto-3x4.png' : $jenis.'-'.$seq.'.png',
            'mime_type' => 'image/png',
            'file_size' => (int) Storage::disk('local')->size($rel),
            'status' => count($p['berkas']) >= 4 ? 'valid' : 'menunggu',
            'catatan_admin' => count($p['berkas']) >= 4 ? 'Sesuai dengan data pendaftaran.' : null,
            'verified_at' => count($p['berkas']) >= 4 ? $p['tgl'].' 13:15:00' : null,
        ]);
    }

    if ($p['bayar']) {
        $bukti = null;
        if ($p['bayar']['bukti']) {
            $bukti = 'ppdb/pembayaran/'.$reg->id.'/bukti-transfer-demo.png';
            Storage::disk('local')->put($bukti, file_get_contents($docs.'/bukti-transfer.png'));
        }
        PpdbPembayaran::forceCreate([
            'ppdb_pendaftaran_id' => $reg->id,
            'kode_invoice' => sprintf('INV-2026%02d-%04d-DEMO', 11, $seq),
            'nominal' => $p['bayar']['nominal'],
            'metode' => $p['bayar']['metode'],
            'status' => $p['bayar']['status'],
            'bukti_path' => $bukti,
            'catatan_siswa' => $bukti ? 'Bukti transfer terlampir, mohon diverifikasi.' : null,
            'catatan_admin' => $p['bayar']['status'] === 'lunas' ? 'Dana diterima di rekening sekolah.' : null,
            'verified_at' => $p['bayar']['status'] === 'lunas' ? $p['tgl'].' 14:00:00' : null,
        ]);
    }

    echo "ok  {$reg->no_pendaftaran}  {$p['nama']}  jalur={$p['jalur']}  seleksi={$p['seleksi']}  berkas=".count($p['berkas'])."\n";
}

echo "\ndata demo siap. login siswa: anindita.kirana@contoh.id / DemoSiswa2026\n";
echo 'registrations total: '.PpdbPendaftaran::count()."\n";