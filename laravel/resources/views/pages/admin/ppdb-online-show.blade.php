@extends('layouts.admin')

@section('title', 'Detail Pendaftar ' . $pendaftaran->no_pendaftaran . ' | Admin SMP PGRI 4 Waru')
@section('crumb', 'Verifikasi Pendaftar Online')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>{{ $pendaftaran->account?->nama_lengkap ?? 'Calon Siswa' }} ({{ $pendaftaran->no_pendaftaran }})</h1>
      <p>Terdaftar pada {{ $pendaftaran->created_at?->format('d M Y H:i') }} · Jalur: <strong>{{ $pendaftaran->jalur?->nama ?? 'Belum dipilih' }}</strong></p>
    </div>
    <div class="adm-actions">
      <a href="{{ route('admin.ppdb', ['tab' => 'online']) }}" class="btn btn--ghost btn--sm">
        <svg class="i" aria-hidden="true"><use href="#i-arrow-left"/></svg>
        <span>Kembali ke Daftar</span>
      </a>
      @if($pendaftaran->account?->whatsapp)
        <a href="https://wa.me/{{ $pendaftaran->account->whatsapp }}" target="_blank" rel="noopener" class="btn btn--primary btn--sm">
          <svg class="i" aria-hidden="true"><use href="#i-wa"/></svg>
          <span>Hubungi WhatsApp Siswa</span>
        </a>
      @endif
    </div>
  </div>

  @if(session('status'))
    <div class="adm-alert" role="status">
      <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  <div class="adm-grid-2">
    <section class="adm-panel" aria-labelledby="verif-seleksi-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="verif-seleksi-heading">1. Keputusan Seleksi &amp; Catatan Panitia</h2>
          <span class="ppdb-status-pill ppdb-status-pill--{{ $pendaftaran->status_seleksi }}">{{ $pendaftaran->statusSeleksiLabel() }}</span>
        </div>
      </div>
      <div class="adm-panel__body">
        <form method="POST" action="{{ route('admin.ppdb.online.status', $pendaftaran) }}">
          @csrf
          @method('PATCH')
          <div class="field">
            <label for="adm-status-seleksi">Status Seleksi Calon Siswa</label>
            <select id="adm-status-seleksi" name="status_seleksi" required>
              <option value="proses" @selected($pendaftaran->status_seleksi === 'proses')>Dalam Proses Verifikasi</option>
              <option value="terverifikasi" @selected($pendaftaran->status_seleksi === 'terverifikasi')>Berkas Terverifikasi</option>
              <option value="diterima" @selected($pendaftaran->status_seleksi === 'diterima')>Diterima (Lulus Seleksi)</option>
              <option value="ditolak" @selected($pendaftaran->status_seleksi === 'ditolak')>Belum Diterima / Ditolak</option>
            </select>
          </div>
          <div class="field">
            <label for="adm-catatan-panitia">Catatan Resmi Panitia (Ditampilkan di Pengumuman Siswa)</label>
            <textarea id="adm-catatan-panitia" name="catatan_panitia" rows="3" placeholder="Contoh: Selamat! Silakan melakukan daftar ulang di Loket TU pada tanggal ...">{{ old('catatan_panitia', $pendaftaran->catatan_panitia) }}</textarea>
          </div>
          <button type="submit" class="btn btn--primary btn--sm">Simpan Status Seleksi</button>
        </form>
      </div>
    </section>

    <section class="adm-panel" aria-labelledby="verif-bayar-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="verif-bayar-heading">2. Verifikasi Pembayaran Formulir</h2>
          @if($pendaftaran->pembayaran)
            <span class="ppdb-status-pill ppdb-status-pill--{{ $pendaftaran->pembayaran->status }}">{{ $pendaftaran->pembayaran->statusLabel() }}</span>
          @endif
        </div>
      </div>
      <div class="adm-panel__body">
        @if(!$pendaftaran->pembayaran)
          <p style="color:var(--ink-3)">Siswa belum memiliki tagihan / belum memilih jalur pendaftaran.</p>
        @else
          <dl class="ppdb-kv-list" style="margin-bottom:1rem">
            <div>
              <dt>Kode Invoice</dt>
              <dd><strong>{{ $pendaftaran->pembayaran->kode_invoice }}</strong></dd>
            </div>
            <div>
              <dt>Nominal &amp; Metode</dt>
              <dd>{{ $pendaftaran->pembayaran->nominalLabel() }} ({{ $pendaftaran->pembayaran->metodeLabel() }})</dd>
            </div>
            <div>
              <dt>Catatan Siswa</dt>
              <dd>{{ $pendaftaran->pembayaran->catatan_siswa ?: '-' }}</dd>
            </div>
            <div>
              <dt>Bukti Pembayaran</dt>
              <dd>
                @if($pendaftaran->pembayaran->bukti_path)
                  <a href="{{ route('ppdb.portal.pembayaran.bukti', $pendaftaran->pembayaran) }}" target="_blank" rel="noopener" class="btn btn--ghost btn--sm">Buka Bukti Transfer</a>
                @else
                  <span>Tidak ada lampiran bukti</span>
                @endif
              </dd>
            </div>
          </dl>

          <form method="POST" action="{{ route('admin.ppdb.pembayaran.verifikasi', $pendaftaran->pembayaran) }}">
            @csrf
            @method('PATCH')
            <div class="field">
              <label for="adm-pay-status">Status Pembayaran</label>
              <select id="adm-pay-status" name="status" required>
                <option value="menunggu" @selected($pendaftaran->pembayaran->status === 'menunggu')>Menunggu Verifikasi</option>
                <option value="lunas" @selected($pendaftaran->pembayaran->status === 'lunas')>Lunas / Terkonfirmasi</option>
                <option value="ditolak" @selected($pendaftaran->pembayaran->status === 'ditolak')>Ditolak (Minta Unggah Ulang)</option>
              </select>
            </div>
            <div class="field">
              <label for="adm-pay-note">Catatan Petugas TU</label>
              <input id="adm-pay-note" name="catatan_admin" type="text" value="{{ old('catatan_admin', $pendaftaran->pembayaran->catatan_admin) }}" placeholder="Keterangan verifikasi pembayaran">
            </div>
            <button type="submit" class="btn btn--primary btn--sm">Perbarui Status Pembayaran</button>
          </form>
        @endif
      </div>
    </section>
  </div>

  <section class="adm-panel" aria-labelledby="verif-berkas-heading">
    <div class="adm-panel__head">
      <div class="adm-panel__title">
        <h2 id="verif-berkas-heading">3. Pemeriksaan Berkas Persyaratan ({{ $pendaftaran->berkas->count() }} Berkas)</h2>
      </div>
    </div>

    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Jenis Dokumen</th>
            <th>Berkas &amp; Pratinjau</th>
            <th>Status Verifikasi</th>
            <th>Tindakan Verifikator</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pendaftaran->berkas as $berkas)
            <tr>
              <td>
                <span class="adm-cell-primary">{{ $berkas->jenisLabel() }}</span>
                <span class="adm-cell-sub">Diunggah {{ $berkas->updated_at?->format('d M Y H:i') }}</span>
              </td>
              <td>
                <span class="adm-cell-primary">{{ $berkas->original_name }} ({{ $berkas->fileSizeLabel() }})</span>
                <span class="adm-cell-sub" style="margin-top:0.25rem">
                  <a href="{{ route('ppdb.portal.berkas.download', $berkas) }}" target="_blank" rel="noopener" style="color:var(--red);font-weight:700">Buka / Unduh Berkas</a>
                </span>
              </td>
              <td>
                <span class="ppdb-status-pill ppdb-status-pill--{{ $berkas->status }}">{{ $berkas->statusLabel() }}</span>
              </td>
              <td>
                <form method="POST" action="{{ route('admin.ppdb.berkas.verifikasi', $berkas) }}" class="adm-inline-form">
                  @csrf
                  @method('PATCH')
                  <select class="adm-select" name="status" aria-label="Status berkas {{ $berkas->jenisLabel() }}">
                    <option value="menunggu" @selected($berkas->status === 'menunggu')>Menunggu</option>
                    <option value="valid" @selected($berkas->status === 'valid')>Valid / Sesuai</option>
                    <option value="revisi" @selected($berkas->status === 'revisi')>Perlu Revisi</option>
                  </select>
                  <input class="adm-input" type="text" name="catatan_admin" value="{{ $berkas->catatan_admin }}" placeholder="Catatan revisi" style="max-width:160px">
                  <button type="submit" class="btn btn--ghost btn--sm">Simpan</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align:center;padding:2rem;color:var(--ink-3)">Siswa belum mengunggah berkas persyaratan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <div class="adm-grid-2">
    <section class="adm-panel" aria-labelledby="detail-biodata-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="detail-biodata-heading">4. Biodata Lengkap Calon Siswa &amp; Orang Tua</h2>
        </div>
      </div>
      <div class="adm-panel__body">
        <dl class="ppdb-kv-list">
          <div>
            <dt>Nama Lengkap</dt>
            <dd><strong>{{ $pendaftaran->account?->nama_lengkap }}</strong></dd>
          </div>
          <div>
            <dt>NIK / NISN</dt>
            <dd>{{ $pendaftaran->account?->nik }} / {{ $pendaftaran->nisn ?: '-' }}</dd>
          </div>
          <div>
            <dt>Email &amp; WhatsApp</dt>
            <dd>{{ $pendaftaran->account?->email }} · +{{ $pendaftaran->account?->whatsapp }}</dd>
          </div>
          <div>
            <dt>Tempat, Tanggal Lahir</dt>
            <dd>{{ $pendaftaran->tempat_lahir ?: '-' }}, {{ $pendaftaran->account?->tanggal_lahir?->format('d-m-Y') ?? '-' }}</dd>
          </div>
          <div>
            <dt>Jenis Kelamin / Agama</dt>
            <dd>{{ $pendaftaran->jenis_kelamin ?: '-' }} / {{ $pendaftaran->agama ?: '-' }}</dd>
          </div>
          <div>
            <dt>Asal SD / MI</dt>
            <dd>{{ $pendaftaran->asal_sd ?: '-' }} (NPSN: {{ $pendaftaran->npsn_sd ?: '-' }} · Lulus: {{ $pendaftaran->tahun_lulus_sd ?: '-' }})</dd>
          </div>
          <div>
            <dt>Alamat Domisili</dt>
            <dd>{{ $pendaftaran->alamat_lengkap ?: '-' }}, {{ $pendaftaran->kelurahan_desa }}, {{ $pendaftaran->kecamatan }}, {{ $pendaftaran->kabupaten_kota }}</dd>
          </div>
          <div>
            <dt>Data Ayah</dt>
            <dd>{{ $pendaftaran->nama_ayah ?: '-' }} ({{ $pendaftaran->pekerjaan_ayah ?: '-' }} · HP: {{ $pendaftaran->no_hp_ayah ?: '-' }})</dd>
          </div>
          <div>
            <dt>Data Ibu</dt>
            <dd>{{ $pendaftaran->nama_ibu ?: '-' }} ({{ $pendaftaran->pekerjaan_ibu ?: '-' }} · HP: {{ $pendaftaran->no_hp_ibu ?: '-' }})</dd>
          </div>
          <div>
            <dt>Penghasilan Orang Tua</dt>
            <dd>{{ $pendaftaran->penghasilan_ortu ?: '-' }}</dd>
          </div>
        </dl>
      </div>
    </section>

    <section class="adm-panel" aria-labelledby="detail-kuesioner-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="detail-kuesioner-heading">5. Jawaban Kuesioner Siswa</h2>
        </div>
      </div>
      <div class="adm-panel__body">
        @php
          $kues = $pendaftaran->kuesioner ?? [];
        @endphp
        @if(empty($kues))
          <p style="color:var(--ink-3)">Siswa belum mengisi kuesioner.</p>
        @else
          <dl class="ppdb-kv-list">
            <div>
              <dt>Sumber Informasi PPDB</dt>
              <dd>{{ $kues['sumber_info'] ?? '-' }}</dd>
            </div>
            <div>
              <dt>Alasan Memilih Sekolah</dt>
              <dd>{{ $kues['alasan_memilih'] ?? '-' }}</dd>
            </div>
            <div>
              <dt>Minat Ekstrakurikuler</dt>
              <dd>{{ !empty($kues['minat_ekstrakurikuler']) ? implode(', ', (array) $kues['minat_ekstrakurikuler']) : '-' }}</dd>
            </div>
            <div>
              <dt>Harapan / Pesan</dt>
              <dd>{{ $kues['saran'] ?? '-' }}</dd>
            </div>
          </dl>
        @endif
      </div>
    </section>
  </div>
@endsection
