@extends('layouts.admin')

@section('title', 'Akun & Kata Sandi — Admin SMP PGRI 4 Waru')
@section('description', 'Ganti kata sandi akun petugas yang sedang digunakan untuk masuk ke panel administrasi SMP PGRI 4 Waru.')
@section('crumb', 'Akun & Kata Sandi')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Akun &amp; Kata Sandi</h1>
      <p>Ganti kata sandi akun yang sedang Anda pakai. Sandi lama diminta sebagai bukti bahwa memang Anda pemilik akun ini.</p>
    </div>
  </div>

  @if(session('status'))
    <div class="adm-alert" role="status">
      <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  <div class="adm-grid-2">
    <section class="adm-panel" aria-labelledby="ganti-sandi-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="ganti-sandi-heading">Ganti Kata Sandi</h2>
          <span class="adm-panel__badge">{{ $petugas->role }}</span>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.account.password') }}" class="adm-panel__body" novalidate>
        @csrf

        <div class="field @error('current_password') has-error @enderror">
          <label for="sandi-lama">Kata sandi saat ini</label>
          <div class="pw-wrap">
            <input id="sandi-lama" name="current_password" type="password" autocomplete="current-password" required>
            <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Tampilkan kata sandi saat ini">
              <svg class="i" aria-hidden="true"><use href="#i-eye"/></svg>
            </button>
          </div>
          @error('current_password')
            <span class="error">{{ $message }}</span>
          @enderror
        </div>

        <div class="field @error('password') has-error @enderror">
          <label for="sandi-baru">Kata sandi baru</label>
          <div class="pw-wrap">
            <input id="sandi-baru" name="password" type="password" autocomplete="new-password" required aria-describedby="sandi-baru-hint">
            <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Tampilkan kata sandi baru">
              <svg class="i" aria-hidden="true"><use href="#i-eye"/></svg>
            </button>
          </div>
          <span class="field-hint" id="sandi-baru-hint">8 - 16 karakter, memuat huruf besar, huruf kecil, dan angka. Tidak boleh sama dengan sandi sekarang.</span>
          @error('password')
            <span class="error">{{ $message }}</span>
          @enderror
        </div>

        <div class="field">
          <label for="sandi-ulang">Ulangi kata sandi baru</label>
          <div class="pw-wrap">
            <input id="sandi-ulang" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Tampilkan ulangi kata sandi">
              <svg class="i" aria-hidden="true"><use href="#i-eye"/></svg>
            </button>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn--primary">Simpan Kata Sandi Baru</button>
        </div>
      </form>
    </section>

    <section class="adm-panel" aria-labelledby="ringkasan-akun-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="ringkasan-akun-heading">Ringkasan Akun</h2>
        </div>
      </div>

      <dl class="adm-meta-grid">
        <div class="adm-meta-card">
          <dt>Nama Petugas</dt>
          <dd>{{ $petugas->name }}</dd>
        </div>
        <div class="adm-meta-card">
          <dt>Email Masuk</dt>
          <dd>{{ $petugas->email }}</dd>
        </div>
        <div class="adm-meta-card">
          <dt>Peran Akses</dt>
          <dd>{{ $petugas->role === 'admin' ? 'Administrator Penuh' : 'Operator Verifikasi' }}</dd>
        </div>
        <div class="adm-meta-card">
          <dt>Ganti Sandi Petugas Lain</dt>
          <dd>Halaman Petugas &amp; Hak Akses</dd>
        </div>
      </dl>

      <div class="adm-panel__body">
        <p class="adm-note">
          <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
          <span>Lupa sandi dan tidak bisa membuka email? Minta admin lain mereset dari halaman <b>Petugas &amp; Hak Akses</b>, atau pakai tautan <a href="{{ route('admin.password.request') }}">Lupa sandi</a>.</span>
        </p>
      </div>
    </section>
  </div>
@endsection
