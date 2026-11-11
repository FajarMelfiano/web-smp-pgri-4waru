@extends('layouts.app')

@section('title', $post->judul . ' — SMP PGRI 4 Waru')
@section('description', $post->excerpt(150))

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:520px;left:-140px;top:-180px"></span>
      <span class="deco__ring deco__ring--dash" style="width:720px;right:-260px;top:-220px"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> <a href="{{ route('berita.index') }}">Berita</a> <span aria-hidden="true">/</span> Baca</p>
      <div style="display:flex;align-items:center;gap:0.65rem;justify-content:center;margin-bottom:0.75rem;flex-wrap:wrap">
        <span class="news-card__badge">Berita Sekolah</span>
        <time style="font-size:0.85rem;color:var(--ink-3);font-weight:600" datetime="{{ $post->published_at?->toDateString() }}">
          {{ $post->published_at?->translatedFormat('d F Y') ?? $post->created_at?->translatedFormat('d F Y') }}
        </time>
      </div>
      <h1>{{ $post->judul }}</h1>
      @if($post->ringkasan)
        <p class="lead">{{ $post->ringkasan }}</p>
      @endif
    </div>
  </div>

  <section class="section section--white" aria-labelledby="isi-berita">
    <div class="wrap" style="max-width:820px">
      <h2 id="isi-berita" class="sr-only">Isi berita</h2>

      @if($post->cover_path)
        <figure class="article-cover rise">
          <img src="{{ str_starts_with($post->cover_path, 'http') || str_starts_with($post->cover_path, '/') ? $post->cover_path : asset('storage/'.$post->cover_path) }}" alt="{{ $post->judul }}">
        </figure>
      @endif

      <div class="prose prose--article rise">
        {!! $post->renderedContent() !!}
      </div>

      <div style="margin-top:2.5rem;padding-top:1.5rem;border-top:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
        <a href="{{ route('berita.index') }}" class="btn btn--ghost"><svg class="i" aria-hidden="true"><use href="#i-arrow-left"/></svg> Kembali ke daftar berita</a>
        <a href="{{ route('ppdb') }}" class="btn btn--primary">Informasi PPDB</a>
      </div>
    </div>
  </section>

  @if(isset($beritaLain) && $beritaLain->isNotEmpty())
    <section class="section" aria-labelledby="berita-lain-heading">
      <div class="wrap">
        <div class="sec-head rise">
          <p class="eyebrow">Baca Juga</p>
          <h2 id="berita-lain-heading">Berita <span class="accent">lainnya</span></h2>
          <span class="rule"></span>
        </div>
        <div class="news-grid">
          @foreach($beritaLain as $lain)
            <article class="news-card rise">
              @if($lain->cover_path)
                <a href="{{ route('berita.show', $lain->slug) }}" class="news-card__media" tabindex="-1" aria-hidden="true">
                  <img src="{{ str_starts_with($lain->cover_path, 'http') || str_starts_with($lain->cover_path, '/') ? $lain->cover_path : asset('storage/'.$lain->cover_path) }}" alt="" loading="lazy">
                </a>
              @endif
              <div class="news-card__body">
                <div class="news-card__meta">
                  <span class="news-card__badge">Berita</span>
                  <time datetime="{{ $lain->published_at?->toDateString() }}">{{ $lain->published_at?->translatedFormat('d M Y') }}</time>
                </div>
                <h3 class="news-card__title">
                  <a href="{{ route('berita.show', $lain->slug) }}">{{ $lain->judul }}</a>
                </h3>
                <p class="news-card__desc">{{ $lain->excerpt(110) }}</p>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </section>
  @endif
@endsection
