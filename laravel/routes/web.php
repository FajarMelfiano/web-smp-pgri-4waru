<?php

use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPasswordResetController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PpdbAccountController;
use App\Http\Controllers\PpdbAuthController;
use App\Http\Controllers\PpdbController;
use App\Http\Controllers\PpdbPasswordResetController;
use App\Http\Controllers\PpdbPortalController;
use App\Models\AlumniReview;
use App\Models\Galeri;
use App\Models\Post;
use App\Models\PpdbJalur;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

Route::get('/storage/{path}', function (string $path) {
    abort_if(str_contains($path, '..'), 404);
    abort_unless(Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path, null, [
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.local.fallback');

Route::get('/', function () {
    $beritaTerbaru = Schema::hasTable('posts')
        ? Post::query()->where('status', 'terbit')->latest('published_at')->latest('id')->limit(3)->get()
        : collect();

    $galeriBeranda = Schema::hasTable('galeri')
        ? Galeri::query()->where('tampil_beranda', true)->latest()->limit(6)->get()
        : collect();

    $ulasanAlumni = Schema::hasTable('alumni_reviews')
        ? AlumniReview::query()->where('status', 'terbit')->latest()->limit(3)->get()
        : collect();

    $jalurPpdb = Schema::hasTable('ppdb_jalur')
        ? PpdbJalur::query()->withCount('pendaftaran')->orderBy('urutan')->orderBy('id')->get()
        : collect();

    return view('pages.beranda', compact('beritaTerbaru', 'galeriBeranda', 'ulasanAlumni', 'jalurPpdb'));
})->name('beranda');

Route::get('/profil', fn () => view('pages.profil'))->name('profil');
Route::get('/akademik', fn () => view('pages.akademik'))->name('akademik');

Route::get('/ppdb', function () {
    $jalurList = Schema::hasTable('ppdb_jalur')
        ? PpdbJalur::query()->withCount('pendaftaran')->orderBy('urutan')->orderBy('id')->get()
        : collect();

    return view('pages.ppdb', compact('jalurList'));
})->name('ppdb');

Route::post('/ppdb', [PpdbController::class, 'store'])->middleware('throttle:5,1')->name('ppdb.store');

Route::middleware('guest:ppdb')->group(function (): void {
    Route::get('/ppdb/daftar', [PpdbAuthController::class, 'showRegister'])->name('ppdb.daftar');
    Route::post('/ppdb/daftar/tahap-1', [PpdbAuthController::class, 'saveRegisterStep1'])->middleware('throttle:20,1')->name('ppdb.daftar.step1');
    Route::post('/ppdb/daftar', [PpdbAuthController::class, 'register'])->middleware('throttle:15,1')->name('ppdb.daftar.store');
    Route::get('/ppdb/masuk', [PpdbAuthController::class, 'showLogin'])->name('ppdb.masuk');
    Route::post('/ppdb/masuk', [PpdbAuthController::class, 'login'])->middleware('throttle:15,1')->name('ppdb.masuk.attempt');

    Route::get('/ppdb/lupa-sandi', [PpdbPasswordResetController::class, 'showRequestForm'])->name('ppdb.password.request');
    Route::post('/ppdb/lupa-sandi', [PpdbPasswordResetController::class, 'sendResetLink'])->middleware('throttle:6,1')->name('ppdb.password.email');
    Route::get('/ppdb/reset-sandi/{token}', [PpdbPasswordResetController::class, 'showResetForm'])->name('ppdb.password.reset');
    Route::post('/ppdb/reset-sandi', [PpdbPasswordResetController::class, 'resetPassword'])->middleware('throttle:6,1')->name('ppdb.password.store');
});

Route::post('/ppdb/keluar', [PpdbAuthController::class, 'logout'])
    ->middleware('auth:ppdb')
    ->name('ppdb.keluar');

Route::middleware('auth:ppdb,web')->prefix('ppdb/portal')->name('ppdb.portal')->group(function (): void {
    Route::get('/pembayaran/{pembayaran}/bukti', [PpdbPortalController::class, 'downloadBuktiBayar'])->name('.pembayaran.bukti');
    Route::get('/berkas/{berkas}/unduh', [PpdbPortalController::class, 'downloadBerkas'])->name('.berkas.download');
});

Route::middleware('auth:ppdb')->prefix('ppdb/portal')->name('ppdb.portal')->group(function (): void {
    Route::get('/', [PpdbPortalController::class, 'beranda']);
    Route::get('/beranda', [PpdbPortalController::class, 'beranda'])->name('.beranda');
    Route::get('/jalur', [PpdbPortalController::class, 'jalur'])->name('.jalur');
    Route::post('/jalur', [PpdbPortalController::class, 'storeJalur'])->name('.jalur.store');
    Route::get('/pembayaran', [PpdbPortalController::class, 'pembayaran'])->name('.pembayaran');
    Route::post('/pembayaran', [PpdbPortalController::class, 'storePembayaran'])->name('.pembayaran.store');
    Route::get('/riwayat-pembayaran', [PpdbPortalController::class, 'riwayatPembayaran'])->name('.riwayat-pembayaran');
    Route::get('/data-diri', [PpdbPortalController::class, 'dataDiri'])->name('.data-diri');
    Route::post('/data-diri', [PpdbPortalController::class, 'updateDataDiri'])->name('.data-diri.update');
    Route::get('/berkas', [PpdbPortalController::class, 'berkas'])->name('.berkas');
    Route::post('/berkas', [PpdbPortalController::class, 'storeBerkas'])->name('.berkas.store');
    Route::get('/kuesioner', [PpdbPortalController::class, 'kuesioner'])->name('.kuesioner');
    Route::post('/kuesioner', [PpdbPortalController::class, 'storeKuesioner'])->name('.kuesioner.store');
    Route::get('/kartu-peserta', [PpdbPortalController::class, 'kartuPeserta'])->name('.kartu-peserta');
    Route::get('/pengumuman', [PpdbPortalController::class, 'pengumuman'])->name('.pengumuman');
    Route::get('/bantuan', [PpdbPortalController::class, 'bantuan'])->name('.bantuan');

    Route::get('/akun', [PpdbAccountController::class, 'edit'])->name('.akun');
    Route::post('/akun/sandi', [PpdbAccountController::class, 'updatePassword'])->middleware('throttle:6,1')->name('.akun.password');
});

Route::get('/kontak', fn () => view('pages.kontak'))->name('kontak');

Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('berita.show');

Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
Route::post('/alumni', [AlumniController::class, 'store'])->middleware('throttle:5,1')->name('alumni.store');

Route::get('/sitemap.xml', function () {
    $urls = [
        route('beranda'),
        route('profil'),
        route('akademik'),
        route('ppdb'),
        route('ppdb.daftar'),
        route('ppdb.masuk'),
        route('kontak'),
        route('berita.index'),
        route('alumni.index'),
    ];

    foreach (Post::query()->where('status', 'terbit')->pluck('slug') as $slug) {
        $urls[] = route('berita.show', $slug);
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $url) {
        $xml .= '<url><loc>'.e($url).'</loc></url>';
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
})->name('sitemap');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:15,1')->name('login.attempt');
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:15,1')->name('admin.login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web')->name('logout');

Route::middleware('guest:web')->group(function (): void {
    Route::get('/admin/lupa-sandi', [AdminPasswordResetController::class, 'showRequestForm'])->name('admin.password.request');
    Route::post('/admin/lupa-sandi', [AdminPasswordResetController::class, 'sendResetLink'])->middleware('throttle:6,1')->name('admin.password.email');
    Route::get('/admin/reset-sandi/{token}', [AdminPasswordResetController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('/admin/reset-sandi', [AdminPasswordResetController::class, 'resetPassword'])->middleware('throttle:6,1')->name('admin.password.store');
});

Route::middleware('auth:web')->group(function (): void {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin');
    Route::get('/admin/ppdb', [AdminController::class, 'ppdb'])->name('admin.ppdb');

    Route::post('/admin/ppdb/jalur', [AdminController::class, 'storePpdbJalur'])->middleware('role:admin')->name('admin.ppdb.jalur.store');
    Route::patch('/admin/ppdb/jalur/{jalur}', [AdminController::class, 'updatePpdbJalur'])->middleware('role:admin')->name('admin.ppdb.jalur.update');
    Route::delete('/admin/ppdb/jalur/{jalur}', [AdminController::class, 'destroyPpdbJalur'])->middleware('role:admin')->name('admin.ppdb.jalur.destroy');

    Route::get('/admin/ppdb/online/{pendaftaran}', [AdminController::class, 'showOnlinePpdb'])->name('admin.ppdb.online.show');
    Route::patch('/admin/ppdb/online/{pendaftaran}/pembayaran', [AdminController::class, 'updateOnlinePembayaran'])->name('admin.ppdb.online.pembayaran');
    Route::patch('/admin/ppdb/online/{pendaftaran}/berkas/{berkas}', [AdminController::class, 'updateOnlineBerkas'])->name('admin.ppdb.online.berkas');
    Route::patch('/admin/ppdb/online/{pendaftaran}/status', [AdminController::class, 'updateOnlineStatus'])->name('admin.ppdb.online.status');
    Route::delete('/admin/ppdb/online/{pendaftaran}', [AdminController::class, 'destroyOnlinePpdb'])->middleware('role:admin')->name('admin.ppdb.online.destroy');
    Route::patch('/admin/ppdb/pembayaran/{pembayaran}/verifikasi', [AdminController::class, 'verifikasiPembayaranPpdb'])->name('admin.ppdb.pembayaran.verifikasi');
    Route::patch('/admin/ppdb/berkas/{berkas}/verifikasi', [AdminController::class, 'verifikasiBerkasPpdb'])->name('admin.ppdb.berkas.verifikasi');
    Route::get('/admin/ppdb/online/{pendaftaran}/berkas/{berkas}/unduh', [AdminController::class, 'downloadOnlineBerkas'])->name('admin.ppdb.online.berkas.unduh');
    Route::get('/admin/ppdb/online/{pendaftaran}/pembayaran/{pembayaran}/bukti', [AdminController::class, 'downloadOnlineBuktiBayar'])->name('admin.ppdb.online.pembayaran.bukti');
    Route::get('/admin/ekspor/ppdb-online.csv', [AdminController::class, 'exportOnlineCsv'])->name('admin.ekspor.ppdb-online');

    Route::patch('/admin/pendaftar/{pendaftar}/status', [AdminController::class, 'updateStatusPendaftar'])->name('admin.pendaftar.status');
    Route::delete('/admin/pendaftar/{pendaftar}', [AdminController::class, 'destroyPendaftar'])->middleware('role:admin')->name('admin.pendaftar.destroy');
    Route::get('/admin/ekspor/pendaftar.csv', [AdminController::class, 'exportCsv'])->name('admin.ekspor.pendaftar');

    Route::get('/admin/berita', [AdminController::class, 'berita'])->name('admin.berita');
    Route::post('/admin/berita', [AdminController::class, 'storePost'])->name('admin.berita.store');
    Route::patch('/admin/berita/{post}/status', [AdminController::class, 'togglePostStatus'])->name('admin.berita.status');
    Route::delete('/admin/berita/{post}', [AdminController::class, 'destroyPost'])->middleware('role:admin')->name('admin.berita.destroy');

    Route::get('/admin/galeri', [AdminController::class, 'galeri'])->name('admin.galeri');
    Route::post('/admin/galeri', [AdminController::class, 'storeGaleri'])->name('admin.galeri.store');
    Route::delete('/admin/galeri/{galeri}', [AdminController::class, 'destroyGaleri'])->middleware('role:admin')->name('admin.galeri.destroy');

    Route::get('/admin/alumni', [AdminController::class, 'alumni'])->name('admin.alumni');
    Route::post('/admin/alumni', [AdminController::class, 'storeAlumni'])->name('admin.alumni.store');
    Route::patch('/admin/alumni/{alumni}/status', [AdminController::class, 'toggleAlumniStatus'])->name('admin.alumni.status');
    Route::delete('/admin/alumni/{alumni}', [AdminController::class, 'destroyAlumni'])->middleware('role:admin')->name('admin.alumni.destroy');

    Route::get('/admin/akademik', [AdminController::class, 'akademik'])->name('admin.akademik');

    Route::get('/admin/petugas', [AdminController::class, 'petugas'])->name('admin.petugas');
    Route::post('/admin/petugas', [AdminController::class, 'storePetugas'])->middleware('role:admin')->name('admin.petugas.store');
    Route::delete('/admin/petugas/{user}', [AdminController::class, 'destroyPetugas'])->middleware('role:admin')->name('admin.petugas.destroy');

    Route::get('/admin/pengaturan', [AdminController::class, 'pengaturan'])->name('admin.pengaturan');
    Route::post('/admin/pengaturan', [AdminController::class, 'updateSettings'])->name('admin.pengaturan.update');

    Route::get('/admin/akun', [AdminAccountController::class, 'edit'])->name('admin.account');
    Route::post('/admin/akun/sandi', [AdminAccountController::class, 'updatePassword'])->middleware('throttle:6,1')->name('admin.account.password');
    Route::post('/admin/petugas/{user}/sandi', [AdminAccountController::class, 'resetPetugasPassword'])->middleware(['role:admin', 'throttle:10,1'])->name('admin.petugas.password');
    Route::post('/admin/ppdb/online/{pendaftaran}/akun/sandi', [AdminAccountController::class, 'resetPpdbPassword'])->middleware(['role:admin', 'throttle:10,1'])->name('admin.ppdb.online.password');
});
