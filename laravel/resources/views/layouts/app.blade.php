<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'SMP PGRI 4 Waru — Sekolah menengah pertama di Kureksari, Sidoarjo')</title>
<meta name="description" content="@yield('description', 'SMP PGRI 4 Waru berdiri sejak 1979 di Jl. Anggrek No. 109, Kureksari, Waru, Sidoarjo. Identitas sekolah, mata pelajaran, alur pendaftaran, dan lokasi.')">
<link rel="preload" href="/fonts/plus-jakarta-sans-var.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="/css/style.css">
<link rel="icon" href="/images/logo-smp-pgri4-waru.png" type="image/png">
<meta property="og:type" content="website">
<meta property="og:title" content="@yield('og_title', 'SMP PGRI 4 Waru')">
<meta property="og:description" content="@yield('og_description', 'Sekolah menengah pertama di Jl. Anggrek No. 109, Kureksari, Waru, Sidoarjo. Berdiri 1979.')">
<meta property="og:locale" content="id_ID">
<script>
  (function () {
    var d = document.documentElement;
    d.classList.add("js");
    var t = null;
    try { t = localStorage.getItem("smp4-theme"); } catch (e) {}
    if (t !== "light" && t !== "dark") {
      t = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    }
    d.setAttribute("data-theme", t);
  })();
</script>
</head>
<body>
<div id="top-sentinel" aria-hidden="true"></div>
<a class="skip-link" href="#isi">Lompat ke isi halaman</a>

@include('partials.icons')

<div class="scroll-progress" id="scroll-progress" aria-hidden="true"></div>

<header class="site-head">
  <div class="wrap">
    <div class="head-inner">
      <a class="brand" href="{{ route('beranda') }}">
        <img class="brand__mark" src="{{ !empty($siteSettings['logo_path']) ? asset('storage/'.$siteSettings['logo_path']) : '/images/logo-smp-pgri4-waru.png' }}" alt="Logo SMP PGRI 4 Waru">
        <span class="brand__text">
          <b>{{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}</b>
          <span>{{ $siteSettings['tagline'] ?? 'Kureksari, Sidoarjo' }}</span>
        </span>
      </a>
      <nav class="nav" id="nav" aria-label="Navigasi utama">
        <ul>
          <li><a href="{{ route('beranda') }}" @if(request()->routeIs('beranda')) aria-current="page" @endif>Beranda</a></li>
          <li><a href="{{ route('profil') }}" @if(request()->routeIs('profil')) aria-current="page" @endif>Profil</a></li>
          <li><a href="{{ route('akademik') }}" @if(request()->routeIs('akademik')) aria-current="page" @endif>Akademik</a></li>
          <li><a href="{{ route('berita.index') }}" @if(request()->routeIs('berita.*')) aria-current="page" @endif>Berita</a></li>
          <li><a href="{{ route('alumni.index') }}" @if(request()->routeIs('alumni.*')) aria-current="page" @endif>Alumni</a></li>
          <li><a href="{{ route('ppdb') }}" @if(request()->routeIs('ppdb')) aria-current="page" @endif>PPDB</a></li>
          <li><a href="{{ route('kontak') }}" @if(request()->routeIs('kontak')) aria-current="page" @endif>Kontak</a></li>
        </ul>
      </nav>
      <div class="head-tools">
        <button class="icon-btn" type="button" data-theme-toggle aria-pressed="false">
          <span class="sr-only" data-theme-label>Gelap</span>
          <svg class="i theme-icon-light" aria-hidden="true"><use href="#i-moon"/></svg>
          <svg class="i theme-icon-dark" aria-hidden="true"><use href="#i-sun"/></svg>
        </button>
        <a class="btn btn--primary btn--sm head-cta" href="{{ route('ppdb') }}">Daftar</a>
        <button class="icon-btn nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="nav">
          <span class="sr-only" data-nav-label>Menu</span>
          <svg class="i" data-icon="open" aria-hidden="true"><use href="#i-menu"/></svg>
          <svg class="i" data-icon="close" aria-hidden="true"><use href="#i-close"/></svg>
        </button>
      </div>
    </div>
  </div>
</header>

<main id="isi">
  @yield('content')
</main>

<footer class="site-foot">
  <div class="wrap">
    <div class="foot-grid">
      <div>
        <p class="foot-title">SMP PGRI 4 Waru</p>
        <p class="foot-sub">Sekolah menengah pertama swasta di bawah YPLP DASMEN PGRI Jawa Timur, berdiri sejak 1979 di Kureksari, Waru, Sidoarjo.</p>
        <div class="foot-social">
          <a href="https://www.instagram.com/smppgri4waru/" aria-label="Instagram @smppgri4waru"><svg class="i" aria-hidden="true"><use href="#i-ig"/></svg></a>
          <a href="mailto:smp.pgri4waru@gmail.com" aria-label="Kirim email ke SMP PGRI 4 Waru"><svg class="i" aria-hidden="true"><use href="#i-mail"/></svg></a>
          <a href="tel:+62318538447" aria-label="Telepon sekolah (031) 8538447"><svg class="i" aria-hidden="true"><use href="#i-phone"/></svg></a>
        </div>
      </div>
      <div>
        <p class="foot-h">Halaman</p>
        <div class="foot-links">
          <a href="{{ route('beranda') }}">Beranda</a>
          <a href="{{ route('profil') }}">Profil</a>
          <a href="{{ route('akademik') }}">Akademik</a>
          <a href="{{ route('berita.index') }}">Berita</a>
          <a href="{{ route('alumni.index') }}">Alumni</a>
          <a href="{{ route('ppdb') }}">PPDB</a>
          <a href="{{ route('kontak') }}">Kontak</a>
          <a href="{{ auth('web')->check() ? route('admin') : route('login') }}">Panel Petugas</a>
        </div>
      </div>
      <div>
        <p class="foot-h">Kontak</p>
        <div class="foot-links">
          <a href="tel:+62318538447">{{ $siteSettings['telepon'] ?? '(031) 8538447' }}</a>
          <a href="mailto:{{ $siteSettings['email'] ?? 'smp.pgri4waru@gmail.com' }}">{{ $siteSettings['email'] ?? 'smp.pgri4waru@gmail.com' }}</a>
          <a href="https://www.instagram.com/smppgri4waru/">Instagram {{ $siteSettings['instagram'] ?? '@smppgri4waru' }}</a>
        </div>
      </div>
      <div>
        <p class="foot-h">Alamat</p>
        <div class="foot-links">
          <span>{{ $siteSettings['alamat'] ?? 'Jl. Anggrek No. 109, Kureksari' }}</span>
          <span>Kec. Waru, Kab. Sidoarjo</span>
          <span>Jawa Timur {{ $siteSettings['kode_pos'] ?? '61256' }}</span>
          <span>NPSN {{ $siteSettings['npsn'] ?? '20540020' }}</span>
        </div>
      </div>
    </div>
    <div class="foot-bottom">
      <p>Situs purwarupa untuk tugas sekolah, bukan situs resmi SMP PGRI 4 Waru. Data pokok bersumber dari Data Referensi Kemendikdasmen.</p>
      <p>Kureksari, Waru, Sidoarjo 61256</p>
    </div>
  </div>
</footer>

<nav class="mobile-quickbar" aria-label="Aksi cepat perangkat seluler">
  <a class="mobile-quickbar__item @if(request()->routeIs('beranda')) is-active @endif" href="{{ route('beranda') }}">
    <svg class="i" aria-hidden="true"><use href="#i-home"/></svg>
    <span>Beranda</span>
  </a>
  <a class="mobile-quickbar__item @if(request()->routeIs('berita.*')) is-active @endif" href="{{ route('berita.index') }}">
    <svg class="i" aria-hidden="true"><use href="#i-newspaper"/></svg>
    <span>Berita</span>
  </a>
  <a class="mobile-quickbar__item mobile-quickbar__item--cta @if(request()->routeIs('ppdb')) is-active @endif" href="{{ route('ppdb') }}">
    <svg class="i" aria-hidden="true"><use href="#i-form"/></svg>
    <span>Daftar PPDB</span>
  </a>
  <a class="mobile-quickbar__item @if(request()->routeIs('alumni.*')) is-active @endif" href="{{ route('alumni.index') }}">
    <svg class="i" aria-hidden="true"><use href="#i-graduate"/></svg>
    <span>Alumni</span>
  </a>
  <a class="mobile-quickbar__item" href="tel:+62318538447">
    <svg class="i" aria-hidden="true"><use href="#i-phone"/></svg>
    <span>Telepon</span>
  </a>
</nav>

<button class="back-top" id="back-top" type="button" aria-label="Kembali ke bagian atas halaman" hidden>
  <svg class="i" aria-hidden="true"><use href="#i-up"/></svg>
</button>

<div class="lightbox" id="photo-lightbox" role="dialog" aria-modal="true" aria-labelledby="lightbox-title" hidden>
  <div class="lightbox__backdrop" data-lightbox-close></div>
  <div class="lightbox__dialog">
    <div class="lightbox__topbar">
      <div class="lightbox__meta">
        <span class="lightbox__badge" id="lightbox-tag">Dokumentasi</span>
        <span class="lightbox__counter" id="lightbox-counter">1 / 1</span>
      </div>
      <button class="icon-btn lightbox__close" type="button" data-lightbox-close aria-label="Tutup pratinjau foto">
        <svg class="i" aria-hidden="true"><use href="#i-close"/></svg>
      </button>
    </div>
    <div class="lightbox__stage" id="lightbox-stage">
      <button class="icon-btn lightbox__nav lightbox__nav--prev" type="button" data-lightbox-prev aria-label="Foto sebelumnya">
        <svg class="i" aria-hidden="true"><use href="#i-chev-left"/></svg>
      </button>
      <figure class="lightbox__figure">
        <img id="lightbox-img" src="" alt="">
        <figcaption class="lightbox__caption">
          <strong id="lightbox-title">Judul Foto</strong>
          <p id="lightbox-desc"></p>
        </figcaption>
      </figure>
      <button class="icon-btn lightbox__nav lightbox__nav--next" type="button" data-lightbox-next aria-label="Foto berikutnya">
        <svg class="i" aria-hidden="true"><use href="#i-chev-right"/></svg>
      </button>
    </div>
  </div>
</div>

<script src="/js/app.js" defer></script>
</body>
</html>
