@extends('layouts.admin')

@section('title', 'Ringkasan Dasbor — Admin SMP PGRI 4 Waru')
@section('description', 'Dasbor utama pengelolaan PPDB, publikasi berita, dan kustomisasi situs SMP PGRI 4 Waru.')
@section('crumb', 'Ringkasan Operasional')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Ringkasan operasional sekolah</h1>
      <p>Pantau pendaftaran siswa baru, pengumuman sekolah, serta pintasan pengelolaan seluruh modul situs.</p>
    </div>
    <div class="adm-actions">
      <a class="btn btn--ghost btn--sm" href="{{ route('admin.pengaturan') }}">
        <svg class="i" aria-hidden="true"><use href="#i-settings"/></svg>
        <span>Kustomisasi Situs</span>
      </a>
      <a class="btn btn--primary btn--sm" href="{{ route('beranda') }}">
        <span>Buka Situs Publik</span>
        <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg>
      </a>
    </div>
  </div>

  @if(session('status'))
    <div class="adm-alert" role="status">
      <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  <section aria-labelledby="kpi-heading">
    <h2 id="kpi-heading" class="sr-only">Metrik utama sekolah</h2>
    <div class="adm-kpi-grid">
      <article class="adm-kpi">
        <div>
          <span class="adm-kpi__label">Total Pendaftar</span>
          <b class="adm-kpi__val">{{ $stats['total'] ?? 0 }}</b>
          <span class="adm-kpi__sub">Tahun ajaran {{ $settings['tahun_ajaran'] ?? '2026/2027' }}</span>
        </div>
        <span class="adm-kpi__icon" aria-hidden="true"><svg class="i"><use href="#i-users"/></svg></span>
      </article>

      <article class="adm-kpi adm-kpi--red">
        <div>
          <span class="adm-kpi__label">Berkas Diterima</span>
          <b class="adm-kpi__val">{{ $stats['diterima'] ?? 0 }}</b>
          <span class="adm-kpi__sub">Telah diverifikasi petugas</span>
        </div>
        <span class="adm-kpi__icon" aria-hidden="true"><svg class="i"><use href="#i-seal-check"/></svg></span>
      </article>

      <article class="adm-kpi">
        <div>
          <span class="adm-kpi__label">Perlu Tindak Lanjut</span>
          <b class="adm-kpi__val">{{ $stats['menunggu'] ?? 0 }}</b>
          <span class="adm-kpi__sub">{{ $stats['baru'] ?? 0 }} baru · {{ $stats['dihubungi'] ?? 0 }} dihubungi</span>
        </div>
        <span class="adm-kpi__icon" aria-hidden="true"><svg class="i"><use href="#i-clock"/></svg></span>
      </article>

      <article class="adm-kpi">
        <div>
          <span class="adm-kpi__label">Publikasi Berita</span>
          <b class="adm-kpi__val">{{ $stats['berita_total'] ?? 0 }}</b>
          <span class="adm-kpi__sub">{{ $stats['berita_terbit'] ?? 0 }} tayang · {{ $stats['berita_draf'] ?? 0 }} draf</span>
        </div>
        <span class="adm-kpi__icon" aria-hidden="true"><svg class="i"><use href="#i-newspaper"/></svg></span>
      </article>
    </div>
  </section>

  <section aria-labelledby="pintasan-heading">
    <div class="adm-page-head" style="margin-bottom:0.85rem">
      <div>
        <h2 id="pintasan-heading" style="font-size:1.05rem;font-weight:800;letter-spacing:-0.015em">Akses Cepat Modul</h2>
      </div>
    </div>
    <div class="adm-quick-grid">
      <a class="adm-quick-card" href="{{ route('admin.ppdb') }}">
        <span class="adm-quick-card__icon" aria-hidden="true"><svg class="i"><use href="#i-users"/></svg></span>
        <span class="adm-quick-card__body">
          <b class="adm-quick-card__title">Pendaftar PPDB</b>
          <span class="adm-quick-card__desc">Saring pendaftar, ubah status verifikasi, hubungi WA wali, dan unduh CSV.</span>
        </span>
        <svg class="adm-quick-card__arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
      </a>

      <a class="adm-quick-card" href="{{ route('admin.berita') }}">
        <span class="adm-quick-card__icon" aria-hidden="true"><svg class="i"><use href="#i-newspaper"/></svg></span>
        <span class="adm-quick-card__body">
          <b class="adm-quick-card__title">Berita &amp; Pengumuman</b>
          <span class="adm-quick-card__desc">Tulis kabar sekolah baru, unggah foto sampul, dan atur status tayang/draf.</span>
        </span>
        <svg class="adm-quick-card__arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
      </a>

      <a class="adm-quick-card" href="{{ route('admin.galeri') }}">
        <span class="adm-quick-card__icon" aria-hidden="true"><svg class="i"><use href="#i-image"/></svg></span>
        <span class="adm-quick-card__body">
          <b class="adm-quick-card__title">Galeri Foto Sekolah</b>
          <span class="adm-quick-card__desc">Tambahkan dan kelola foto kegiatan belajar yang tampil di Beranda &amp; halaman Berita ({{ $stats['galeri_total'] ?? 0 }} foto).</span>
        </span>
        <svg class="adm-quick-card__arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
      </a>

      <a class="adm-quick-card" href="{{ route('admin.alumni') }}">
        <span class="adm-quick-card__icon" aria-hidden="true"><svg class="i"><use href="#i-chat"/></svg></span>
        <span class="adm-quick-card__body">
          <b class="adm-quick-card__title">Moderasi Ulasan Alumni</b>
          <span class="adm-quick-card__desc">Verifikasi testimoni yang dikirim alumni ({{ $stats['alumni_terbit'] ?? 0 }} tayang · {{ $stats['alumni_menunggu'] ?? 0 }} menunggu).</span>
        </span>
        <svg class="adm-quick-card__arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
      </a>

      <a class="adm-quick-card" href="{{ route('admin.pengaturan') }}">
        <span class="adm-quick-card__icon" aria-hidden="true"><svg class="i"><use href="#i-settings"/></svg></span>
        <span class="adm-quick-card__body">
          <b class="adm-quick-card__title">Pengaturan &amp; Kustomisasi Situs</b>
          <span class="adm-quick-card__desc">Unggah logo, foto beranda, brosur PPDB, dan ubah seluruh teks identitas.</span>
        </span>
        <svg class="adm-quick-card__arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
      </a>

      <a class="adm-quick-card" href="{{ route('admin.akademik') }}">
        <span class="adm-quick-card__icon" aria-hidden="true"><svg class="i"><use href="#i-book"/></svg></span>
        <span class="adm-quick-card__body">
          <b class="adm-quick-card__title">Kurikulum &amp; Profil</b>
          <span class="adm-quick-card__desc">Tinjau 11 mata pelajaran struktur SMP dan data pokok sekolah.</span>
        </span>
        <svg class="adm-quick-card__arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
      </a>

      <a class="adm-quick-card" href="{{ route('admin.petugas') }}">
        <span class="adm-quick-card__icon" aria-hidden="true"><svg class="i"><use href="#i-shield"/></svg></span>
        <span class="adm-quick-card__body">
          <b class="adm-quick-card__title">Petugas &amp; Hak Akses</b>
          <span class="adm-quick-card__desc">Kelola akun administrator dan operator tata usaha ({{ $stats['petugas_total'] ?? 0 }} akun).</span>
        </span>
        <svg class="adm-quick-card__arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
      </a>

      <a class="adm-quick-card" href="{{ route('admin.ekspor.pendaftar') }}">
        <span class="adm-quick-card__icon" aria-hidden="true"><svg class="i"><use href="#i-download"/></svg></span>
        <span class="adm-quick-card__body">
          <b class="adm-quick-card__title">Ekspor Data PPDB (CSV)</b>
          <span class="adm-quick-card__desc">Unduh rekapitulasi lengkap pendaftar untuk arsip panitia sekolah.</span>
        </span>
        <svg class="adm-quick-card__arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
      </a>
    </div>
  </section>

  @php
    $totalPpdb = max(1, (int) ($stats['total'] ?? 0));
    $pctBaru = (int) round((($stats['baru'] ?? 0) / $totalPpdb) * 100);
    $pctDihubungi = (int) round((($stats['dihubungi'] ?? 0) / $totalPpdb) * 100);
    $pctDiterima = (int) round((($stats['diterima'] ?? 0) / $totalPpdb) * 100);
    $pctDitolak = (int) round((($stats['ditolak'] ?? 0) / $totalPpdb) * 100);
  @endphp

  <section class="adm-panel" aria-labelledby="corong-ppdb">
    <div class="adm-panel__head">
      <div class="adm-panel__title">
        <h2 id="corong-ppdb">Distribusi Status Pendaftar PPDB</h2>
        <span class="adm-panel__badge">{{ $stats['total'] ?? 0 }} calon siswa</span>
      </div>
      <div class="adm-actions">
        <a href="{{ route('admin.ppdb') }}" class="btn btn--ghost btn--sm">Kelola Semua Pendaftar</a>
      </div>
    </div>
    <div class="adm-funnel">
      <div class="adm-funnel__step">
        <div class="adm-funnel__head">
          <span class="adm-status adm-status--baru">Baru</span>
          <span class="adm-cell-sub">{{ $pctBaru }}%</span>
        </div>
        <b class="adm-funnel__val">{{ $stats['baru'] ?? 0 }}</b>
        <div class="adm-funnel__bar"><div class="adm-funnel__fill" style="width:{{ $pctBaru }}%"></div></div>
      </div>

      <div class="adm-funnel__step">
        <div class="adm-funnel__head">
          <span class="adm-status adm-status--dihubungi">Dihubungi</span>
          <span class="adm-cell-sub">{{ $pctDihubungi }}%</span>
        </div>
        <b class="adm-funnel__val">{{ $stats['dihubungi'] ?? 0 }}</b>
        <div class="adm-funnel__bar"><div class="adm-funnel__fill" style="width:{{ $pctDihubungi }}%"></div></div>
      </div>

      <div class="adm-funnel__step">
        <div class="adm-funnel__head">
          <span class="adm-status adm-status--diterima">Diterima</span>
          <span class="adm-cell-sub">{{ $pctDiterima }}%</span>
        </div>
        <b class="adm-funnel__val">{{ $stats['diterima'] ?? 0 }}</b>
        <div class="adm-funnel__bar"><div class="adm-funnel__fill" style="width:{{ $pctDiterima }}%"></div></div>
      </div>

      <div class="adm-funnel__step">
        <div class="adm-funnel__head">
          <span class="adm-status adm-status--ditolak">Ditolak</span>
          <span class="adm-cell-sub">{{ $pctDitolak }}%</span>
        </div>
        <b class="adm-funnel__val">{{ $stats['ditolak'] ?? 0 }}</b>
        <div class="adm-funnel__bar"><div class="adm-funnel__fill" style="width:{{ $pctDitolak }}%"></div></div>
      </div>
    </div>
  </section>

  <div class="adm-grid-2">
    <section class="adm-panel" aria-labelledby="pendaftar-terbaru-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="pendaftar-terbaru-heading">Pendaftar Terbaru</h2>
          <span class="adm-panel__badge">5 terakhir</span>
        </div>
        <a href="{{ route('admin.ppdb') }}" class="btn btn--ghost btn--sm">Buka Halaman PPDB</a>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th scope="col">Calon Siswa &amp; SD Asal</th>
              <th scope="col">Wali &amp; WhatsApp</th>
              <th scope="col">Status</th>
              <th scope="col">Tindakan Cepat</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pendaftarTerbaru ?? [] as $item)
              <tr>
                <td>
                  <span class="adm-cell-primary">{{ $item->nama_siswa }}</span>
                  <span class="adm-cell-sub">{{ $item->asal_sd }}</span>
                </td>
                <td>
                  <span class="adm-cell-primary">{{ $item->nama_wali }}</span>
                  <span class="adm-cell-sub">{{ $item->whatsapp }}</span>
                </td>
                <td>
                  <span class="adm-status adm-status--{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                </td>
                <td>
                  <form method="POST" action="{{ route('admin.pendaftar.status', $item) }}" class="adm-inline-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="redirect_to" value="dashboard">
                    <select class="adm-select" name="status" aria-label="Ubah status {{ $item->nama_siswa }}">
                      @foreach(['baru', 'dihubungi', 'diterima', 'ditolak'] as $st)
                        <option value="{{ $st }}" @selected($item->status === $st)>{{ ucfirst($st) }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="btn btn--ghost btn--sm">Simpan</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" style="text-align:center;padding:2rem 1rem;color:var(--ink-3)">
                  Belum ada pendaftar masuk.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <div>
      <section class="adm-panel" aria-labelledby="berita-terbaru-heading">
        <div class="adm-panel__head">
          <div class="adm-panel__title">
            <h2 id="berita-terbaru-heading">Berita &amp; Pengumuman Terbaru</h2>
          </div>
          <a href="{{ route('admin.berita') }}" class="btn btn--ghost btn--sm">Kelola Berita</a>
        </div>

        <ul class="adm-list-feed">
          @forelse($beritaTerbaru ?? [] as $post)
            <li>
              <div>
                <span class="adm-cell-primary">{{ $post->judul }}</span>
                <span class="adm-cell-sub">{{ $post->created_at?->format('d M Y') }} · /berita/{{ $post->slug }}</span>
              </div>
              <div class="adm-actions">
                <span class="adm-status adm-status--{{ $post->status }}">{{ ucfirst($post->status) }}</span>
                <a href="{{ route('admin.berita', ['edit' => $post->id]) }}" class="btn btn--ghost btn--sm">Sunting</a>
              </div>
            </li>
          @empty
            <li style="justify-content:center;color:var(--ink-3)">Belum ada artikel berita.</li>
          @endforelse
        </ul>
      </section>

      <section class="adm-panel" aria-labelledby="ringkasan-identitas-heading">
        <div class="adm-panel__head">
          <div class="adm-panel__title">
            <h2 id="ringkasan-identitas-heading">Status Konfigurasi Situs</h2>
          </div>
          <a href="{{ route('admin.pengaturan') }}" class="btn btn--ghost btn--sm">Ubah Pengaturan</a>
        </div>

        <dl class="adm-meta-grid">
          <div class="adm-meta-card">
            <dt>Nama &amp; NPSN</dt>
            <dd>{{ $settings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }} ({{ $settings['npsn'] ?? '20540020' }})</dd>
          </div>
          <div class="adm-meta-card">
            <dt>Status PPDB</dt>
            <dd>{{ $settings['status_ppdb'] ?? 'Dibuka — Layanan Kantor Sekolah' }}</dd>
          </div>
          <div class="adm-meta-card">
            <dt>Kontak Resmi</dt>
            <dd>{{ $settings['telepon'] ?? '(031) 8538447' }} · {{ $settings['email'] ?? 'smp.pgri4waru@gmail.com' }}</dd>
          </div>
          <div class="adm-meta-card">
            <dt>Aset Kustom Terunggah</dt>
            <dd>
              Logo: <b>{{ !empty($settings['logo_path']) ? 'Kustom' : 'Bawaan' }}</b> ·
              Hero: <b>{{ !empty($settings['hero_image_path']) ? 'Kustom' : 'Bawaan' }}</b> ·
              Brosur: <b>{{ !empty($settings['brosur_ppdb_path']) ? 'Tersedia' : 'Belum' }}</b>
            </dd>
          </div>
        </dl>
      </section>
    </div>
  </div>
@endsection
