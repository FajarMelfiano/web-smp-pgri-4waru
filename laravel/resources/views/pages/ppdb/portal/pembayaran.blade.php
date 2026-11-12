@extends('layouts.ppdb-portal')

@section('title', 'Pembayaran & Konfirmasi Formulir | Portal PPDB ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))

@php
  $jalur = $pendaftaran->jalur;
  $pembayaran = $pendaftaran->pembayaran;
  $isGratis = $jalur && $jalur->isGratis();
@endphp

@section('content')
<div class="ppdb-portal-stack">
  <section class="adm-panel" aria-labelledby="ppdb-pay-heading">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Tahap 2 dari 6</span>
        <h1 class="adm-panel__title" id="ppdb-pay-heading">Pembayaran / Konfirmasi Formulir</h1>
        <p class="adm-panel__sub">Informasi biaya registrasi formulir untuk jalur <strong>{{ $jalur?->nama ?? '-' }}</strong>.</p>
      </div>
      <a href="{{ route('ppdb.portal.riwayat-pembayaran') }}" class="btn btn--ghost btn--sm">
        <svg class="i" aria-hidden="true"><use href="#i-receipt"/></svg>
        <span>Riwayat Pembayaran</span>
      </a>
    </div>

    @if($errors->any())
      <div class="form-alert form-alert--error" role="alert" style="margin-bottom:1.25rem">
        <strong>Periksa kembali data konfirmasi:</strong>
        <ul>
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="ppdb-invoice-grid">
      <div class="ppdb-invoice-box">
        <div class="ppdb-invoice-box__head">
          <div>
            <span>Kode Tagihan / Invoice</span>
            <strong>{{ $pembayaran->kode_invoice ?? ('INV-' . $pendaftaran->no_pendaftaran) }}</strong>
          </div>
          <span class="ppdb-status-pill ppdb-status-pill--{{ $pembayaran->status ?? ($isGratis ? 'lunas' : 'menunggu') }}">
            {{ $pembayaran ? $pembayaran->statusLabel() : ($isGratis ? 'Gratis / Lunas' : 'Belum Dibayar') }}
          </span>
        </div>

        <dl class="ppdb-kv-list">
          <div>
            <dt>Nomor Pendaftaran</dt>
            <dd>{{ $pendaftaran->no_pendaftaran }}</dd>
          </div>
          <div>
            <dt>Nama Calon Siswa</dt>
            <dd>{{ $account->nama_lengkap }}</dd>
          </div>
          <div>
            <dt>Jalur Pendaftaran</dt>
            <dd>{{ $jalur?->nama ?? '-' }}</dd>
          </div>
          <div>
            <dt>Nominal Formulir</dt>
            <dd><strong>{{ $jalur ? $jalur->biayaLabel() : 'Rp 0' }}</strong></dd>
          </div>
          <div>
            <dt>Metode Konfirmasi</dt>
            <dd>{{ $pembayaran ? $pembayaran->metodeLabel() : ($isGratis ? 'Bebas Biaya (Gratis)' : '-') }}</dd>
          </div>
          @if($pembayaran?->verified_at)
            <div>
              <dt>Waktu Konfirmasi</dt>
              <dd>{{ $pembayaran->verified_at->format('d/m/Y H:i') }} WIB</dd>
            </div>
          @endif
        </dl>

        @if($pembayaran?->catatan_admin)
          <div class="form-alert {{ $pembayaran->status === 'ditolak' ? 'form-alert--error' : 'form-alert--ok' }}" style="margin-top:1rem">
            <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
            <div class="form-alert__body">
              <strong>Catatan Petugas TU:</strong> {{ $pembayaran->catatan_admin }}
            </div>
          </div>
        @endif
      </div>

      <div class="ppdb-payment-action-box">
        @if($isGratis)
          <div class="ppdb-gratis-state">
            <div class="ppdb-gratis-state__icon">
              <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
            </div>
            <h2>Formulir Bebas Biaya (Rp 0)</h2>
            <p>Jalur <strong>{{ $jalur->nama }}</strong> tidak memungut biaya formulir pendaftaran. Tahap pembayaran telah terkonfirmasi otomatis oleh sistem.</p>
            <div class="ppdb-gratis-state__actions">
              <a href="{{ route('ppdb.portal.data-diri') }}" class="btn btn--primary">
                <span>Lanjut ke Tahap 3: Data Diri</span>
                <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
              </a>
            </div>
          </div>
        @else
          <div class="ppdb-bank-card">
            <div class="ppdb-bank-card__head">
              <svg class="i" aria-hidden="true"><use href="#i-bank"/></svg>
              <div>
                <span>Rekening Resmi Panitia PPDB</span>
                <strong>{{ $bankInfo['nama_bank'] }}</strong>
              </div>
            </div>
            <div class="ppdb-bank-card__number">
              <span>Nomor Rekening</span>
              <strong>{{ $bankInfo['nomor_rekening'] }}</strong>
              <small>a.n. {{ $bankInfo['atas_nama'] }}</small>
            </div>
            <p class="ppdb-bank-card__note">Anda juga dapat melakukan pembayaran tunai langsung di Loket Tata Usaha (TU) {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }} pada jam kerja.</p>
          </div>

          <form method="POST" action="{{ route('ppdb.portal.pembayaran.store') }}" enctype="multipart/form-data" class="ppdb-portal-form">
            @csrf
            <div class="field">
              <label for="pay-metode">Metode Pembayaran <span class="req">*</span></label>
              <select id="pay-metode" name="metode" data-pay-metode-select required>
                <option value="transfer" @selected(old('metode', $pembayaran->metode ?? 'transfer') === 'transfer')>Transfer Bank (Unggah Bukti Transfer)</option>
                <option value="tunai_tu" @selected(old('metode', $pembayaran->metode ?? '') === 'tunai_tu')>Bayar Tunai di Loket TU Sekolah</option>
              </select>
            </div>

            <div class="field" data-pay-bukti-wrap>
              <label for="pay-bukti">Unggah Bukti Transfer / Kwitansi (JPG, PNG, WebP, atau PDF maks. 4 MB)</label>
              <input type="file" id="pay-bukti" name="bukti" accept=".jpg,.jpeg,.png,.webp,.pdf">
              @if($pembayaran?->bukti_path)
                <small class="field-hint">
                  Bukti saat ini sudah tersimpan. <a href="{{ route('ppdb.portal.pembayaran.bukti', $pembayaran) }}" target="_blank" rel="noopener">Lihat bukti yang diunggah</a>.
                </small>
              @else
                <small class="field-hint">Wajib diunggah apabila memilih metode Transfer Bank.</small>
              @endif
            </div>

            <div class="field">
              <label for="pay-catatan">Catatan Tambahan (Opsional)</label>
              <input
                type="text"
                id="pay-catatan"
                name="catatan_siswa"
                value="{{ old('catatan_siswa', $pembayaran->catatan_siswa ?? '') }}"
                placeholder="Contoh: Transfer dari BCA a.n. Bapak Ahmad atau jadwal datang ke TU"
              >
            </div>

            <div class="ppdb-form-foot">
              <button type="submit" class="btn btn--primary">
                <span>Simpan Konfirmasi Pembayaran</span>
                <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
              </button>
              @if($pendaftaran->canAccessBiodata())
                <a href="{{ route('ppdb.portal.data-diri') }}" class="btn btn--ghost">Lanjut ke Data Diri</a>
              @endif
            </div>
          </form>
        @endif
      </div>
    </div>
  </section>
</div>
@endsection
