<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PpdbController;
use App\Models\AlumniReview;
use App\Models\Galeri;
use App\Models\Post;
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

    return view('pages.beranda', compact('beritaTerbaru', 'galeriBeranda', 'ulasanAlumni'));
})->name('beranda');

Route::get('/profil', fn () => view('pages.profil'))->name('profil');
Route::get('/akademik', fn () => view('pages.akademik'))->name('akademik');
Route::get('/ppdb', fn () => view('pages.ppdb'))->name('ppdb');
Route::post('/ppdb', [PpdbController::class, 'store'])->middleware('throttle:5,1')->name('ppdb.store');
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
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin');
    Route::get('/admin/ppdb', [AdminController::class, 'ppdb'])->name('admin.ppdb');
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
});
