<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Admin Panel — SMP PGRI 4 Waru')</title>
<meta name="description" content="@yield('description', 'Sistem administrasi pendaftaran dan publikasi SMP PGRI 4 Waru.')">
<link rel="preload" href="/fonts/plus-jakarta-sans-var.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="/css/style.css">
<link rel="icon" href="/images/logo-smp-pgri4-waru.png" type="image/png">
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
<a class="skip-link" href="#isi">Lompat ke isi halaman</a>

@include('partials.icons')

<div class="adm-shell">
  <div class="adm-backdrop" data-adm-backdrop></div>

  <aside class="adm-sidebar" id="adm-sidebar" aria-label="Menu panel administrasi">
    <a class="adm-brand" href="{{ route('admin') }}">
      <img class="adm-brand__mark" src="{{ !empty($siteSettings['logo_path']) ? asset('storage/'.$siteSettings['logo_path']) : '/images/logo-smp-pgri4-waru.png' }}" alt="Logo SMP PGRI 4 Waru">
      <span class="adm-brand__text">
        <b>{{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}</b>
        <span>Panel Administrasi</span>
      </span>
    </a>

    <nav class="adm-nav" aria-label="Navigasi modul admin">
      <div class="adm-nav__group">
        <p class="adm-nav__label">Modul Utama</p>
        <ul class="adm-nav__list">
          <li>
            <a class="adm-nav__link {{ request()->routeIs('admin') ? 'is-active' : '' }}" href="{{ route('admin') }}" @if(request()->routeIs('admin')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-layers"/></svg>
              <span>Ringkasan</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link {{ request()->routeIs('admin.ppdb*') ? 'is-active' : '' }}" href="{{ route('admin.ppdb') }}" @if(request()->routeIs('admin.ppdb*')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-users"/></svg>
              <span>Pendaftar PPDB</span>
              <span class="adm-nav__count">{{ ($stats['online_total'] ?? 0) + ($stats['total'] ?? 0) }}</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link {{ request()->routeIs('admin.berita') ? 'is-active' : '' }}" href="{{ route('admin.berita') }}" @if(request()->routeIs('admin.berita')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-newspaper"/></svg>
              <span>Berita &amp; Pengumuman</span>
              <span class="adm-nav__count">{{ $stats['berita_total'] ?? 0 }}</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link {{ request()->routeIs('admin.galeri') ? 'is-active' : '' }}" href="{{ route('admin.galeri') }}" @if(request()->routeIs('admin.galeri')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-image"/></svg>
              <span>Galeri Foto</span>
              <span class="adm-nav__count">{{ $stats['galeri_total'] ?? 0 }}</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link {{ request()->routeIs('admin.alumni') ? 'is-active' : '' }}" href="{{ route('admin.alumni') }}" @if(request()->routeIs('admin.alumni')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-chat"/></svg>
              <span>Ulasan Alumni</span>
              <span class="adm-nav__count">{{ $stats['alumni_total'] ?? 0 }}</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link {{ request()->routeIs('admin.akademik') ? 'is-active' : '' }}" href="{{ route('admin.akademik') }}" @if(request()->routeIs('admin.akademik')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-book"/></svg>
              <span>Kurikulum &amp; Profil</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link {{ request()->routeIs('admin.petugas') ? 'is-active' : '' }}" href="{{ route('admin.petugas') }}" @if(request()->routeIs('admin.petugas')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-shield"/></svg>
              <span>Petugas &amp; Hak Akses</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link {{ request()->routeIs('admin.pengaturan') ? 'is-active' : '' }}" href="{{ route('admin.pengaturan') }}" @if(request()->routeIs('admin.pengaturan')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-settings"/></svg>
              <span>Pengaturan Situs</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="adm-nav__group">
        <p class="adm-nav__label">Akses Cepat</p>
        <ul class="adm-nav__list">
          <li>
            <a class="adm-nav__link" href="{{ route('admin.ekspor.pendaftar') }}">
              <svg class="i" aria-hidden="true"><use href="#i-download"/></svg>
              <span>Ekspor CSV PPDB</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link" href="{{ route('beranda') }}">
              <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg>
              <span>Lihat Situs Publik</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link" href="{{ route('ppdb') }}">
              <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg>
              <span>Formulir PPDB Publik</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <div class="adm-user">
      @auth
        <div class="adm-user__info">
          <div>
            <span class="adm-user__name">{{ auth()->user()->name }}</span>
            <span class="adm-user__email">{{ auth()->user()->email }}</span>
          </div>
          <span class="adm-role-badge">{{ auth()->user()->role }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn--ghost btn--sm" style="width:100%">Keluar sesi</button>
        </form>
      @endauth
    </div>
  </aside>

  <div class="adm-workspace">
    <header class="adm-topbar">
      <div class="adm-topbar__left">
        <button class="icon-btn adm-sidebar-toggle" type="button" data-adm-toggle aria-label="Buka menu navigasi admin" aria-controls="adm-sidebar" aria-expanded="false">
          <svg class="i" aria-hidden="true"><use href="#i-menu"/></svg>
        </button>
        <div class="adm-crumbs">
          <span>Admin</span>
          <span aria-hidden="true">/</span>
          <b>@yield('crumb', 'Ringkasan Operasional')</b>
        </div>
      </div>

      <div class="adm-topbar__right">
        <button class="icon-btn" type="button" data-theme-toggle aria-pressed="false">
          <span class="sr-only" data-theme-label>Gelap</span>
          <svg class="i theme-icon-light" aria-hidden="true"><use href="#i-moon"/></svg>
          <svg class="i theme-icon-dark" aria-hidden="true"><use href="#i-sun"/></svg>
        </button>
        <a class="btn btn--ghost btn--sm" href="{{ route('admin.ekspor.pendaftar') }}">Unduh CSV</a>
        <a class="btn btn--primary btn--sm" href="{{ route('admin.berita') }}">+ Tulis Berita</a>
      </div>
    </header>

    <main id="isi" class="adm-main">
      @yield('content')
    </main>
  </div>
</div>

<script src="/js/app.js" defer></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var btn = document.querySelector("[data-adm-toggle]");
    var sidebar = document.getElementById("adm-sidebar");
    var backdrop = document.querySelector("[data-adm-backdrop]");
    if (!btn || !sidebar) return;
    function setOpen(open) {
      sidebar.classList.toggle("is-open", open);
      if (backdrop) backdrop.classList.toggle("is-open", open);
      btn.setAttribute("aria-expanded", open ? "true" : "false");
    }
    btn.addEventListener("click", function () {
      setOpen(!sidebar.classList.contains("is-open"));
    });
    if (backdrop) {
      backdrop.addEventListener("click", function () { setOpen(false); });
    }
  });
</script>
</body>
</html>
