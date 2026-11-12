<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', 'Portal PPDB Online | ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))</title>
<meta name="description" content="@yield('description', 'Portal pendaftaran peserta didik baru (PPDB) online SMP PGRI 4 Waru.')">
<link rel="preload" href="/fonts/plus-jakarta-sans-var.woff2" as="font" type="font/woff2" crossorigin>
@php
  // Versi ikut mtime berkas: setiap deploy CSS baru otomatis memaksa browser
  // mengambil ulang, tanpa perlu user menekan Ctrl+Shift+R.
  $cssBerkas = public_path('css/style.css');
  $cssVersi = is_file($cssBerkas) ? filemtime($cssBerkas) : '1';
@endphp
<link rel="stylesheet" href="/css/style.css?v={{ $cssVersi }}">
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
@php
  $isPendaftaranGroup = request()->routeIs(
    'ppdb.portal.jalur*',
    'ppdb.portal.pembayaran*',
    'ppdb.portal.riwayat-pembayaran',
    'ppdb.portal.data-diri*',
    'ppdb.portal.berkas*',
    'ppdb.portal.kuesioner*',
    'ppdb.portal.kartu-peserta'
  );
  $canPayment = isset($pendaftaran) && $pendaftaran->hasChosenJalur();
  $canBiodata = isset($pendaftaran) && $pendaftaran->canAccessBiodata();
  $canBerkas = isset($pendaftaran) && $pendaftaran->canAccessBerkas();
  $canKuesioner = isset($pendaftaran) && $pendaftaran->canAccessKuesioner();
  $canKartu = isset($pendaftaran) && $pendaftaran->canAccessKartuPeserta();
@endphp
<body class="ppdb-portal-body">
<a class="skip-link" href="#isi">Lompat ke isi halaman</a>

@include('partials.icons')

<div class="adm-shell ppdb-shell">
  <div class="adm-backdrop" data-adm-backdrop></div>

  <aside class="adm-sidebar ppdb-sidebar" id="adm-sidebar" aria-label="Menu portal PPDB">
    <a class="adm-brand" href="{{ route('ppdb.portal.beranda') }}">
      <img class="adm-brand__mark" src="{{ !empty($siteSettings['logo_path']) ? asset('storage/'.$siteSettings['logo_path']) : '/images/logo-smp-pgri4-waru.png' }}" alt="Logo {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}">
      <span class="adm-brand__text">
        <b>{{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}</b>
        <span>Portal PPDB {{ $siteSettings['tahun_ajaran'] ?? '2026/2027' }}</span>
      </span>
    </a>

    <nav class="adm-nav" aria-label="Navigasi tahapan PPDB">
      <div class="adm-nav__group">
        <p class="adm-nav__label">Menu Utama</p>
        <ul class="adm-nav__list">
          <li>
            <a class="adm-nav__link {{ request()->routeIs('ppdb.portal.beranda') ? 'is-active' : '' }}" href="{{ route('ppdb.portal.beranda') }}" @if(request()->routeIs('ppdb.portal.beranda')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-layers"/></svg>
              <span>Beranda</span>
            </a>
          </li>

          <li class="ppdb-nav-acc {{ $isPendaftaranGroup ? 'is-open' : '' }}" data-ppdb-acc>
            <button
              type="button"
              class="adm-nav__link ppdb-nav-acc__btn {{ $isPendaftaranGroup ? 'is-active' : '' }}"
              data-ppdb-acc-toggle
              aria-expanded="{{ $isPendaftaranGroup ? 'true' : 'false' }}"
              aria-controls="ppdb-subnav-pendaftaran"
            >
              <svg class="i" aria-hidden="true"><use href="#i-file"/></svg>
              <span>Pendaftaran</span>
              <svg class="i ppdb-nav-acc__chev" aria-hidden="true"><use href="#i-chev-down"/></svg>
            </button>

            <ul class="ppdb-subnav" id="ppdb-subnav-pendaftaran" @if(!$isPendaftaranGroup) hidden @endif>
              <li>
                <a class="ppdb-subnav__link {{ request()->routeIs('ppdb.portal.jalur*') ? 'is-active' : '' }}" href="{{ route('ppdb.portal.jalur') }}">
                  <span>Pendaftaran Jalur</span>
                  @if(isset($pendaftaran) && $pendaftaran->hasChosenJalur())
                    <svg class="i ppdb-subnav__badge is-done" aria-hidden="true"><use href="#i-check"/></svg>
                  @endif
                </a>
              </li>
              <li>
                <a class="ppdb-subnav__link {{ request()->routeIs('ppdb.portal.pembayaran*') ? 'is-active' : '' }} {{ !$canPayment ? 'is-locked' : '' }}" href="{{ route('ppdb.portal.pembayaran') }}">
                  <span>Pembayaran / Konfirmasi</span>
                  @if(isset($pendaftaran) && $pendaftaran->hasCompletedPaymentStep())
                    <svg class="i ppdb-subnav__badge is-done" aria-hidden="true"><use href="#i-check"/></svg>
                  @elseif(!$canPayment)
                    <svg class="i ppdb-subnav__badge" aria-hidden="true"><use href="#i-lock"/></svg>
                  @endif
                </a>
              </li>
              <li>
                <a class="ppdb-subnav__link {{ request()->routeIs('ppdb.portal.riwayat-pembayaran') ? 'is-active' : '' }}" href="{{ route('ppdb.portal.riwayat-pembayaran') }}">
                  <span>Riwayat Pembayaran</span>
                </a>
              </li>
              <li>
                <a class="ppdb-subnav__link {{ request()->routeIs('ppdb.portal.data-diri*') ? 'is-active' : '' }} {{ !$canBiodata ? 'is-locked' : '' }}" href="{{ route('ppdb.portal.data-diri') }}">
                  <span>Data Diri</span>
                  @if(isset($pendaftaran) && $pendaftaran->hasCompletedBiodata())
                    <svg class="i ppdb-subnav__badge is-done" aria-hidden="true"><use href="#i-check"/></svg>
                  @elseif(!$canBiodata)
                    <svg class="i ppdb-subnav__badge" aria-hidden="true"><use href="#i-lock"/></svg>
                  @endif
                </a>
              </li>
              <li>
                <a class="ppdb-subnav__link {{ request()->routeIs('ppdb.portal.berkas*') ? 'is-active' : '' }} {{ !$canBerkas ? 'is-locked' : '' }}" href="{{ route('ppdb.portal.berkas') }}">
                  <span>Berkas</span>
                  @if(isset($pendaftaran) && $pendaftaran->hasCompletedBerkas())
                    <svg class="i ppdb-subnav__badge is-done" aria-hidden="true"><use href="#i-check"/></svg>
                  @elseif(!$canBerkas)
                    <svg class="i ppdb-subnav__badge" aria-hidden="true"><use href="#i-lock"/></svg>
                  @endif
                </a>
              </li>
              <li>
                <a class="ppdb-subnav__link {{ request()->routeIs('ppdb.portal.kuesioner*') ? 'is-active' : '' }} {{ !$canKuesioner ? 'is-locked' : '' }}" href="{{ route('ppdb.portal.kuesioner') }}">
                  <span>Kuesioner</span>
                  @if(isset($pendaftaran) && $pendaftaran->hasCompletedKuesioner())
                    <svg class="i ppdb-subnav__badge is-done" aria-hidden="true"><use href="#i-check"/></svg>
                  @elseif(!$canKuesioner)
                    <svg class="i ppdb-subnav__badge" aria-hidden="true"><use href="#i-lock"/></svg>
                  @endif
                </a>
              </li>
              <li>
                <a class="ppdb-subnav__link {{ request()->routeIs('ppdb.portal.kartu-peserta') ? 'is-active' : '' }} {{ !$canKartu ? 'is-locked' : '' }}" href="{{ route('ppdb.portal.kartu-peserta') }}">
                  <span>Kartu Peserta</span>
                  @if(!$canKartu)
                    <svg class="i ppdb-subnav__badge" aria-hidden="true"><use href="#i-lock"/></svg>
                  @endif
                </a>
              </li>
            </ul>
          </li>

          <li>
            <a class="adm-nav__link {{ request()->routeIs('ppdb.portal.pengumuman') ? 'is-active' : '' }}" href="{{ route('ppdb.portal.pengumuman') }}" @if(request()->routeIs('ppdb.portal.pengumuman')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-newspaper"/></svg>
              <span>Pengumuman</span>
            </a>
          </li>

          <li>
            <a class="adm-nav__link {{ request()->routeIs('ppdb.portal.bantuan') ? 'is-active' : '' }}" href="{{ route('ppdb.portal.bantuan') }}" @if(request()->routeIs('ppdb.portal.bantuan')) aria-current="page" @endif>
              <svg class="i" aria-hidden="true"><use href="#i-question"/></svg>
              <span>Bantuan</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="adm-nav__group">
        <p class="adm-nav__label">Panduan &amp; Situs</p>
        <ul class="adm-nav__list">
          <li>
            <button type="button" class="adm-nav__link ppdb-nav-btn" data-ppdb-guide-open>
              <svg class="i" aria-hidden="true"><use href="#i-book"/></svg>
              <span>Tata Cara Pendaftaran</span>
            </button>
          </li>
          <li>
            <a class="adm-nav__link" href="{{ route('beranda') }}">
              <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg>
              <span>Ke Beranda Sekolah</span>
            </a>
          </li>
          <li>
            <a class="adm-nav__link" href="{{ auth('web')->check() ? route('admin.ppdb') : route('login') }}">
              <svg class="i" aria-hidden="true"><use href="#i-shield"/></svg>
              <span>{{ auth('web')->check() ? 'Buka Panel Admin' : 'Login Petugas / Admin' }}</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <div class="adm-user">
      @if(isset($account))
        <div class="adm-user__info">
          <div>
            <span class="adm-user__name">{{ $account->nama_lengkap }}</span>
            <span class="adm-user__email">NIK {{ $account->nik }}</span>
          </div>
          <span class="adm-role-badge">Siswa</span>
        </div>
      @endif
      <form method="POST" action="{{ route('ppdb.keluar') }}">
        @csrf
        <button type="submit" class="btn btn--ghost btn--sm" style="width:100%">Keluar Portal</button>
      </form>
    </div>
  </aside>

  <div class="adm-workspace">
    <header class="adm-topbar ppdb-topbar">
      <div class="adm-topbar__left">
        <button class="icon-btn adm-sidebar-toggle" type="button" data-adm-toggle aria-label="Buka menu portal PPDB" aria-controls="adm-sidebar" aria-expanded="false">
          <svg class="i" aria-hidden="true"><use href="#i-menu"/></svg>
        </button>

        <div class="ppdb-topbar-chips">
          <div class="ppdb-topbar-chip">
            <span>Nomor Pendaftaran</span>
            <strong>{{ $pendaftaran->no_pendaftaran ?? '-' }}</strong>
          </div>
          <div class="ppdb-topbar-chip ppdb-topbar-chip--school">
            <span>Sekolah Terdaftar</span>
            <strong>{{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}</strong>
          </div>
        </div>
      </div>

      <div class="adm-topbar__right">
        <button type="button" class="btn btn--ghost btn--sm" data-ppdb-guide-open>
          <svg class="i" aria-hidden="true"><use href="#i-book"/></svg>
          <span>Tata Cara</span>
        </button>
        <button class="icon-btn" type="button" data-theme-toggle aria-pressed="false">
          <span class="sr-only" data-theme-label>Gelap</span>
          <svg class="i theme-icon-light" aria-hidden="true"><use href="#i-moon"/></svg>
          <svg class="i theme-icon-dark" aria-hidden="true"><use href="#i-sun"/></svg>
        </button>
      </div>
    </header>

    <main id="isi" class="adm-main ppdb-main">
      @if(session('ppdb_status'))
        <div class="adm-alert ppdb-flash" role="status">
          <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
          <span>{{ session('ppdb_status') }}</span>
        </div>
      @endif

      @if(session('ppdb_warning'))
        <div class="form-alert form-alert--error ppdb-flash" role="alert">
          <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
          <div class="form-alert__body">
            <span>{{ session('ppdb_warning') }}</span>
          </div>
        </div>
      @endif

      @yield('content')
    </main>
  </div>
</div>

{{-- Modal Tata Cara Pendaftaran (6 Langkah sesuai referensi img1.png) --}}
<div class="ppdb-modal" id="ppdb-guide-modal" role="dialog" aria-modal="true" aria-labelledby="ppdb-guide-title" hidden>
  <div class="ppdb-modal__backdrop" data-ppdb-guide-close></div>
  <div class="ppdb-modal__dialog">
    <div class="ppdb-modal__head">
      <div>
        <span class="eyebrow">Panduan Resmi PPDB Online</span>
        <h2 id="ppdb-guide-title">Tata Cara Pendaftaran</h2>
      </div>
      <button type="button" class="icon-btn" data-ppdb-guide-close aria-label="Tutup panduan pendaftaran">
        <svg class="i" aria-hidden="true"><use href="#i-close"/></svg>
      </button>
    </div>

    <div class="ppdb-modal__body">
      <p class="ppdb-modal__lead">Ikuti 6 tahapan berurutan berikut agar berkas pendaftaran Anda di {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }} dapat segera diverifikasi oleh Panitia PPDB:</p>

      <ol class="ppdb-guide-list">
        <li>
          <span class="ppdb-guide-list__num">1</span>
          <div>
            <strong>Pendaftaran Jalur</strong>
            <p>Pilih salah satu jalur/gelombang pendaftaran yang sedang dibuka (Jalur Inden / Gelombang Dini, Jalur Prestasi Akademik &amp; Non-Akademik, atau Jalur Reguler).</p>
          </div>
        </li>
        <li>
          <span class="ppdb-guide-list__num">2</span>
          <div>
            <strong>Pembayaran / Konfirmasi Formulir</strong>
            <p>Apabila jalur yang dipilih bebas biaya formulir (Gratis), sistem otomatis mengonfirmasi tahapan ini. Apabila berbayar, unggah bukti transfer bank atau pilih bayar tunai di Loket TU Sekolah.</p>
          </div>
        </li>
        <li>
          <span class="ppdb-guide-list__num">3</span>
          <div>
            <strong>Pengisian Data Diri &amp; Orang Tua</strong>
            <p>Lengkapi biodata calon siswa (NISN, tempat lahir, agama, alamat domisili, asal SD/MI) beserta identitas dan kontak orang tua/wali.</p>
          </div>
        </li>
        <li>
          <span class="ppdb-guide-list__num">4</span>
          <div>
            <strong>Unggah Berkas Persyaratan</strong>
            <p>Unggah 4 dokumen wajib: Kartu Keluarga (KK), Akta Kelahiran, Surat Keterangan Lulus (SKL) / Rapor SD, dan Pas Foto 3x4 berwarna. Sertifikat prestasi dapat dilampirkan sebagai pendukung.</p>
          </div>
        </li>
        <li>
          <span class="ppdb-guide-list__num">5</span>
          <div>
            <strong>Pengisian Kuesioner Singkat</strong>
            <p>Isi sumber informasi PPDB, alasan memilih SMP PGRI 4 Waru, minat ekstrakurikuler, serta pernyataan kesiapan menaati tata tertib sekolah.</p>
          </div>
        </li>
        <li>
          <span class="ppdb-guide-list__num">6</span>
          <div>
            <strong>Cetak Kartu Peserta &amp; Pantau Pengumuman</strong>
            <p>Setelah seluruh tahap selesai, unduh atau cetak Kartu Peserta PPDB sebagai bukti sah registrasi dan pantau hasil kelulusan di menu Pengumuman.</p>
          </div>
        </li>
      </ol>
    </div>

    <div class="ppdb-modal__foot">
      <a href="{{ route('ppdb.portal.bantuan') }}" class="btn btn--ghost btn--sm">Butuh Bantuan Panitia?</a>
      <button type="button" class="btn btn--primary btn--sm" data-ppdb-guide-close>Mengerti &amp; Lanjutkan</button>
    </div>
  </div>
</div>

<script src="/js/app.js" defer></script>
</body>
</html>
