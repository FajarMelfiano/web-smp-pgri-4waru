@extends('layouts.app')

@section('title', 'Buat Kata Sandi Baru Petugas — SMP PGRI 4 Waru')
@section('description', 'Buat kata sandi baru untuk akun petugas SMP PGRI 4 Waru.')

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:520px;left:-140px;top:-180px"></span>
      <span class="deco__ring deco__ring--dash" style="width:720px;right:-260px;top:-220px"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> <a href="{{ route('admin.login') }}">Masuk</a> <span aria-hidden="true">/</span> Kata sandi baru</p>
      <h1>Buat kata sandi baru</h1>
      <p class="lead">Tautan ini hanya berlaku satu kali. Setelah kata sandi diperbarui, Anda langsung masuk ke panel petugas.</p>
    </div>
  </div>

  <section class="section" aria-labelledby="judul-sandi-baru">
    <div class="wrap" style="max-width:540px">
      <h2 id="judul-sandi-baru" class="sr-only">Formulir kata sandi baru</h2>

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

      <form method="POST" action="{{ route('admin.password.store') }}" class="form-sheet rise" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-head">
          <span>Akun petugas</span>
        </div>
        <div class="form-body">
          <div class="field @error('email') has-error @enderror">
            <label for="email">Alamat email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="email" readonly>
            @error('email')
              <span class="error">{{ $message }}</span>
            @enderror
          </div>

          <div class="field @error('password') has-error @enderror">
            <label for="password">Kata sandi baru</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" minlength="8" autofocus>
            @error('password')
              <span class="error">{{ $message }}</span>
            @else
              <span class="hint">Minimal 8 karakter, memuat huruf dan angka.</span>
            @enderror
          </div>

          <div class="field @error('password_confirmation') has-error @enderror">
            <label for="password_confirmation">Ulangi kata sandi baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" minlength="8">
            @error('password_confirmation')
              <span class="error">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn--primary">Simpan kata sandi</button>
            <a class="btn btn--ghost" href="{{ route('admin.password.request') }}">Minta tautan baru</a>
          </div>
        </div>
      </form>
    </div>
  </section>
@endsection
