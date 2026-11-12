@extends('layouts.app')

@section('title', 'Masuk Admin — SMP PGRI 4 Waru')
@section('description', 'Halaman masuk pengelolaan administrasi SMP PGRI 4 Waru.')

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:520px;left:-140px;top:-180px"></span>
      <span class="deco__ring deco__ring--dash" style="width:720px;right:-260px;top:-220px"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> Masuk</p>
      <h1>Masuk petugas sekolah</h1>
      <p class="lead">Gunakan akun petugas tata usaha atau kepala sekolah untuk mengelola data pendaftar dan berita.</p>
    </div>
  </div>

  <section class="section" aria-labelledby="judul-login">
    <div class="wrap" style="max-width:540px">
      <h2 id="judul-login" class="sr-only">Formulir masuk</h2>
      <form method="POST" action="{{ route('login.attempt') }}" class="form-sheet rise" novalidate>
        @csrf
        <div class="form-head">
          <span>Otentikasi petugas</span>
        </div>
        <div class="form-body">
          <div class="field @error('email') has-error @enderror">
            <label for="email">Alamat email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
              <span class="error">{{ $message }}</span>
            @enderror
          </div>
          <div class="field @error('password') has-error @enderror">
            <label for="password">Kata sandi</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
            @error('password')
              <span class="error">{{ $message }}</span>
            @enderror
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn--primary">Masuk ke panel</button>
          </div>

          <p class="hint" style="margin-top:0.9rem">
            Lupa kata sandi? <a href="{{ route('admin.password.request') }}">Minta tautan pengaturan kata sandi</a>
          </p>
        </div>
      </form>
    </div>
  </section>
@endsection
