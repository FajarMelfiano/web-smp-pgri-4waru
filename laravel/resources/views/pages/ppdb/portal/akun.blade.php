@extends('layouts.ppdb-portal')

@section('title', 'Akun & Kata Sandi | Portal PPDB ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))
@section('description', 'Ganti kata sandi akun portal PPDB online Anda.')

@section('content')
<div class="ppdb-portal-stack">
  <section class="adm-panel" aria-labelledby="akun-ppdb-heading">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Keamanan Akun</span>
        <h1 class="adm-panel__title" id="akun-ppdb-heading">Akun &amp; Kata Sandi</h1>
        <p class="adm-panel__sub">Ganti kata sandi portal PPDB Anda sendiri. Sandi lama diminta supaya akun tetap aman kalau perangkat tertinggal dalam keadaan masuk.</p>
      </div>
    </div>

    <div class="adm-panel__body">
      <div class="ppdb-info-duo">
        <form method="POST" action="{{ route('ppdb.portal.akun.password') }}" novalidate>
          @csrf

          <div class="field @error('current_password') has-error @enderror">
            <label for="ppdb-sandi-lama">Kata sandi saat ini</label>
            <div class="pw-wrap">
              <input id="ppdb-sandi-lama" name="current_password" type="password" autocomplete="current-password" required>
              <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Tampilkan kata sandi saat ini">
                <svg class="i" aria-hidden="true"><use href="#i-eye"/></svg>
              </button>
            </div>
            @error('current_password')
              <span class="error">{{ $message }}</span>
            @enderror
          </div>

          <div class="field @error('password') has-error @enderror">
            <label for="ppdb-sandi-baru">Kata sandi baru</label>
            <div class="pw-wrap">
              <input id="ppdb-sandi-baru" name="password" type="password" autocomplete="new-password" required aria-describedby="ppdb-sandi-hint">
              <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Tampilkan kata sandi baru">
                <svg class="i" aria-hidden="true"><use href="#i-eye"/></svg>
              </button>
            </div>
            <span class="field-hint" id="ppdb-sandi-hint">8 - 16 karakter, memuat huruf besar, huruf kecil, dan angka. Tidak boleh sama dengan sandi sekarang.</span>
            @error('password')
              <span class="error">{{ $message }}</span>
            @enderror
          </div>

          <div class="field">
            <label for="ppdb-sandi-ulang">Ulangi kata sandi baru</label>
            <div class="pw-wrap">
              <input id="ppdb-sandi-ulang" name="password_confirmation" type="password" autocomplete="new-password" required>
              <button type="button" class="pw-toggle" data-pw-toggle aria-pressed="false" aria-label="Tampilkan ulangi kata sandi">
                <svg class="i" aria-hidden="true"><use href="#i-eye"/></svg>
              </button>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn--primary">Simpan Kata Sandi Baru</button>
          </div>
        </form>

        <aside class="ppdb-contact-box">
          <h2>Data Akun</h2>
          <dl class="ppdb-kv-list">
            <div>
              <dt>Nama Lengkap</dt>
              <dd>{{ $account->nama_lengkap }}</dd>
            </div>
            <div>
              <dt>Email Akun</dt>
              <dd>{{ $account->email }}</dd>
            </div>
            <div>
              <dt>NIK</dt>
              <dd>{{ $account->nik }}</dd>
            </div>
            <div>
              <dt>Nomor Pendaftaran</dt>
              <dd>{{ $pendaftaran?->no_pendaftaran ?? 'Belum terbit' }}</dd>
            </div>
          </dl>

          <p class="ppdb-inline-note">
            Lupa kata sandi dan tidak bisa membuka email? Minta panitia PPDB mereset akun Anda di sekretariat, atau pakai tautan
            <a href="{{ route('ppdb.password.request') }}">lupa sandi</a>.
          </p>
        </aside>
      </div>
    </div>
  </section>
</div>
@endsection
