@extends('layouts.ppdb-portal')

@section('title', 'Beranda Portal PPDB | ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))

@php
  $completedCount = collect($steps)->where('state', 'completed')->count();
  $totalSteps = count($steps);
  $progressPercent = (int) round(($completedCount / max($totalSteps, 1)) * 100);
@endphp

@section('content')
<div class="ppdb-portal-stack">
  <section class="ppdb-welcome-banner" aria-labelledby="ppdb-welcome-heading">
    <div class="ppdb-welcome-banner__copy">
      <span class="ppdb-welcome-banner__eyebrow">Selamat Datang di Portal PPDB {{ $pendaftaran->tahun_ajaran }}</span>
      <h1 id="ppdb-welcome-heading">Halo, {{ $account->nama_lengkap }}!</h1>
      <p>Lengkapi tahapan pendaftaran secara berurutan di bawah ini. Saat ini Anda telah menyelesaikan <strong>{{ $completedCount }} dari {{ $totalSteps }} tahapan</strong> ({{ $progressPercent }}%).</p>

      <div class="ppdb-welcome-banner__actions">
        @if($nextStep['state'] !== 'completed')
          <a href="{{ route($nextStep['route']) }}" class="btn btn--primary">
            <span>Lanjutkan: {{ $nextStep['title'] }}</span>
            <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
          </a>
        @else
          <a href="{{ route('ppdb.portal.kartu-peserta') }}" class="btn btn--primary">
            <svg class="i" aria-hidden="true"><use href="#i-print"/></svg>
            <span>Lihat &amp; Cetak Kartu Peserta</span>
          </a>
        @endif

        <button type="button" class="btn btn--ghost ppdb-welcome-banner__guide" data-ppdb-guide-open>
          <svg class="i" aria-hidden="true"><use href="#i-book"/></svg>
          <span>Tata Cara Pendaftaran</span>
        </button>
      </div>
    </div>

    <div class="ppdb-welcome-banner__summary">
      <div class="ppdb-summary-item">
        <span>Status Seleksi</span>
        <strong class="ppdb-status-pill ppdb-status-pill--{{ $pendaftaran->status_seleksi }}">{{ $pendaftaran->statusSeleksiLabel() }}</strong>
      </div>
      <div class="ppdb-summary-item">
        <span>Jalur Pilihan</span>
        <strong>{{ $pendaftaran->jalur?->nama ?? 'Belum dipilih' }}</strong>
      </div>
      <div class="ppdb-summary-item">
        <span>Status Pembayaran</span>
        <strong>{{ $pendaftaran->pembayaran ? $pendaftaran->pembayaran->statusLabel() : ($pendaftaran->jalur && $pendaftaran->jalur->isGratis() ? 'Gratis (Terkonfirmasi)' : 'Belum ada') }}</strong>
      </div>
      <div class="ppdb-summary-item">
        <span>Berkas Diunggah</span>
        <strong>{{ $pendaftaran->berkas->count() }} / 4 Wajib</strong>
      </div>
    </div>
  </section>

  @if($pendaftaran->catatan_panitia)
    <div class="form-alert {{ $pendaftaran->status_seleksi === 'ditolak' ? 'form-alert--error' : 'form-alert--ok' }}" role="status">
      <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
      <div class="form-alert__body">
        <strong>Catatan Panitia PPDB:</strong> {{ $pendaftaran->catatan_panitia }}
      </div>
    </div>
  @endif

  <section class="adm-panel ppdb-flow-panel" aria-labelledby="ppdb-flow-title">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Tahapan Berurutan</span>
        <h2 class="adm-panel__title" id="ppdb-flow-title">Alur Pendaftaran Calon Siswa</h2>
        <p class="adm-panel__sub">Selesaikan setiap tahap untuk membuka kunci tahapan berikutnya.</p>
      </div>
      <div class="ppdb-progress-pill" aria-label="Progres pendaftaran {{ $progressPercent }} persen">
        <span>Progres</span>
        <strong>{{ $completedCount }}/{{ $totalSteps }} ({{ $progressPercent }}%)</strong>
      </div>
    </div>

    <ol class="ppdb-flow-grid">
      @foreach($steps as $step)
        <li class="ppdb-flow-card is-{{ $step['state'] }}">
          <div class="ppdb-flow-card__top">
            <span class="ppdb-flow-card__num">
              @if($step['state'] === 'completed')
                <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
              @elseif($step['state'] === 'locked')
                <svg class="i" aria-hidden="true"><use href="#i-lock"/></svg>
              @else
                {{ $step['number'] }}
              @endif
            </span>

            <span class="ppdb-flow-card__badge">
              @if($step['state'] === 'completed')
                Selesai
              @elseif($step['state'] === 'active')
                Tahap Aktif
              @else
                Terkunci
              @endif
            </span>
          </div>

          <div class="ppdb-flow-card__body">
            <h3>{{ $step['title'] }}</h3>
            <p>{{ $step['subtitle'] }}</p>
          </div>

          <div class="ppdb-flow-card__foot">
            @if($step['state'] === 'locked')
              <span class="ppdb-flow-card__lock-note">Selesaikan tahap sebelumnya</span>
            @else
              <a href="{{ route($step['route']) }}" class="btn {{ $step['state'] === 'active' ? 'btn--primary' : 'btn--ghost' }} btn--sm">
                <span>{{ $step['state'] === 'completed' ? 'Tinjau / Ubah' : 'Kerjakan Tahap Ini' }}</span>
                <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
              </a>
            @endif
          </div>
        </li>
      @endforeach
    </ol>
  </section>

  <section class="ppdb-info-duo" aria-label="Ringkasan akun dan kontak panitia">
    <article class="adm-panel">
      <div class="adm-panel__head">
        <div>
          <h2 class="adm-panel__title">Identitas Akun Terdaftar</h2>
          <p class="adm-panel__sub">Data dasar saat pembuatan akun PPDB.</p>
        </div>
        @if($pendaftaran->canAccessBiodata())
          <a href="{{ route('ppdb.portal.data-diri') }}" class="btn btn--ghost btn--sm">Lengkapi Biodata</a>
        @endif
      </div>

      <div class="adm-panel__body">
        <dl class="ppdb-kv-list">
          <div>
            <dt>Nomor Pendaftaran</dt>
            <dd><strong>{{ $pendaftaran->no_pendaftaran }}</strong></dd>
          </div>
          <div>
            <dt>Nama Lengkap</dt>
            <dd>{{ $account->nama_lengkap }}</dd>
          </div>
          <div>
            <dt>NIK (16 Digit)</dt>
            <dd>{{ $account->nik }}</dd>
          </div>
          <div>
            <dt>Tanggal Lahir</dt>
            <dd>{{ $account->tanggal_lahir?->format('d/m/Y') ?? '-' }}</dd>
          </div>
          <div>
            <dt>Email Akun</dt>
            <dd>{{ $account->email }}</dd>
          </div>
          <div>
            <dt>WhatsApp Aktif</dt>
            <dd>+{{ $account->whatsapp }}</dd>
          </div>
        </dl>
      </div>
    </article>

    <article class="adm-panel">
      <div class="adm-panel__head">
        <div>
          <h2 class="adm-panel__title">Layanan Panitia PPDB</h2>
          <p class="adm-panel__sub">Pendampingan langsung dari Sekretariat SMP PGRI 4 Waru.</p>
        </div>
      </div>

      <div class="adm-panel__body">
        <p style="margin:0 0 1rem;color:var(--ink-2);font-size:0.9rem;line-height:1.6;">Mengalami kendala saat memilih jalur, mengunggah dokumen, atau membutuhkan konfirmasi cepat? Hubungi Panitia PPDB dengan menyebutkan Nomor Pendaftaran <strong>{{ $pendaftaran->no_pendaftaran }}</strong>.</p>

        <dl class="ppdb-kv-list" style="margin-bottom:1.15rem;">
          <div>
            <dt>Jam Pelayanan</dt>
            <dd>{{ $siteSettings['jam_layanan'] ?? 'Senin - Sabtu, 07.00 - 14.00 WIB' }}</dd>
          </div>
          <div>
            <dt>Telepon &amp; WA TU</dt>
            <dd>{{ $siteSettings['telepon'] ?? '(031) 8538447' }} / +{{ $siteSettings['whatsapp_ppdb'] ?? '62318538447' }}</dd>
          </div>
          <div>
            <dt>Lokasi Loket</dt>
            <dd>Sekretariat PPDB SMP PGRI 4 Waru</dd>
          </div>
        </dl>

        <div class="ppdb-support-actions">
          <a href="https://wa.me/{{ $siteSettings['whatsapp_ppdb'] ?? '62318538447' }}?text={{ urlencode('Halo Panitia PPDB SMP PGRI 4 Waru, saya ' . $account->nama_lengkap . ' (No. Pendaftaran: ' . $pendaftaran->no_pendaftaran . ') ingin bertanya mengenai tahapan pendaftaran.') }}" target="_blank" rel="noopener" class="btn btn--primary btn--sm">
            <svg class="i" aria-hidden="true"><use href="#i-wa"/></svg>
            <span>Chat WhatsApp Panitia</span>
          </a>
          <a href="{{ route('ppdb.portal.bantuan') }}" class="btn btn--ghost btn--sm">
            <svg class="i" aria-hidden="true"><use href="#i-question"/></svg>
            <span>Pusat Bantuan &amp; FAQ</span>
          </a>
        </div>
      </div>
    </article>
  </section>
</div>
@endsection
