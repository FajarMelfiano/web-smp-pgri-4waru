@extends('layouts.ppdb-portal')

@section('title', 'Pendaftaran Jalur | Portal PPDB ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))

@section('content')
<div class="ppdb-portal-stack">
  <section class="adm-panel" aria-labelledby="ppdb-jalur-heading">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Tahap 1 dari 6</span>
        <h1 class="adm-panel__title" id="ppdb-jalur-heading">Pilih Jalur Pendaftaran</h1>
        <p class="adm-panel__sub">Pilih jalur penerimaan peserta didik baru yang sesuai. Anda dapat membaca jadwal, biaya formulir, dan ketentuan masing-masing jalur.</p>
      </div>

      <nav class="chips ppdb-jalur-filter" aria-label="Filter status jalur">
        <a class="chip {{ $statusFilter === 'dibuka' ? 'is-active' : '' }}" href="{{ route('ppdb.portal.jalur', ['status' => 'dibuka']) }}" @if($statusFilter === 'dibuka') aria-current="page" @endif>Sedang Dibuka</a>
        <a class="chip {{ $statusFilter === 'ditutup' ? 'is-active' : '' }}" href="{{ route('ppdb.portal.jalur', ['status' => 'ditutup']) }}" @if($statusFilter === 'ditutup') aria-current="page" @endif>Sudah Ditutup</a>
        <a class="chip {{ $statusFilter === 'semua' ? 'is-active' : '' }}" href="{{ route('ppdb.portal.jalur') }}" @if($statusFilter === 'semua') aria-current="page" @endif>Semua Jalur</a>
      </nav>
    </div>

    @if($errors->any())
      <div class="form-alert form-alert--error" role="alert" style="margin-bottom:1.25rem">
        <strong>Gagal memilih jalur:</strong>
        <ul>
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if($pendaftaran->jalur)
      <div class="ppdb-current-jalur">
        <div>
          <span class="ppdb-current-jalur__label">Jalur Terpilih Saat Ini</span>
          <strong>{{ $pendaftaran->jalur->nama }}</strong>
          <span>Dipilih pada {{ $pendaftaran->jalur_dipilih_at?->format('d/m/Y H:i') ?? '-' }} · Biaya Formulir: {{ $pendaftaran->jalur->biayaLabel() }}</span>
        </div>
        <a href="{{ route('ppdb.portal.pembayaran') }}" class="btn btn--primary btn--sm">
          <span>Lanjut ke Tahap 2</span>
          <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
        </a>
      </div>
    @endif

    @if($jalurList->isEmpty())
      <div class="adm-empty">
        <p>Tidak ada jalur pendaftaran yang sesuai dengan filter tersebut.</p>
      </div>
    @else
      <div class="ppdb-jalur-grid">
        @foreach($jalurList as $jalur)
          @php
            $isSelected = (int) $pendaftaran->ppdb_jalur_id === (int) $jalur->id;
          @endphp
          <article class="ppdb-jalur-card {{ $isSelected ? 'is-selected' : '' }} {{ !$jalur->dibuka ? 'is-closed' : '' }}">
            <div class="ppdb-jalur-card__head">
              <div class="ppdb-jalur-card__badges">
                <span class="ppdb-jalur-pill {{ $jalur->dibuka ? 'is-open' : 'is-closed' }}">
                  {{ $jalur->dibuka ? 'Dibuka' : 'Ditutup' }}
                </span>
                @if($isSelected)
                  <span class="ppdb-jalur-pill is-selected">Jalur Pilihan Anda</span>
                @endif
              </div>
              <h2>{{ $jalur->nama }}</h2>
              <p class="ppdb-jalur-card__period">
                <svg class="i" aria-hidden="true"><use href="#i-clock"/></svg>
                <span>{{ $jalur->periodeLabel() }}</span>
              </p>
            </div>

            <div class="ppdb-jalur-card__body">
              @if($jalur->deskripsi)
                <p class="ppdb-jalur-card__desc">{{ $jalur->deskripsi }}</p>
              @endif

              <dl class="ppdb-jalur-meta">
                <div>
                  <dt>Biaya Pendaftaran</dt>
                  <dd><strong>{{ $jalur->biayaLabel() }}</strong></dd>
                </div>
                <div>
                  <dt>Daya Tampung / Kuota</dt>
                  <dd>{{ $jalur->kuota ? $jalur->kuota . ' kursi' : 'Sesuai pagu sekolah' }}</dd>
                </div>
              </dl>

              @if($jalur->persyaratan)
                <div class="ppdb-jalur-req">
                  <strong>Ketentuan &amp; Keunggulan:</strong>
                  <p>{{ $jalur->persyaratan }}</p>
                </div>
              @endif
            </div>

            <div class="ppdb-jalur-card__foot">
              @if($jalur->dibuka)
                <form method="POST" action="{{ route('ppdb.portal.jalur.store') }}">
                  @csrf
                  <input type="hidden" name="ppdb_jalur_id" value="{{ $jalur->id }}">
                  <button type="submit" class="btn {{ $isSelected ? 'btn--ghost' : 'btn--primary' }} ppdb-btn-block">
                    @if($isSelected)
                      <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
                      <span>Jalur Sudah Dipilih (Klik untuk Konfirmasi Ulang)</span>
                    @else
                      <span>Daftar Sekarang di {{ $jalur->nama }}</span>
                      <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
                    @endif
                  </button>
                </form>
              @else
                <button type="button" class="btn btn--ghost ppdb-btn-block" disabled>
                  <svg class="i" aria-hidden="true"><use href="#i-lock"/></svg>
                  <span>Pendaftaran Jalur Ditutup</span>
                </button>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>
</div>
@endsection
