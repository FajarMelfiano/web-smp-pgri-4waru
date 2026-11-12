@extends('layouts.app')

@section('title', 'Masuk Portal PPDB | ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))
@section('description', 'Masuk ke portal PPDB Online SMP PGRI 4 Waru menggunakan email atau 16 digit NIK calon siswa.')

@section('content')
<section class="ppdb-auth-section" aria-labelledby="ppdb-login-title">
  <div class="wrap">
    <div class="ppdb-auth-grid">
      <aside class="ppdb-auth-poster" aria-label="Informasi Portal PPDB SMP PGRI 4 Waru">
        <div class="ppdb-auth-poster__media">
          <picture>
            <source srcset="{{ asset('images/gedung-smp-pgri4-waru.webp') }}" type="image/webp">
            <img
              src="{{ asset('images/gedung-smp-pgri4-waru.jpg') }}"
              alt="Gedung SMP PGRI 4 Waru di Kureksari, Sidoarjo"
              width="960"
              height="720"
              loading="eager"
            >
          </picture>
          <div class="ppdb-auth-poster__overlay"></div>
        </div>

        <div class="ppdb-auth-poster__content">
          <div class="ppdb-auth-poster__top">
            <img
              src="{{ !empty($siteSettings['logo_path']) ? asset('storage/' . $siteSettings['logo_path']) : asset('images/logo-smp-pgri4-waru.png') }}"
              alt="Logo {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}"
              width="48"
              height="48"
            >
            <div>
              <strong>{{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}</strong>
              <span>{{ $siteSettings['naungan'] ?? 'YPLP DASMEN PGRI Jawa Timur' }}</span>
            </div>
          </div>

          <div class="ppdb-auth-poster__body">
            <span class="ppdb-auth-poster__eyebrow">Portal Calon Siswa · T.A. {{ $siteSettings['tahun_ajaran'] ?? '2026/2027' }}</span>
            <h2>Lanjutkan Pendaftaran &amp; Pantau Status Seleksi Anda</h2>
            <p>Masuk menggunakan alamat email atau 16 digit NIK yang telah didaftarkan untuk melengkapi tahapan PPDB Online.</p>

            <ul class="ppdb-auth-poster__list">
              <li>
                <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
                <span>Cek Status Verifikasi Pembayaran &amp; Berkas</span>
              </li>
              <li>
                <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
                <span>Lengkapi Data Diri &amp; Kuesioner Calon Siswa</span>
              </li>
              <li>
                <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
                <span>Unduh / Cetak Kartu Peserta Resmi</span>
              </li>
            </ul>
          </div>

          <div class="ppdb-auth-poster__foot">
            <span>{{ $siteSettings['alamat'] ?? 'Jl. Anggrek No. 109, Kureksari, Waru, Sidoarjo' }}</span>
            <span>Telp: {{ $siteSettings['telepon'] ?? '(031) 8538447' }} · NPSN {{ $siteSettings['npsn'] ?? '20540020' }}</span>
          </div>
        </div>
      </aside>

      <div class="ppdb-auth-card ppdb-auth-card--login">
        <div class="ppdb-auth-card__head">
          <p class="crumbs">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('ppdb') }}">PPDB</a>
            <span aria-hidden="true">/</span>
            Masuk Portal
          </p>
          <h1 id="ppdb-login-title">Masuk Portal PPDB</h1>
          <p>Silakan masuk menggunakan email atau 16 digit NIK calon siswa yang telah terdaftar.</p>
        </div>

        @if(session('ppdb_status'))
          <div class="adm-alert" role="status">
            <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
            <span>{{ session('ppdb_status') }}</span>
          </div>
        @endif

        @if($errors->any())
          <div class="form-alert form-alert--error" role="alert">
            <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
            <div class="form-alert__body">
              <strong>Periksa kembali kredensial masuk Anda:</strong>
              <ul>
                @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('ppdb.masuk.attempt') }}" class="ppdb-auth-form" novalidate>
          @csrf

          <div class="field">
            <label for="login-identifier">Email atau 16 Digit NIK <span class="req">*</span></label>
            <input
              type="text"
              id="login-identifier"
              name="identifier"
              value="{{ old('identifier') }}"
              placeholder="Contoh: budi@gmail.com atau 351518XXXXXXXXXX"
              autocomplete="username"
              required
            >
            @error('identifier')
              <span class="field-error">{{ $message }}</span>
            @enderror
          </div>

          <div class="field">
            <label for="login-password">Kata Sandi <span class="req">*</span></label>
            <div class="ppdb-input-pw">
              <input
                type="password"
                id="login-password"
                name="password"
                placeholder="Masukkan kata sandi akun PPDB"
                autocomplete="current-password"
                required
              >
              <button type="button" class="ppdb-pw-toggle" data-pw-toggle="login-password" aria-label="Tampilkan kata sandi">
                <svg class="i" data-pw-icon-show aria-hidden="true"><use href="#i-eye"/></svg>
                <svg class="i" data-pw-icon-hide aria-hidden="true" hidden><use href="#i-eye-slash"/></svg>
              </button>
            </div>
            @error('password')
              <span class="field-error">{{ $message }}</span>
            @enderror
          </div>

          <label class="ppdb-remember">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            <span>Ingat sesi saya di perangkat ini</span>
          </label>

          <div class="ppdb-auth-actions">
            <button type="submit" class="btn btn--primary ppdb-btn-block">
              <span>Masuk ke Portal</span>
              <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
            </button>
          </div>
        </form>

        <div class="ppdb-auth-card__foot">
          <p>Belum memiliki akun pendaftaran? <a href="{{ route('ppdb.daftar') }}">Daftar akun PPDB</a></p>
          <p class="ppdb-auth-help">Lupa kata sandi? <a href="{{ route('ppdb.password.request') }}">Atur ulang lewat email</a> · <a href="https://wa.me/{{ $siteSettings['whatsapp_ppdb'] ?? '62318538447' }}" target="_blank" rel="noopener">WhatsApp Panitia</a> · <a href="{{ route('login') }}">Masuk Petugas Sekolah</a></p>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
