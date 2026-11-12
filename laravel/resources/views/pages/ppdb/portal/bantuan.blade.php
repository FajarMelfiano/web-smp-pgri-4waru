@extends('layouts.ppdb-portal')

@section('title', 'Pusat Bantuan PPDB | ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))

@section('content')
<div class="ppdb-portal-stack">
  <section class="adm-panel" aria-labelledby="ppdb-bantuan-heading">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Layanan Informasi &amp; Pendampingan</span>
        <h1 class="adm-panel__title" id="ppdb-bantuan-heading">Pusat Bantuan PPDB Online</h1>
        <p class="adm-panel__sub">Pertanyaan yang sering diajukan oleh calon siswa dan orang tua serta kontak resmi Sekretariat PPDB.</p>
      </div>
      <button type="button" class="btn btn--ghost btn--sm" data-ppdb-guide-open>
        <svg class="i" aria-hidden="true"><use href="#i-book"/></svg>
        <span>Buka Tata Cara Pendaftaran</span>
      </button>
    </div>

    <div class="ppdb-info-duo">
      <div class="ppdb-faq-list">
        <article class="ppdb-faq-item">
          <h2>Mengapa menu Data Diri atau Berkas masih terkunci?</h2>
          <p>Tahapan portal PPDB berjalan berurutan. Pastikan Anda telah memilih jalur pendaftaran di menu <strong>Pendaftaran Jalur</strong> dan menyelesaikan konfirmasi di menu <strong>Pembayaran / Konfirmasi</strong>.</p>
        </article>

        <article class="ppdb-faq-item">
          <h2>Bagaimana jika SKL / Ijazah SD belum terbit?</h2>
          <p>Pada slot berkas <strong>SKL / Rapor SD</strong>, Anda diperbolehkan mengunggah hasil pindai (scan/foto jelas) nilai rapor kelas 5 atau kelas 6 semester terakhir terlebih dahulu.</p>
        </article>

        <article class="ppdb-faq-item">
          <h2>Apakah bisa mengubah berkas jika ada revisi dari panitia?</h2>
          <p>Bisa. Buka kembali menu <strong>Berkas</strong>, lihat catatan verifikator, lalu pilih file baru dan tekan tombol <strong>Ganti Berkas</strong>.</p>
        </article>

        <article class="ppdb-faq-item">
          <h2>Kapan jadwal layanan Loket Sekretariat PPDB di sekolah?</h2>
          <p>Sekretariat PPDB {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }} melayani konsultasi dan verifikasi fisik setiap <strong>{{ $siteSettings['jam_layanan'] ?? 'Senin sampai Sabtu, pukul 07.00 sampai 14.00 WIB' }}</strong>.</p>
        </article>
      </div>

      <aside class="ppdb-contact-box">
        <h2>Kontak Sekretariat PPDB</h2>
        <dl class="ppdb-kv-list">
          <div>
            <dt>Alamat Sekolah</dt>
            <dd>{{ $siteSettings['alamat'] ?? 'Jl. Anggrek No. 109, Kureksari, Kec. Waru, Kab. Sidoarjo' }}</dd>
          </div>
          <div>
            <dt>Telepon TU</dt>
            <dd>{{ $siteSettings['telepon'] ?? '(031) 8538447' }}</dd>
          </div>
          <div>
            <dt>WhatsApp PPDB</dt>
            <dd>+{{ $siteSettings['whatsapp_ppdb'] ?? '62318538447' }}</dd>
          </div>
          <div>
            <dt>Email Resmi</dt>
            <dd>{{ $siteSettings['email'] ?? 'smppgri4waru@gmail.com' }}</dd>
          </div>
          <div>
            <dt>Jam Layanan</dt>
            <dd>{{ $siteSettings['jam_layanan'] ?? 'Senin sampai Sabtu, 07.00 sampai 14.00 WIB' }}</dd>
          </div>
        </dl>

        <div class="ppdb-support-actions" style="margin-top:1.25rem">
          <a href="https://wa.me/{{ $siteSettings['whatsapp_ppdb'] ?? '62318538447' }}?text={{ urlencode('Halo Panitia PPDB SMP PGRI 4 Waru, saya ' . $account->nama_lengkap . ' (No. Pendaftaran: ' . $pendaftaran->no_pendaftaran . ') membutuhkan bantuan terkait pendaftaran online.') }}" target="_blank" rel="noopener" class="btn btn--primary ppdb-btn-block">
            <svg class="i" aria-hidden="true"><use href="#i-wa"/></svg>
            <span>Hubungi Panitia via WhatsApp</span>
          </a>
        </div>
      </aside>
    </div>
  </section>
</div>
@endsection
