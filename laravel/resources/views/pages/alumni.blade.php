@extends('layouts.app')

@section('title', 'Jejak & Ulasan Alumni — SMP PGRI 4 Waru')
@section('description', 'Halaman kesan, pesan, dan ulasan para alumni SMP PGRI 4 Waru sejak berdiri tahun 1979 di Kureksari, Waru, Sidoarjo.')

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:520px;left:-140px;top:-180px"></span>
      <span class="deco__ring deco__ring--dash" style="width:720px;right:-260px;top:-220px"></span>
      <span class="deco__diamond" style="left:8%;top:28%"></span>
      <span class="deco__dots" style="right:6%;bottom:20%"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> Alumni</p>
      <h1>Suara &amp; ulasan <span class="accent">para alumni</span></h1>
      <p class="lead">Sejak berdiri pada tahun 1979 di Jalan Anggrek No. 109, ribuan lulusan telah tumbuh bersama SMP PGRI 4 Waru. Baca kesan mereka atau kirimkan cerita Anda sendiri.</p>
      <div style="margin-top:1.5rem;display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap">
        <a href="#tulis-ulasan" class="btn btn--primary">Tulis Ulasan Alumni <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
        <a href="#daftar-ulasan" class="btn btn--ghost">Lihat Cerita Lulusan</a>
      </div>
    </div>
  </div>

  <section class="section section--white" id="daftar-ulasan" aria-labelledby="ulasan-heading">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Kesan &amp; Pesan Lulusan</p>
        <h2 id="ulasan-heading">Cerita dari <span class="accent">alumni kami</span></h2>
        <p>Setiap ulasan yang tampil telah melalui verifikasi pengurus sekolah agar tetap santun dan bermanfaat bagi calon siswa.</p>
        <span class="rule"></span>
      </div>

      @if(session('status'))
        <div class="form-status" role="status" style="margin-bottom:2rem">
          <h3>Pengiriman Berhasil</h3>
          <p>{{ session('status') }}</p>
        </div>
      @endif

      @if($ulasan->isEmpty())
        <div class="card rise">
          <h3>Belum ada ulasan alumni yang ditayangkan</h3>
          <p>Apakah Anda lulusan SMP PGRI 4 Waru? Silakan isi formulir di bawah halaman ini untuk membagikan pengalaman masa sekolah Anda.</p>
        </div>
      @else
        @php
          $angkatanList = $ulasan->pluck('angkatan')->filter()->unique()->sortDesc()->values();
        @endphp
        <div class="interactive-toolbar rise" data-alumni-filter-wrap>
          <div class="filter-bar" role="group" aria-label="Saring angkatan alumni">
            <button type="button" class="pill" data-alumni-cohort="semua" aria-pressed="true">Semua Angkatan ({{ $ulasan->count() }})</button>
            @foreach($angkatanList as $thn)
              <button type="button" class="pill" data-alumni-cohort="{{ $thn }}" aria-pressed="false">Lulusan {{ $thn }}</button>
            @endforeach
          </div>
          <div class="search-pill search-pill--compact">
            <svg class="i" aria-hidden="true"><use href="#i-search"/></svg>
            <label for="alumni-search-input" class="sr-only">Cari nama, profesi, atau cerita alumni</label>
            <input id="alumni-search-input" type="search" data-alumni-search placeholder="Cari nama, profesi, atau cerita...">
          </div>
        </div>
        <p class="filter-status" id="alumni-filter-status" aria-live="polite">Menampilkan {{ $ulasan->count() }} testimoni alumni.</p>

        <div class="alumni-grid" id="alumni-grid-list">
          @foreach($ulasan as $item)
            <article
              class="alumni-card rise"
              data-alumni-item
              data-cohort="{{ $item->angkatan }}"
              data-search="{{ mb_strtolower($item->nama . ' ' . $item->angkatan . ' ' . $item->pekerjaan . ' ' . $item->ulasan) }}"
            >
              <p class="alumni-card__quote">“{{ $item->ulasan }}”</p>
              <div class="alumni-card__author">
                @if($item->foto_path)
                  <img class="alumni-card__avatar" src="{{ str_starts_with($item->foto_path, 'http') || str_starts_with($item->foto_path, '/') ? $item->foto_path : asset('storage/'.$item->foto_path) }}" alt="{{ $item->nama }}" loading="lazy">
                @else
                  <span class="alumni-card__avatar alumni-card__avatar--initial" aria-hidden="true">{{ mb_strtoupper(mb_substr($item->nama, 0, 1)) }}</span>
                @endif
                <div>
                  <b class="alumni-card__name">{{ $item->nama }}</b>
                  <span class="alumni-card__meta">Angkatan {{ $item->angkatan }}@if($item->pekerjaan) · {{ $item->pekerjaan }}@endif</span>
                </div>
              </div>
            </article>
          @endforeach
        </div>
        <div class="card empty-filter-state" id="alumni-empty-state" hidden>
          <h3>Belum ada testimoni pada saringan ini</h3>
          <p>Silakan pilih angkatan lain atau hapus kata kunci pencarian.</p>
        </div>
      @endif
    </div>
  </section>

  <section class="section" id="tulis-ulasan" aria-labelledby="form-alumni-heading">
    <div class="wrap" style="max-width:780px">
      <div class="sec-head rise">
        <p class="eyebrow">Kirim Testimoni</p>
        <h2 id="form-alumni-heading">Bagikan kesan <span class="accent">masa sekolah Anda</span></h2>
        <p>Formulir terbuka bagi seluruh lulusan SMP PGRI 4 Waru dari semua tahun angkatan. Ulasan akan ditinjau oleh admin sekolah sebelum ditayangkan ke publik.</p>
        <span class="rule"></span>
      </div>

      <div class="card rise">
        @if($errors->any())
          <div class="form-status" role="alert" style="margin-bottom:1.5rem">
            <h3>Mohon periksa kembali isian Anda</h3>
            <ul style="margin:0.5rem 0 0;padding-left:1.25rem;color:var(--ink-2);font-size:0.88rem">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('alumni.store') }}" enctype="multipart/form-data">
          @csrf

          <div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden">
            <label for="website_url">Jangan diisi jika Anda manusia</label>
            <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
          </div>

          <div class="adm-form-grid-2">
            <div class="field @error('nama') has-error @enderror">
              <label for="nama">Nama Lengkap Alumni *</label>
              <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required maxlength="120" placeholder="Contoh: Rina Wulandari">
            </div>

            <div class="field @error('angkatan') has-error @enderror">
              <label for="angkatan">Tahun Lulus / Angkatan *</label>
              <input type="text" id="angkatan" name="angkatan" value="{{ old('angkatan') }}" required maxlength="20" placeholder="Contoh: 2019">
            </div>
          </div>

          <div class="adm-form-grid-2">
            <div class="field @error('pekerjaan') has-error @enderror">
              <label for="pekerjaan">Sekolah Lanjutan / Aktivitas Saat Ini</label>
              <input type="text" id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" maxlength="150" placeholder="Contoh: SMAN 1 Waru / Mahasiswa">
            </div>

            <div class="field @error('foto') has-error @enderror">
              <label for="foto">Foto Profil (Opsional, JPG/PNG maks 2MB)</label>
              <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp">
            </div>
          </div>

          <div class="field @error('ulasan') has-error @enderror">
            <div style="display:flex;justify-content:space-between;align-items:baseline;gap:0.5rem">
              <label for="ulasan">Kesan, Pesan &amp; Ulasan Selama Bersekolah *</label>
              <span class="hint" id="ulasan-char-count" style="font-weight:700">0 / 1200</span>
            </div>
            <textarea id="ulasan" name="ulasan" rows="4" required minlength="10" maxlength="1200" data-alumni-textarea placeholder="Ceritakan pengalaman belajar, kenangan bersama bapak/ibu guru, atau pesan untuk adik-adik kelas...">{{ old('ulasan') }}</textarea>
          </div>

          <div class="alumni-live-preview" id="alumni-live-preview" aria-live="polite">
            <span class="alumni-live-preview__label">Pratinjau Kartu Testimoni Anda</span>
            <article class="alumni-card alumni-card--preview">
              <p class="alumni-card__quote" id="preview-alumni-quote">“Tuliskan kesan dan cerita masa sekolah Anda di atas untuk melihat tampilannya di sini.”</p>
              <div class="alumni-card__author">
                <span class="alumni-card__avatar alumni-card__avatar--initial" id="preview-alumni-avatar" aria-hidden="true">A</span>
                <div>
                  <b class="alumni-card__name" id="preview-alumni-name">Nama Alumni</b>
                  <span class="alumni-card__meta" id="preview-alumni-meta">Angkatan Lulusan · Profesi / Studi</span>
                </div>
              </div>
            </article>
          </div>

          <div style="margin-top:1.35rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
            <span style="font-size:0.84rem;color:var(--ink-3)">Ulasan yang dikirim akan dimoderasi oleh pengurus sekolah.</span>
            <button type="submit" class="btn btn--primary">Kirim Ulasan Alumni <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></button>
          </div>
        </form>
      </div>
    </div>
  </section>
@endsection
