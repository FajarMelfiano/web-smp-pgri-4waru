@extends('layouts.app')

@section('title', 'Berita & Galeri Foto — SMP PGRI 4 Waru')
@section('description', 'Informasi pengumuman kegiatan belajar dan galeri dokumentasi foto SMP PGRI 4 Waru di Kureksari, Waru, Sidoarjo.')

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:520px;left:-140px;top:-180px"></span>
      <span class="deco__ring deco__ring--dash" style="width:720px;right:-260px;top:-220px"></span>
      <span class="deco__diamond" style="left:8%;top:28%"></span>
      <span class="deco__dots" style="right:6%;bottom:20%"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> Berita &amp; Galeri</p>
      <h1>Berita &amp; <span class="accent">galeri sekolah</span></h1>
      <p class="lead">Kabar kegiatan belajar, pengumuman layanan administrasi, serta dokumentasi foto kegiatan dari SMP PGRI 4 Waru.</p>
    </div>
  </div>

  <section class="section section--white" aria-labelledby="daftar-berita">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Arsip Terbitan</p>
        <h2 id="daftar-berita">Berita &amp; pengumuman <span class="accent">terbaru</span></h2>
        <p>Seluruh pengumuman resmi dan liputan kegiatan siswa di lingkungan sekolah.</p>
        <span class="rule"></span>
      </div>

      @if($posts->isEmpty())
        <div class="card rise">
          <h3>Belum ada berita yang diterbitkan</h3>
          <p>Silakan kembali lagi nanti atau hubungi kantor sekolah di Jl. Anggrek No. 109, Kureksari untuk informasi terbaru.</p>
        </div>
      @else
        <div class="interactive-toolbar rise">
          <div class="search-pill">
            <svg class="i" aria-hidden="true"><use href="#i-search"/></svg>
            <label for="news-search-input" class="sr-only">Cari judul atau isi berita</label>
            <input id="news-search-input" type="search" data-news-search placeholder="Cari judul berita atau kata kunci pengumuman...">
          </div>
          <span class="interactive-toolbar__count" id="news-search-status" aria-live="polite">Menampilkan {{ $posts->count() }} berita</span>
        </div>

        <div class="news-grid" id="news-grid-list">
          @foreach($posts as $post)
            <article class="news-card rise" data-news-item data-search="{{ mb_strtolower($post->judul . ' ' . $post->excerpt(200)) }}">
              @if($post->cover_path)
                <a href="{{ route('berita.show', $post->slug) }}" class="news-card__media" tabindex="-1" aria-hidden="true">
                  <img src="{{ str_starts_with($post->cover_path, 'http') || str_starts_with($post->cover_path, '/') ? $post->cover_path : asset('storage/'.$post->cover_path) }}" alt="" loading="lazy">
                </a>
              @endif
              <div class="news-card__body">
                <div class="news-card__meta">
                  <span class="news-card__badge">Pengumuman</span>
                  <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('d M Y') ?? $post->created_at?->translatedFormat('d M Y') }}</time>
                </div>
                <h3 class="news-card__title">
                  <a href="{{ route('berita.show', $post->slug) }}">{{ $post->judul }}</a>
                </h3>
                <p class="news-card__desc">{{ $post->excerpt(140) }}</p>
                <a class="link-arrow" href="{{ route('berita.show', $post->slug) }}">Baca berita lengkap <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
              </div>
            </article>
          @endforeach
        </div>
        <div class="card empty-filter-state" id="news-empty-state" hidden>
          <h3>Tidak ada berita yang cocok</h3>
          <p>Coba kata kunci lain atau hapus pencarian untuk melihat seluruh arsip pengumuman sekolah.</p>
        </div>
      @endif
    </div>
  </section>

  <section class="section" id="galeri" aria-labelledby="galeri-heading">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Dokumentasi Sekolah</p>
        <h2 id="galeri-heading">Galeri foto <span class="accent">SMP PGRI 4 Waru</span></h2>
        <p>Dokumentasi kegiatan belajar pagi, upacara, ekstrakurikuler, serta fasilitas sekolah yang dapat terus ditambahkan melalui ruang administrasi.</p>
        <span class="rule"></span>
      </div>

      @if(isset($galeri) && $galeri->isNotEmpty())
        @php
          $kategoriGaleri = $galeri->pluck('kategori')->filter()->unique()->values();
        @endphp
        <div class="interactive-toolbar rise" data-gallery-scope="arsip">
          <div class="filter-bar" role="group" aria-label="Saring kategori galeri foto">
            <button type="button" class="pill" data-gallery-filter="semua" aria-pressed="true">Semua ({{ $galeri->count() }})</button>
            @foreach($kategoriGaleri as $kat)
              <button type="button" class="pill" data-gallery-filter="{{ $kat }}" aria-pressed="false">{{ $kat }}</button>
            @endforeach
          </div>
          <span class="interactive-toolbar__hint">Klik foto untuk pratinjau penuh</span>
        </div>

        <div class="photo-grid" data-gallery-grid="arsip">
          @foreach($galeri as $foto)
            @php
              $fotoSrc = str_starts_with($foto->image_path, 'http') || str_starts_with($foto->image_path, '/') ? $foto->image_path : asset('storage/'.$foto->image_path);
            @endphp
            <figure class="photo-card rise" data-gallery-item data-cat="{{ $foto->kategori }}">
              <button
                type="button"
                class="photo-card__trigger"
                data-lightbox-trigger
                data-src="{{ $fotoSrc }}"
                data-title="{{ $foto->judul }}"
                data-desc="{{ $foto->keterangan }}"
                data-tag="{{ $foto->kategori }}"
                aria-label="Perbesar foto {{ $foto->judul }}"
              >
                <div class="photo-card__img">
                  <img src="{{ $fotoSrc }}" alt="{{ $foto->judul }}" loading="lazy">
                  <span class="photo-card__tag">{{ $foto->kategori }}</span>
                  <span class="photo-card__zoom" aria-hidden="true">
                    <svg class="i" aria-hidden="true"><use href="#i-expand"/></svg>
                  </span>
                </div>
                <figcaption class="photo-card__cap">
                  <b>{{ $foto->judul }}</b>
                  @if($foto->keterangan)
                    <span>{{ $foto->keterangan }}</span>
                  @endif
                </figcaption>
              </button>
            </figure>
          @endforeach
        </div>
      @else
        <div class="card rise">
          <h3>Belum ada foto di galeri</h3>
          <p>Petugas sekolah dapat menambahkan foto dokumentasi kegiatan melalui menu Galeri Foto di panel admin.</p>
        </div>
      @endif
    </div>
  </section>
@endsection
