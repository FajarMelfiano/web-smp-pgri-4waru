@extends('layouts.ppdb-portal')

@section('title', 'Kartu Peserta PPDB | ' . $pendaftaran->no_pendaftaran)

@section('content')
<div class="ppdb-portal-stack">
  <div class="ppdb-print-toolbar no-print">
    <div>
      <span class="eyebrow">Tahap 6 dari 6</span>
      <h1 class="adm-panel__title">Kartu Tanda Peserta PPDB</h1>
      <p class="adm-panel__sub">Simpan sebagai PDF atau cetak kartu ini untuk dibawa saat daftar ulang dan verifikasi fisik di sekolah.</p>
    </div>
    <div class="ppdb-print-toolbar__actions">
      <button type="button" class="btn btn--primary" onclick="window.print()">
        <svg class="i" aria-hidden="true"><use href="#i-print"/></svg>
        <span>Cetak / Simpan PDF</span>
      </button>
      <a href="{{ route('ppdb.portal.pengumuman') }}" class="btn btn--ghost">Lihat Pengumuman</a>
    </div>
  </div>

  <article class="ppdb-participant-card" aria-label="Kartu Peserta PPDB {{ $account->nama_lengkap }}">
    <header class="ppdb-participant-card__head">
      <img
        src="{{ !empty($siteSettings['logo_path']) ? asset('storage/' . $siteSettings['logo_path']) : asset('images/logo-smp-pgri4-waru.png') }}"
        alt="Logo {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}"
        width="64"
        height="64"
      >
      <div class="ppdb-participant-card__school">
        <span>PANITIA PENERIMAAN PESERTA DIDIK BARU (PPDB)</span>
        <h2>{{ strtoupper($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 WARU') }}</h2>
        <p>{{ $siteSettings['alamat'] ?? 'Jl. Anggrek No. 109, Kureksari, Kec. Waru, Kab. Sidoarjo' }} · Telp. {{ $siteSettings['telepon'] ?? '(031) 8538447' }}</p>
      </div>
      <div class="ppdb-participant-card__regno">
        <span>NOMOR PENDAFTARAN</span>
        <strong>{{ $pendaftaran->no_pendaftaran }}</strong>
        <small>T.A. {{ $pendaftaran->tahun_ajaran }}</small>
      </div>
    </header>

    <div class="ppdb-participant-card__body">
      <div class="ppdb-participant-card__photo">
        @if($fotoBerkas && str_starts_with((string) $fotoBerkas->mime_type, 'image/'))
          <img src="{{ route('ppdb.portal.berkas.download', $fotoBerkas) }}" alt="Pas Foto {{ $account->nama_lengkap }}">
        @else
          <div class="ppdb-participant-card__photo-placeholder">
            <span>PAS FOTO</span>
            <strong>3 x 4</strong>
          </div>
        @endif
        <span class="ppdb-status-pill ppdb-status-pill--{{ $pendaftaran->status_seleksi }}">
          {{ $pendaftaran->statusSeleksiLabel() }}
        </span>
      </div>

      <dl class="ppdb-participant-card__table">
        <div>
          <dt>Nama Lengkap Calon Siswa</dt>
          <dd><strong>{{ $account->nama_lengkap }}</strong></dd>
        </div>
        <div>
          <dt>NIK / NISN</dt>
          <dd>{{ $account->nik }} / {{ $pendaftaran->nisn ?: '-' }}</dd>
        </div>
        <div>
          <dt>Tempat, Tanggal Lahir</dt>
          <dd>{{ $pendaftaran->tempat_lahir ?: '-' }}, {{ $account->tanggal_lahir?->format('d-m-Y') ?? '-' }}</dd>
        </div>
        <div>
          <dt>Jenis Kelamin / Agama</dt>
          <dd>{{ $pendaftaran->jenis_kelamin ?: '-' }} / {{ $pendaftaran->agama ?: '-' }}</dd>
        </div>
        <div>
          <dt>Asal Sekolah Dasar (SD/MI)</dt>
          <dd>{{ $pendaftaran->asal_sd ?: '-' }} (Lulus {{ $pendaftaran->tahun_lulus_sd ?: '-' }})</dd>
        </div>
        <div>
          <dt>Jalur Pendaftaran</dt>
          <dd><strong>{{ $pendaftaran->jalur?->nama ?? '-' }}</strong></dd>
        </div>
        <div>
          <dt>Alamat Domisili</dt>
          <dd>{{ $pendaftaran->alamat_lengkap }}, {{ $pendaftaran->kelurahan_desa }}, Kec. {{ $pendaftaran->kecamatan }}, {{ $pendaftaran->kabupaten_kota }}</dd>
        </div>
        <div>
          <dt>Nama Orang Tua (Ayah / Ibu)</dt>
          <dd>{{ $pendaftaran->nama_ayah ?: '-' }} / {{ $pendaftaran->nama_ibu ?: '-' }}</dd>
        </div>
        <div>
          <dt>Kontak WhatsApp Aktif</dt>
          <dd>+{{ $account->whatsapp }}</dd>
        </div>
      </dl>
    </div>

    <footer class="ppdb-participant-card__foot">
      <div class="ppdb-participant-card__notes">
        <strong>Ketentuan Kartu Peserta:</strong>
        <ol>
          <li>Kartu ini adalah bukti sah pendaftaran online PPDB {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}.</li>
          <li>Bawa cetakan kartu ini beserta dokumen asli saat verifikasi fisik / daftar ulang di Sekretariat PPDB.</li>
        </ol>
      </div>

      <div class="ppdb-participant-card__sign">
        <span>Waru, {{ ($pendaftaran->kuesioner_lengkap_at ?? now())->format('d/m/Y') }}</span>
        <strong>Panitia PPDB {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}</strong>
        <small>Terkonfirmasi Sistem Portal PPDB</small>
      </div>
    </footer>
  </article>
</div>
@endsection
