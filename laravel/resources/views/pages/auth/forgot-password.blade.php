@extends('layouts.app')

@section('title', 'Lupa Kata Sandi Petugas — SMP PGRI 4 Waru')
@section('description', 'Minta tautan pengaturan ulang kata sandi untuk akun petugas SMP PGRI 4 Waru.')

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:520px;left:-140px;top:-180px"></span>
      <span class="deco__ring deco__ring--dash" style="width:720px;right:-260px;top:-220px"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> <a href="{{ route('admin.login') }}">Masuk</a> <span aria-hidden="true">/</span> Lupa kata sandi</p>
      <h1>Lupa kata sandi petugas</h1>
      <p class="lead">Masukkan email akun petugas Anda. Kami kirimkan tautan untuk membuat kata sandi baru yang berlaku 60 menit.</p>
    </div>
  </div>

  <section class="section" aria-labelledby="judul-lupa-sandi">
    <div class="wrap" style="max-width:540px">
      <h2 id="judul-lupa-sandi" class="sr-only">Formulir permintaan tautan kata sandi</h2>

      @if(session('status'))
        <div class="form-alert form-alert--ok" role="status">
          <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
          <div class="form-alert__body">
            <span>{{ session('status') }}</span>
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.password.email') }}" class="form-sheet rise" novalidate>
        @csrf
        <div class="form-head">
          <span>Pemulihan akun petugas</span>
        </div>
        <div class="form-body">
          <div class="field @error('email') has-error @enderror">
            <label for="email">Alamat email terdaftar</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nama@smppgri4waru.sch.id">
            @error('email')
              <span class="error">{{ $message }}</span>
            @else
              <span class="hint">Gunakan email yang dipakai untuk masuk ke panel petugas.</span>
            @enderror
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn--primary">Kirim tautan pengaturan</button>
            <a class="btn btn--ghost" href="{{ route('admin.login') }}">Kembali ke halaman masuk</a>
          </div>

          <p class="hint" style="margin-top:1rem">
            Tidak menerima email dalam 10 menit? Periksa folder spam, atau hubungi kepala sekolah untuk pengaturan manual.
          </p>
        </div>
      </form>
    </div>
  </section>
@endsection
