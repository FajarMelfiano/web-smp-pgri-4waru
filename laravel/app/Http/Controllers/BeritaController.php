<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use App\Models\Post;
use Illuminate\Contracts\View\View;

class BeritaController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->where('status', 'terbit')
            ->latest('published_at')
            ->latest('id')
            ->get();

        $galeri = Galeri::query()
            ->latest()
            ->get();

        return view('pages.berita.index', compact('posts', 'galeri'));
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->where('status', 'terbit')
            ->firstOrFail();

        $beritaLain = Post::query()
            ->where('status', 'terbit')
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('pages.berita.show', compact('post', 'beritaLain'));
    }
}
