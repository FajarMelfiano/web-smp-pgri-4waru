@extends('layouts.app')

@section('title', 'Lupa Kata Sandi Portal PPDB | ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))
@section('description', 'Minta tautan pengaturan ulang kata sandi akun PPDB Online SMP PGRI 4 Waru.')

@section('content')
<section class="ppdb-auth-section" aria-labelledby="ppdb-lupa-sandi-title">
  <div class="wrap" style="max-width:620px">
    <div class="ppdb-auth-card">
      <div class="ppdb-auth-card__head">
        <p class="crumbs">
          <a href="{{ route('beranda') }}">Beranda</a>
          <span aria-hidden="true">/</span>
          <a href="{{ route('ppdb.masuk') }}">Masuk Portal</a>
          <span aria-hidden="true">/</span>
          Lupa kata sandi
        </p>
        <h1 id="ppdb-lupa-sandi-title">Lupa kata sandi portal PPDB</h1>
        <p>Masukkan email yang Anda daftarkan bersama akun PPDB. Tautan pembuatan kata sandi baru berlaku 60 menit.</p>
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
            <strong>Periksa kembali isian Anda:</strong>
            <ul>
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('ppdb.password.email') }}" class="ppdb-auth-form" novalidate>
        @csrf

        <div class="field">
          <label for="lupa-email">Email akun PPDB <span class="req">*</span></label>
          <input
            type="email"
            id="lupa-email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Contoh: budi@gmail.com"
            autocomplete="email"
            inputmode="email"
            required
            autofocus
          >
          @error('email')
            <span class="field-error">{{ $message }}</span>
          @else
            <span class="field-hint">Gunakan email yang sama seperti saat membuat akun. NIK tidak bisa dipakai di sini.</span>
          @enderror
        </div>

        <div class="ppdb-auth-actions">
          <button type="submit" class="btn btn--primary ppdb-btn-block">
            <span>Kirim tautan pengaturan</span>
            <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
          </button>
        </div>
      </form>

      <div class="ppdb-auth-card__foot">
        <p>Ingat kata sandi Anda? <a href="{{ route('ppdb.masuk') }}">Kembali ke halaman masuk</a></p>
        <p class="ppdb-auth-help">Email tidak aktif atau lupa email pendaftaran? Hubungi Panitia PPDB via <a href="https://wa.me/{{ $siteSettings['whatsapp_ppdb'] ?? '62318538447' }}" target="_blank" rel="noopener">WhatsApp Resmi</a></p>
      </div>
    </div>
  </div>
</section>
@endsection
