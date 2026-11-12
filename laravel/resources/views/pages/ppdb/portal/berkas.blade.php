@extends('layouts.ppdb-portal')

@section('title', 'Unggah Berkas Persyaratan | Portal PPDB ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))

@section('content')
<div class="ppdb-portal-stack">
  <section class="adm-panel" aria-labelledby="ppdb-berkas-heading">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Tahap 4 dari 6</span>
        <h1 class="adm-panel__title" id="ppdb-berkas-heading">Unggah Berkas Persyaratan</h1>
        <p class="adm-panel__sub">Unggah 4 dokumen wajib (KK, Akta Kelahiran, SKL/Rapor SD, Pas Foto 3x4) dalam format JPG, PNG, WebP, atau PDF (maks. 4 MB per berkas).</p>
      </div>
      @if($pendaftaran->hasCompletedBerkas())
        <a href="{{ route('ppdb.portal.kuesioner') }}" class="btn btn--primary btn--sm">
          <span>Lanjut ke Tahap 5: Kuesioner</span>
          <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
        </a>
      @endif
    </div>

    @if($errors->any())
      <div class="form-alert form-alert--error" role="alert" style="margin-bottom:1.25rem">
        <strong>Gagal mengunggah berkas:</strong>
        <ul>
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="ppdb-berkas-grid">
      @foreach(\App\Models\PpdbPendaftaran::BERKAS_LABELS as $jenisKey => $jenisLabel)
        @php
          $isRequired = array_key_exists($jenisKey, \App\Models\PpdbPendaftaran::REQUIRED_BERKAS);
          $uploaded = $berkasMap->get($jenisKey);
        @endphp
        <article class="ppdb-berkas-card {{ $uploaded ? 'is-uploaded' : '' }}">
          <div class="ppdb-berkas-card__head">
            <div>
              <span class="ppdb-berkas-tag {{ $isRequired ? 'is-req' : 'is-opt' }}">
                {{ $isRequired ? 'Dokumen Wajib' : 'Dokumen Pendukung (Opsional)' }}
              </span>
              <h2>{{ $jenisLabel }}</h2>
            </div>

            @if($uploaded)
              <span class="ppdb-status-pill ppdb-status-pill--{{ $uploaded->status }}">
                {{ $uploaded->statusLabel() }}
              </span>
            @else
              <span class="ppdb-status-pill ppdb-status-pill--menunggu">Belum Diunggah</span>
            @endif
          </div>

          @if($uploaded)
            <div class="ppdb-berkas-meta">
              <div>
                <strong>{{ $uploaded->original_name }}</strong>
                <span>{{ $uploaded->fileSizeLabel() }} · Diunggah {{ $uploaded->updated_at?->format('d/m/Y H:i') }}</span>
              </div>
              <a href="{{ route('ppdb.portal.berkas.download', $uploaded) }}" target="_blank" rel="noopener" class="btn btn--ghost btn--sm">
                <svg class="i" aria-hidden="true"><use href="#i-download"/></svg>
                <span>Pratinjau / Unduh</span>
              </a>
            </div>

            @if($uploaded->catatan_admin)
              <div class="form-alert {{ $uploaded->status === 'revisi' ? 'form-alert--error' : 'form-alert--ok' }}" style="margin-bottom:0.85rem">
                <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
                <div class="form-alert__body">
                  <strong>Catatan Verifikator:</strong> {{ $uploaded->catatan_admin }}
                </div>
              </div>
            @endif
          @endif

          <form method="POST" action="{{ route('ppdb.portal.berkas.store') }}" enctype="multipart/form-data" class="ppdb-berkas-form">
            @csrf
            <input type="hidden" name="jenis" value="{{ $jenisKey }}">
            <div class="ppdb-berkas-form__row">
              <label class="ppdb-file" for="berkas-{{ $jenisKey }}">
                <svg class="i" aria-hidden="true"><use href="#i-form"/></svg>
                <span class="ppdb-file__name" data-file-name>Pilih file JPG, PNG, WebP, atau PDF</span>
                <input type="file" id="berkas-{{ $jenisKey }}" name="file" accept=".jpg,.jpeg,.png,.webp,.pdf" required>
              </label>
              <button type="submit" class="btn {{ $uploaded ? 'btn--ghost' : 'btn--primary' }} btn--sm">
                <span>{{ $uploaded ? 'Ganti Berkas' : 'Unggah Berkas' }}</span>
              </button>
            </div>
          </form>
        </article>
      @endforeach
    </div>
  </section>
</div>
@endsection
