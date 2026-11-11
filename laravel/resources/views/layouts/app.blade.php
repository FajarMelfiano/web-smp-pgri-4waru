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

<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
  <defs>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M4 12h15m0 0-6-6m6 6-6 6"/></symbol>
    <symbol id="i-upright" viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></symbol>
    <symbol id="i-close" viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4"/></symbol>
    <symbol id="i-moon" viewBox="0 0 24 24"><path d="M20 14.5A8 8 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5Z"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></symbol>
    <symbol id="i-phone" viewBox="0 0 24 24"><path d="M6.4 3h3.1l1.5 3.9-2 1.5a12.4 12.4 0 0 0 6.6 6.6l1.5-2 3.9 1.5v3.1a2.5 2.5 0 0 1-2.8 2.5C10.5 19.6 4.4 13.5 3.9 5.8A2.5 2.5 0 0 1 6.4 3Z"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m4 7 8 6 8-6"/></symbol>
    <symbol id="i-ig" viewBox="0 0 24 24"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1" fill="currentColor" stroke="none"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="16" rx="3"/><path d="M8 3v4M16 3v4M3.5 10h17"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></symbol>
    <symbol id="i-book" viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H19v15H6.5A2.5 2.5 0 0 0 4 20.5Z"/><path d="M4 20.5A2.5 2.5 0 0 1 6.5 18H19v3H6.5"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3l7 3v6c0 4.4-3 7.6-7 9-4-1.4-7-4.6-7-9V6Z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="i-home" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><path d="M9 21v-7a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7"/></symbol>
    <symbol id="i-newspaper" viewBox="0 0 24 24"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Z"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8Z"/></symbol>
    <symbol id="i-form" viewBox="0 0 24 24"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></symbol>
    <symbol id="i-graduate" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20c0-3 2.5-5 5.5-5s5.5 2 5.5 5"/><path d="M16 5.5a3 3 0 0 1 0 5.8M17.5 15c2 .6 3 2.2 3 5"/></symbol>
    <symbol id="i-layers" viewBox="0 0 24 24"><path d="m12 3 8 4.5-8 4.5-8-4.5Z"/><path d="m4 12 8 4.5 8-4.5M4 16.5 12 21l8-4.5"/></symbol>
    <symbol id="i-file" viewBox="0 0 24 24"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h4"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7"/></symbol>
    <symbol id="i-print" viewBox="0 0 24 24"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M6 17h12v4H6Z"/></symbol>
    <symbol id="i-image" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="m21 15-5-5L5 20"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.8-3.8"/></symbol>
    <symbol id="i-expand" viewBox="0 0 24 24"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></symbol>
    <symbol id="i-chev-left" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></symbol>
    <symbol id="i-chev-right" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></symbol>
    <symbol id="i-up" viewBox="0 0 24 24"><path d="M12 19V5m0 0-6 6m6-6 6 6"/></symbol>
  </defs>
</svg>

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
        <a class="btn btn--primary btn--sm head-cta" href="{{ route('ppdb') }}">Daftar <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
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
