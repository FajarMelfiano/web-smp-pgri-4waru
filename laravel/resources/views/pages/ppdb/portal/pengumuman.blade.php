@extends('layouts.ppdb-portal')

@section('title', 'Pengumuman Seleksi PPDB | ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))

@section('content')
<div class="ppdb-portal-stack">
  <section class="adm-panel" aria-labelledby="ppdb-pengumuman-heading">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Hasil Verifikasi &amp; Seleksi</span>
        <h1 class="adm-panel__title" id="ppdb-pengumuman-heading">Pengumuman Status PPDB</h1>
        <p class="adm-panel__sub">Status resmi penerimaan peserta didik baru untuk nomor pendaftaran <strong>{{ $pendaftaran->no_pendaftaran }}</strong>.</p>
      </div>
    </div>

    <div class="ppdb-announce-banner is-{{ $pendaftaran->status_seleksi }}">
      <div class="ppdb-announce-banner__badge">
        <span class="ppdb-status-pill ppdb-status-pill--{{ $pendaftaran->status_seleksi }}">
          {{ $pendaftaran->statusSeleksiLabel() }}
        </span>
      </div>

      @if($pendaftaran->status_seleksi === 'diterima')
        <h2>Selamat! Anda Dinyatakan DITERIMA di {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}.</h2>
        <p>Calon siswa <strong>{{ $account->nama_lengkap }}</strong> ({{ $pendaftaran->no_pendaftaran }}) dinyatakan lulus seleksi jalur <strong>{{ $pendaftaran->jalur?->nama ?? '-' }}</strong> Tahun Ajaran {{ $pendaftaran->tahun_ajaran }}.</p>
      @elseif($pendaftaran->status_seleksi === 'terverifikasi')
        <h2>Berkas &amp; Pendaftaran Anda Telah Terverifikasi.</h2>
        <p>Seluruh persyaratan administrasi Anda telah diperiksa oleh Panitia PPDB. Silakan menunggu penetapan kelulusan akhir atau hubungi Sekretariat untuk jadwal daftar ulang.</p>
      @elseif($pendaftaran->status_seleksi === 'ditolak')
        <h2>Mohon Maaf, Pendaftaran Belum Dapat Diterima.</h2>
        <p>Berdasarkan hasil verifikasi panitia, pendaftaran Anda saat ini belum memenuhi ketentuan seleksi. Silakan periksa catatan panitia di bawah atau hubungi Sekretariat PPDB.</p>
      @else
        <h2>Pendaftaran Anda Sedang Dalam Proses Verifikasi.</h2>
        <p>Pastikan Anda telah menyelesaikan seluruh 6 tahapan pendaftaran hingga cetak Kartu Peserta agar Panitia PPDB dapat segera memverifikasi berkas Anda.</p>
      @endif

      @if($pendaftaran->catatan_panitia)
        <div class="ppdb-announce-note">
          <strong>Pesan Resmi Panitia PPDB:</strong>
          <p>{{ $pendaftaran->catatan_panitia }}</p>
        </div>
      @endif

      <div class="ppdb-announce-actions">
        @if($pendaftaran->canAccessKartuPeserta())
          <a href="{{ route('ppdb.portal.kartu-peserta') }}" class="btn btn--primary">
            <svg class="i" aria-hidden="true"><use href="#i-print"/></svg>
            <span>Cetak Kartu Peserta</span>
          </a>
        @else
          <a href="{{ route('ppdb.portal.beranda') }}" class="btn btn--primary">
            <span>Lengkapi Tahapan Pendaftaran</span>
            <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
          </a>
        @endif

        <a href="https://wa.me/{{ $siteSettings['whatsapp_ppdb'] ?? '62318538447' }}?text={{ urlencode('Halo Panitia PPDB SMP PGRI 4 Waru, saya ' . $account->nama_lengkap . ' (' . $pendaftaran->no_pendaftaran . ') ingin menanyakan informasi daftar ulang / pengumuman seleksi.') }}" target="_blank" rel="noopener" class="btn btn--ghost">
          <svg class="i" aria-hidden="true"><use href="#i-wa"/></svg>
          <span>Hubungi Panitia via WhatsApp</span>
        </a>
      </div>
    </div>

    <div class="ppdb-info-duo" style="margin-top:1.35rem">
      <div class="ppdb-faq-item">
        <h2>Tahapan Verifikasi Panitia</h2>
        <p style="margin-bottom:0.85rem">Pantau posisi pendaftaran Anda pada alur pemeriksaan berikut.</p>

        @php
          $verifySteps = [
            ['label' => 'Berkas persyaratan diunggah', 'done' => $pendaftaran->hasCompletedBerkas(), 'note' => $pendaftaran->hasCompletedBerkas() ? '4 berkas wajib sudah masuk ke sistem.' : 'Lengkapi 4 berkas wajib di menu Berkas.'],
            ['label' => 'Konfirmasi pembayaran formulir', 'done' => $pendaftaran->hasCompletedPaymentStep(), 'note' => $pendaftaran->hasCompletedPaymentStep() ? 'Pembayaran sudah tercatat atau jalur bebas biaya.' : 'Selesaikan konfirmasi di menu Pembayaran.'],
            ['label' => 'Validasi berkas oleh panitia', 'done' => $pendaftaran->areAllBerkasVerified(), 'note' => $pendaftaran->areAllBerkasVerified() ? 'Seluruh berkas dinyatakan valid.' : 'Menunggu pemeriksaan petugas PPDB.'],
            ['label' => 'Penetapan hasil seleksi', 'done' => $pendaftaran->status_seleksi !== 'proses', 'note' => $pendaftaran->status_seleksi !== 'proses' ? 'Hasil seleksi sudah ditetapkan panitia.' : 'Panitia menetapkan hasil setelah berkas valid.'],
          ];
        @endphp

        <ol class="ppdb-guide-list">
          @foreach($verifySteps as $index => $step)
            <li>
              <span class="ppdb-guide-list__num {{ $step['done'] ? '' : 'is-pending' }}">
                @if($step['done'])
                  <svg class="i" style="width:15px;height:15px" aria-hidden="true"><use href="#i-check"/></svg>
                  <span class="sr-only">Selesai</span>
                @else
                  {{ $index + 1 }}
                @endif
              </span>
              <div>
                <strong>{{ $step['label'] }}</strong>
                <p>{{ $step['note'] }}</p>
              </div>
            </li>
          @endforeach
        </ol>
      </div>

      <aside class="ppdb-contact-box">
        <h2>Sekretariat PPDB</h2>
        <dl class="ppdb-kv-list">
          <div>
            <dt>Telepon TU</dt>
            <dd>{{ $siteSettings['telepon'] ?? '(031) 8538447' }}</dd>
          </div>
          <div>
            <dt>WhatsApp PPDB</dt>
            <dd>+{{ $siteSettings['whatsapp_ppdb'] ?? '62318538447' }}</dd>
          </div>
          <div>
            <dt>Jam Layanan</dt>
            <dd>{{ $siteSettings['jam_layanan'] ?? 'Senin sampai Sabtu, 07.00 sampai 14.00 WIB' }}</dd>
          </div>
          <div>
            <dt>Alamat</dt>
            <dd>{{ $siteSettings['alamat'] ?? 'Jl. Anggrek No. 109, Kureksari, Waru, Sidoarjo' }}</dd>
          </div>
        </dl>

        <div class="ppdb-support-actions" style="margin-top:1.25rem">
          <a href="{{ route('ppdb.portal.bantuan') }}" class="btn btn--ghost ppdb-btn-block">
            <span>Buka Pusat Bantuan</span>
          </a>
        </div>
      </aside>
    </div>
  </section>
</div>
@endsection
