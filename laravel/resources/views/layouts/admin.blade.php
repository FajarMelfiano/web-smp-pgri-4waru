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

<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
  <defs>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M4 12h15m0 0-6-6m6 6-6 6"/></symbol>
    <symbol id="i-upright" viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></symbol>
    <symbol id="i-close" viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4"/></symbol>
    <symbol id="i-moon" viewBox="0 0 24 24"><path d="M20 14.5A8 8 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5Z"/></symbol>
    <symbol id="i-phone" viewBox="0 0 24 24"><path d="M6.4 3h3.1l1.5 3.9-2 1.5a12.4 12.4 0 0 0 6.6 6.6l1.5-2 3.9 1.5v3.1a2.5 2.5 0 0 1-2.8 2.5C10.5 19.6 4.4 13.5 3.9 5.8A2.5 2.5 0 0 1 6.4 3Z"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3l7 3v6c0 4.4-3 7.6-7 9-4-1.4-7-4.6-7-9V6Z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20c0-3 2.5-5 5.5-5s5.5 2 5.5 5"/><path d="M16 5.5a3 3 0 0 1 0 5.8M17.5 15c2 .6 3 2.2 3 5"/></symbol>
    <symbol id="i-layers" viewBox="0 0 24 24"><path d="m12 3 8 4.5-8 4.5-8-4.5Z"/><path d="m4 12 8 4.5 8-4.5M4 16.5 12 21l8-4.5"/></symbol>
    <symbol id="i-file" viewBox="0 0 24 24"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h4"/></symbol>
    <symbol id="i-book" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></symbol>
    <symbol id="i-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7"/></symbol>
    <symbol id="i-image" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="m21 15-5-5L5 20"/></symbol>
  </defs>
</svg>

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
            <a class="adm-nav__link {{ request()->routeIs('admin.ppdb') ? 'is-active' : '' }}" href="{{ route('admin.ppdb') }}" @if(request()->routeIs('admin.ppdb')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-users"/></svg>
              <span>Pendaftar PPDB</span>
              <span class="adm-nav__count">{{ $stats['total'] ?? 0 }}</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link {{ request()->routeIs('admin.berita') ? 'is-active' : '' }}" href="{{ route('admin.berita') }}" @if(request()->routeIs('admin.berita')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-file"/></svg>
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
              <svg class="i" aria-hidden="true"><use href="#i-users"/></svg>
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
              <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
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
