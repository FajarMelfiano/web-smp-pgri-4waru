@extends('layouts.app')

@section('title', 'Buat Kata Sandi Baru Portal PPDB | ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))
@section('description', 'Buat kata sandi baru untuk akun PPDB Online SMP PGRI 4 Waru.')

@section('content')
<section class="ppdb-auth-section" aria-labelledby="ppdb-sandi-baru-title">
  <div class="wrap" style="max-width:620px">
    <div class="ppdb-auth-card">
      <div class="ppdb-auth-card__head">
        <p class="crumbs">
          <a href="{{ route('beranda') }}">Beranda</a>
          <span aria-hidden="true">/</span>
          <a href="{{ route('ppdb.masuk') }}">Masuk Portal</a>
          <span aria-hidden="true">/</span>
          Kata sandi baru
        </p>
        <h1 id="ppdb-sandi-baru-title">Buat kata sandi baru</h1>
        <p>Tautan ini hanya bisa dipakai satu kali. Setelah tersimpan, Anda langsung masuk ke portal PPDB.</p>
      </div>

      @if($errors->any())
        <div class="form-alert form-alert--error" role="alert">
          <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
          <div class="form-alert__body">
            <strong>Kata sandi belum bisa disimpan:</strong>
            <ul>
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('ppdb.password.store') }}" class="ppdb-auth-form" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
          <label for="reset-email">Email akun PPDB <span class="req">*</span></label>
          <input type="email" id="reset-email" name="email" value="{{ old('email', $email) }}" autocomplete="email" readonly required>
          @error('email')
            <span class="field-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="field">
          <label for="reset-password">Kata sandi baru <span class="req">*</span></label>
          <div class="ppdb-input-pw">
            <input
              type="password"
              id="reset-password"
              name="password"
              placeholder="8–16 karakter"
              autocomplete="new-password"
              minlength="8"
              maxlength="16"
              required
            >
            <button type="button" class="ppdb-pw-toggle" data-pw-toggle="reset-password" aria-label="Tampilkan kata sandi">
              <svg class="i" data-pw-icon-show aria-hidden="true"><use href="#i-eye"/></svg>
              <svg class="i" data-pw-icon-hide aria-hidden="true" hidden><use href="#i-eye-slash"/></svg>
            </button>
          </div>
          @error('password')
            <span class="field-error">{{ $message }}</span>
          @else
            <span class="field-hint">Wajib memuat huruf besar, huruf kecil, dan minimal satu angka.</span>
          @enderror
        </div>

        <div class="field">
          <label for="reset-password-confirmation">Ulangi kata sandi baru <span class="req">*</span></label>
          <input
            type="password"
            id="reset-password-confirmation"
            name="password_confirmation"
            placeholder="Ketik ulang kata sandi baru"
            autocomplete="new-password"
            minlength="8"
            maxlength="16"
            required
          >
        </div>

        <div class="ppdb-auth-actions">
          <button type="submit" class="btn btn--primary ppdb-btn-block">
            <span>Simpan &amp; masuk portal</span>
            <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
          </button>
        </div>
      </form>

      <div class="ppdb-auth-card__foot">
        <p>Tautan sudah kedaluwarsa? <a href="{{ route('ppdb.password.request') }}">Minta tautan baru</a></p>
      </div>
    </div>
  </div>
</section>
@endsection
