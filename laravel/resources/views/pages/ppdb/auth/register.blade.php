@extends('layouts.app')

@section('title', 'Daftar Akun PPDB Online | ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))
@section('description', 'Buat akun pendaftaran peserta didik baru (PPDB) online SMP PGRI 4 Waru Tahun Ajaran ' . ($siteSettings['tahun_ajaran'] ?? '2026/2027') . '.')

@php
  $draftStep1 = $draftStep1 ?? session('ppdb_reg_draft', []);
  $savedEmail = old('email', $draftStep1['email'] ?? '');
  $savedPassword = $draftStep1['password'] ?? '';
  $step2HasError = $errors->hasAny(['nama_lengkap', 'tanggal_lahir', 'whatsapp', 'nik']);
  $step1HasError = $errors->hasAny(['email', 'password', 'password_confirmation']);
  $hasSavedStep1 = $savedEmail !== '' && $savedPassword !== '';
  $initialStep = (($step2HasError || $hasSavedStep1) && ! $step1HasError) ? 2 : 1;
@endphp

@section('content')
<section class="ppdb-auth-section" aria-labelledby="ppdb-register-title">
  <div class="wrap">
    <div class="ppdb-auth-grid">
      <aside class="ppdb-auth-poster" aria-label="Informasi PPDB SMP PGRI 4 Waru">
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
            <span class="ppdb-auth-poster__eyebrow">PPDB Online · T.A. {{ $siteSettings['tahun_ajaran'] ?? '2026/2027' }}</span>
            <h2>Saatnya Bergabung Bersama Keluarga Besar SMP PGRI 4 Waru</h2>
            <p>Daftar dari rumah melalui portal resmi, pilih jalur gelombang, unggah berkas persyaratan, dan pantau hasil seleksi secara langsung.</p>

            <ul class="ppdb-auth-poster__list">
              <li>
                <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
                <span>Pilih Jalur Inden, Prestasi, atau Reguler</span>
              </li>
              <li>
                <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
                <span>Unggah 4 Berkas Utama (KK, Akta, SKL/Rapor, Foto)</span>
              </li>
              <li>
                <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
                <span>Cetak Kartu Peserta &amp; Pantau Pengumuman Resmi</span>
              </li>
            </ul>
          </div>

          <div class="ppdb-auth-poster__foot">
            <span>{{ $siteSettings['alamat'] ?? 'Jl. Anggrek No. 109, Kureksari, Waru, Sidoarjo' }}</span>
            <span>Telp: {{ $siteSettings['telepon'] ?? '(031) 8538447' }} · NPSN {{ $siteSettings['npsn'] ?? '20540020' }}</span>
          </div>
        </div>
      </aside>

      <div class="ppdb-auth-card">
        <div class="ppdb-auth-card__head">
          <p class="crumbs">
            <a href="{{ route('beranda') }}">Beranda</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('ppdb') }}">PPDB</a>
            <span aria-hidden="true">/</span>
            Daftar Akun
          </p>
          <h1 id="ppdb-register-title">Buat Akun PPDB</h1>
          <p>Isi data akun dan identitas calon siswa untuk memulai pendaftaran tahun ajaran {{ $siteSettings['tahun_ajaran'] ?? '2026/2027' }}.</p>
        </div>

        <ol class="ppdb-auth-steps" aria-label="Tahap pembuatan akun">
          <li class="ppdb-auth-steps__item {{ $initialStep === 1 ? 'is-active' : 'is-done' }}" data-reg-step-indicator="1">
            <span class="ppdb-auth-steps__num">1</span>
            <div>
              <strong>Data Akun</strong>
              <span>Email &amp; kata sandi</span>
            </div>
          </li>
          <li class="ppdb-auth-steps__item {{ $initialStep === 2 ? 'is-active' : '' }}" data-reg-step-indicator="2">
            <span class="ppdb-auth-steps__num">2</span>
            <div>
              <strong>Identitas Siswa</strong>
              <span>Nama, lahir, WA &amp; NIK</span>
            </div>
          </li>
        </ol>

        @if($errors->any())
          <div class="form-alert form-alert--error" role="alert">
            <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
            <div class="form-alert__body">
              <strong>Periksa kembali isian Anda:</strong>
              <ul>
                @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('ppdb.daftar.store') }}" class="ppdb-auth-form" data-ppdb-register-form data-step1-url="{{ route('ppdb.daftar.step1') }}" data-initial-step="{{ $initialStep }}" novalidate>
          @csrf
          <div style="position:absolute;left:-9999px;opacity:0;pointer-events:none" aria-hidden="true">
            <label for="website_url">Jangan diisi</label>
            <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
          </div>

          <div class="ppdb-auth-pane" data-reg-pane="1" @if($initialStep !== 1) hidden @endif>
            <div class="field">
              <label for="reg-email">Email Calon Siswa / Orang Tua <span class="req">*</span></label>
              <input
                type="email"
                id="reg-email"
                name="email"
                value="{{ $savedEmail }}"
                placeholder="Masukkan email aktif (contoh: budi@gmail.com)"
                autocomplete="email"
                required
              >
              <span class="field-error" data-field-error="email">@error('email'){{ $message }}@enderror</span>
            </div>

            <div class="field">
              <label for="reg-password">Kata Sandi <span class="req">*</span></label>
              <div class="ppdb-input-pw">
                <input
                  type="password"
                  id="reg-password"
                  name="password"
                  value="{{ $savedPassword }}"
                  placeholder="Buat kata sandi (8 sampai 16 karakter)"
                  autocomplete="new-password"
                  minlength="8"
                  maxlength="16"
                  data-pw-input
                  required
                >
                <button type="button" class="ppdb-pw-toggle" data-pw-toggle="reg-password" aria-label="Tampilkan kata sandi">
                  <svg class="i" data-pw-icon-show aria-hidden="true"><use href="#i-eye"/></svg>
                  <svg class="i" data-pw-icon-hide aria-hidden="true" hidden><use href="#i-eye-slash"/></svg>
                </button>
              </div>
              <span class="field-error" data-field-error="password">@error('password'){{ $message }}@enderror</span>

              <ul class="ppdb-pw-rules" aria-label="Ketentuan kata sandi">
                <li data-pw-rule="length">
                  <span class="ppdb-pw-rules__dot" aria-hidden="true"><svg class="i"><use href="#i-check"/></svg></span>
                  <span>Panjang 8 - 16 karakter</span>
                </li>
                <li data-pw-rule="case">
                  <span class="ppdb-pw-rules__dot" aria-hidden="true"><svg class="i"><use href="#i-check"/></svg></span>
                  <span>Mengandung huruf besar dan huruf kecil</span>
                </li>
                <li data-pw-rule="digit">
                  <span class="ppdb-pw-rules__dot" aria-hidden="true"><svg class="i"><use href="#i-check"/></svg></span>
                  <span>Mengandung minimal 1 angka</span>
                </li>
              </ul>
            </div>

            <div class="field">
              <label for="reg-password-confirm">Ulangi Kata Sandi <span class="req">*</span></label>
              <div class="ppdb-input-pw">
                <input
                  type="password"
                  id="reg-password-confirm"
                  name="password_confirmation"
                  value="{{ $savedPassword }}"
                  placeholder="Ketik ulang kata sandi Anda"
                  autocomplete="new-password"
                  data-pw-confirm
                  required
                >
                <button type="button" class="ppdb-pw-toggle" data-pw-toggle="reg-password-confirm" aria-label="Tampilkan konfirmasi kata sandi">
                  <svg class="i" data-pw-icon-show aria-hidden="true"><use href="#i-eye"/></svg>
                  <svg class="i" data-pw-icon-hide aria-hidden="true" hidden><use href="#i-eye-slash"/></svg>
                </button>
              </div>
              <span class="field-error" data-field-error="password_confirmation"></span>
            </div>

            <div class="ppdb-auth-actions">
              <button type="button" class="btn btn--primary ppdb-btn-block" data-reg-next>
                <span>Selanjutnya</span>
                <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
              </button>
            </div>
          </div>

          <div class="ppdb-auth-pane" data-reg-pane="2" @if($initialStep !== 2) hidden @endif>
            <div class="ppdb-step1-summary" data-reg-step1-summary>
              <div class="ppdb-step1-summary__info">
                <span class="ppdb-step1-summary__label">Akun PPDB (Tahap 1 Tersimpan)</span>
                <strong data-reg-email-preview>{{ $savedEmail ?: 'Email akun telah diisi' }}</strong>
              </div>
              <button type="button" class="btn btn--quiet btn--sm" data-reg-prev>Ubah Email / Sandi</button>
            </div>
            <div class="field">
              <label for="reg-nama">Nama Lengkap Calon Siswa <span class="req">*</span></label>
              <input
                type="text"
                id="reg-nama"
                name="nama_lengkap"
                value="{{ old('nama_lengkap') }}"
                placeholder="Masukkan nama lengkap sesuai Akta / KK"
                autocomplete="name"
                required
              >
              <span class="field-error" data-field-error="nama_lengkap">@error('nama_lengkap'){{ $message }}@enderror</span>
            </div>

            <div class="field">
              <label for="reg-tgl-lahir">Tanggal Lahir <span class="req">*</span></label>
              <input
                type="date"
                id="reg-tgl-lahir"
                name="tanggal_lahir"
                value="{{ old('tanggal_lahir') }}"
                max="{{ now()->subYears(9)->toDateString() }}"
                required
              >
              <span class="field-error" data-field-error="tanggal_lahir">@error('tanggal_lahir'){{ $message }}@enderror</span>
            </div>

            <div class="field">
              <label for="reg-wa">Nomor WhatsApp Aktif <span class="req">*</span></label>
              @php
                $oldWa = (string) old('whatsapp', '');
                if (str_starts_with($oldWa, '62')) {
                    $oldWa = substr($oldWa, 2);
                } elseif (str_starts_with($oldWa, '0')) {
                    $oldWa = substr($oldWa, 1);
                }
              @endphp
              <div class="ppdb-input-prefix">
                <span class="ppdb-input-prefix__tag" aria-hidden="true">+62</span>
                <input
                  type="tel"
                  id="reg-wa"
                  name="whatsapp"
                  value="{{ $oldWa }}"
                  placeholder="81234567890"
                  inputmode="numeric"
                  autocomplete="tel-national"
                  required
                >
              </div>
              <small class="field-hint">Tulis angka tanpa awalan 0 (contoh: 81234567890).</small>
              <span class="field-error" data-field-error="whatsapp">@error('whatsapp'){{ $message }}@enderror</span>
            </div>

            <div class="field">
              <label for="reg-nik">Nomor Induk Kependudukan (NIK) <span class="req">*</span></label>
              <input
                type="text"
                id="reg-nik"
                name="nik"
                value="{{ old('nik') }}"
                placeholder="16 digit NIK calon siswa sesuai Kartu Keluarga"
                inputmode="numeric"
                maxlength="16"
                pattern="[0-9]{16}"
                required
              >
              <small class="field-hint">Wajib tepat 16 digit angka pada Kartu Keluarga (KK).</small>
              <span class="field-error" data-field-error="nik">@error('nik'){{ $message }}@enderror</span>
            </div>

            <div class="ppdb-auth-actions ppdb-auth-actions--split">
              <button type="button" class="btn btn--ghost" data-reg-prev>
                <svg class="i" aria-hidden="true"><use href="#i-arrow-left"/></svg>
                <span>Kembali</span>
              </button>
              <button type="submit" class="btn btn--primary">
                <span>Daftar Sekarang</span>
                <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
              </button>
            </div>
          </div>
        </form>

        <div class="ppdb-auth-card__foot">
          <p>Sudah memiliki akun PPDB? <a href="{{ route('ppdb.masuk') }}">Masuk di sini</a></p>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
