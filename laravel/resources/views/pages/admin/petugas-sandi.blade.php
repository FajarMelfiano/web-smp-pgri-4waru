@extends('layouts.admin')

@section('title', 'Ganti Sandi ' . $petugas->name . ' — Admin SMP PGRI 4 Waru')
@section('description', 'Tetapkan kata sandi baru untuk akun petugas SMP PGRI 4 Waru yang kehilangan akses masuk.')
@section('crumb', 'Ganti Sandi Petugas')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Ganti Sandi: {{ $petugas->name }}</h1>
      <p>Tetapkan kata sandi baru untuk akun ini. Dipakai saat petugas kehilangan akses masuk atau tidak bisa membuka email pemulihan.</p>
    </div>
    <div class="adm-actions">
      <a href="{{ route('admin.petugas') }}" class="btn btn--ghost btn--sm">
        <svg class="i" aria-hidden="true"><use href="#i-arrow-left"/></svg>
        <span>Kembali ke Daftar Akun</span>
      </a>
    </div>
  </div>

  @if($errors->has('new_password'))
    <div class="adm-alert adm-alert--error" role="alert">
      <svg class="i" aria-hidden="true"><use href="#i-warning"/></svg>
      <span>{{ $errors->first('new_password') }}</span>
    </div>
  @endif

  <div class="adm-grid-2">
    <section class="adm-panel" aria-labelledby="sandi-petugas-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="sandi-petugas-heading">Kata Sandi Baru</h2>
          <span class="adm-role-badge">{{ $petugas->role }}</span>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.petugas.password', $petugas) }}" class="adm-panel__body" novalidate>
        @csrf

        <div class="field @error('new_password') has-error @enderror">
          <label for="sandi-petugas-baru">Kata sandi baru</label>
          <div class="pw-wrap">
            <input id="sandi-petugas-baru" name="new_password" type="password" autocomplete="new-password" required aria-describedby="sandi-petugas-hint">
            <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Tampilkan kata sandi baru">
              <svg class="i" aria-hidden="true"><use href="#i-eye"/></svg>
            </button>
          </div>
          <span class="field-hint" id="sandi-petugas-hint">8 - 16 karakter, memuat huruf besar, huruf kecil, dan angka. Tidak boleh sama dengan sandi yang sekarang.</span>
          @error('new_password')
            <span class="error">{{ $message }}</span>
          @enderror
        </div>

        <div class="field">
          <label for="sandi-petugas-ulang">Ulangi kata sandi baru</label>
          <div class="pw-wrap">
            <input id="sandi-petugas-ulang" name="new_password_confirmation" type="password" autocomplete="new-password" required>
            <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Tampilkan ulangi kata sandi">
              <svg class="i" aria-hidden="true"><use href="#i-eye"/></svg>
            </button>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn--primary">Simpan Sandi Baru</button>
          <a href="{{ route('admin.petugas') }}" class="btn btn--quiet">Batal</a>
        </div>
      </form>
    </section>

    <section class="adm-panel" aria-labelledby="ringkasan-petugas-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="ringkasan-petugas-heading">Akun Yang Diganti</h2>
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
      </dl>

      <div class="adm-panel__body">
        <p class="adm-note">
          <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
          <span>Kata sandi tidak dikirim lewat email. Sampaikan sandi baru langsung kepada yang bersangkutan, lalu minta segera menggantinya dari halaman <b>Akun &amp; Kata Sandi</b>. Sesi yang masih terbuka tetap berjalan sampai pengguna keluar sendiri.</span>
        </p>
      </div>
    </section>
  </div>
@endsection
