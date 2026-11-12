@extends('layouts.ppdb-portal')

@section('title', 'Riwayat Pembayaran | Portal PPDB ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))

@php
  $pembayaran = $pendaftaran->pembayaran;
@endphp

@section('content')
<div class="ppdb-portal-stack">
  <section class="adm-panel" aria-labelledby="ppdb-riwayat-heading">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Transaksi PPDB</span>
        <h1 class="adm-panel__title" id="ppdb-riwayat-heading">Riwayat Pembayaran Formulir</h1>
        <p class="adm-panel__sub">Catatan tagihan dan konfirmasi biaya pendaftaran akun Anda.</p>
      </div>
      <a href="{{ route('ppdb.portal.pembayaran') }}" class="btn btn--ghost btn--sm">Kembali ke Pembayaran</a>
    </div>

    @if(!$pembayaran)
      <div class="adm-panel__body">
        <div class="ppdb-empty-state">
          <div class="ppdb-empty-state__icon" aria-hidden="true">
            <svg class="i"><use href="#i-receipt"/></svg>
          </div>
          <h2>Belum Ada Riwayat Tagihan</h2>
          <p>Tagihan atau konfirmasi biaya formulir akan otomatis diterbitkan setelah Anda memilih jalur pendaftaran pada <strong>Tahap 1 (Pendaftaran Jalur)</strong>.</p>
          <div class="ppdb-empty-state__actions">
            <a href="{{ route('ppdb.portal.jalur') }}" class="btn btn--primary">
              <span>Pilih Jalur Pendaftaran</span>
              <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
            <a href="{{ route('ppdb.portal.beranda') }}" class="btn btn--ghost">Kembali ke Beranda</a>
          </div>
        </div>

        <div class="ppdb-info-duo" style="margin-top:1.25rem;">
          <div class="ppdb-faq-item">
            <h2>Ketentuan Biaya Formulir PPDB</h2>
            <p style="margin-bottom:0.75rem;">Biaya registrasi formulir disesuaikan dengan jalur penerimaan yang Anda pilih:</p>
            <dl class="ppdb-kv-list">
              <div>
                <dt>Jalur Inden / Prestasi</dt>
                <dd><strong>Gratis (Rp 0)</strong> — Otomatis Lunas</dd>
              </div>
              <div>
                <dt>Jalur Reguler</dt>
                <dd><strong>Rp 150.000</strong> — Transfer / Tunai TU</dd>
              </div>
            </dl>
          </div>

          <div class="ppdb-faq-item">
            <h2>Rekening Resmi &amp; Loket Pembayaran</h2>
            <dl class="ppdb-kv-list">
              <div>
                <dt>Bank Tujuan</dt>
                <dd><strong>{{ $siteSettings['ppdb_bank_nama'] ?? 'Bank Jatim' }}</strong></dd>
              </div>
              <div>
                <dt>Nomor Rekening</dt>
                <dd><strong>{{ $siteSettings['ppdb_bank_rekening'] ?? '0262148891' }}</strong></dd>
              </div>
              <div>
                <dt>Atas Nama</dt>
                <dd>{{ $siteSettings['ppdb_bank_atas_nama'] ?? 'Panitia PPDB SMP PGRI 4 Waru' }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    @else
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th>Kode Invoice</th>
              <th>Jalur PPDB</th>
              <th>Nominal</th>
              <th>Metode</th>
              <th>Status</th>
              <th>Waktu Update</th>
              <th>Bukti / Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>{{ $pembayaran->kode_invoice }}</strong></td>
              <td>{{ $pendaftaran->jalur?->nama ?? '-' }}</td>
              <td><strong>{{ $pembayaran->nominalLabel() }}</strong></td>
              <td>{{ $pembayaran->metodeLabel() }}</td>
              <td>
                <span class="ppdb-status-pill ppdb-status-pill--{{ $pembayaran->status }}">
                  {{ $pembayaran->statusLabel() }}
                </span>
              </td>
              <td>{{ $pembayaran->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
              <td>
                @if($pembayaran->bukti_path)
                  <a href="{{ route('ppdb.portal.pembayaran.bukti', $pembayaran) }}" target="_blank" rel="noopener" class="btn btn--ghost btn--sm">Lihat Bukti</a>
                @elseif($pembayaran->metode === 'gratis')
                  <span>Otomatis (Jalur Gratis)</span>
                @else
                  <span>{{ $pembayaran->catatan_siswa ?: 'Menunggu konfirmasi' }}</span>
                @endif
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    @endif
  </section>
</div>
@endsection
