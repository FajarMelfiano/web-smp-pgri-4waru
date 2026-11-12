@extends('layouts.admin')

@section('title', 'Pendaftar & Jalur PPDB | Admin SMP PGRI 4 Waru')
@section('description', 'Pengelolaan pendaftar Portal PPDB Online, manajemen jalur gelombang, verifikasi berkas, dan pesan konsultasi PPDB.')
@section('crumb', 'Manajemen PPDB')

@php
  $activeTab = request('tab', $editJalur ? 'jalur' : ($onlinePendaftaran->isNotEmpty() ? 'online' : 'pesan'));
@endphp

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Manajemen PPDB Online &amp; Jalur Seleksi</h1>
      <p>Verifikasi akun pendaftar Portal PPDB Online, atur pembukaan jalur gelombang, periksa berkas &amp; pembayaran, atau kelola pesan konsultasi masuk.</p>
    </div>
    <div class="adm-actions">
      <a class="btn btn--ghost btn--sm" href="{{ route('ppdb.daftar') }}" target="_blank" rel="noopener">
        <span>Lihat Portal PPDB</span>
        <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg>
      </a>
      <a class="btn btn--ghost btn--sm" href="{{ route('admin.ekspor.ppdb-online') }}">
        <svg class="i" aria-hidden="true"><use href="#i-download"/></svg>
        <span>CSV Portal Online</span>
      </a>
      <a class="btn btn--primary btn--sm" href="{{ route('admin.ekspor.pendaftar') }}">
        <svg class="i" aria-hidden="true"><use href="#i-download"/></svg>
        <span>CSV Pesan Cepat</span>
      </a>
    </div>
  </div>

  @if(session('status'))
    <div class="adm-alert" role="status">
      <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  @if($errors->any())
    <div class="form-alert form-alert--error" role="alert" style="margin-bottom:1.25rem">
      <strong>Periksa kembali input Anda:</strong>
      <ul>
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <nav class="adm-tabs" aria-label="Modul PPDB">
    <a class="adm-tab {{ $activeTab === 'online' ? 'is-active' : '' }}" href="{{ route('admin.ppdb', ['tab' => 'online']) }}">
      <span>Pendaftar Portal Online</span>
      <span class="adm-tab__count">{{ $stats['online_total'] ?? 0 }}</span>
    </a>
    <a class="adm-tab {{ $activeTab === 'jalur' ? 'is-active' : '' }}" href="{{ route('admin.ppdb', ['tab' => 'jalur']) }}">
      <span>Jalur &amp; Gelombang PPDB</span>
      <span class="adm-tab__count">{{ $jalurList->count() }}</span>
    </a>
    <a class="adm-tab {{ $activeTab === 'pesan' ? 'is-active' : '' }}" href="{{ route('admin.ppdb', ['tab' => 'pesan']) }}">
      <span>Pesan Cepat / Konsultasi</span>
      <span class="adm-tab__count">{{ $stats['total'] ?? 0 }}</span>
    </a>
  </nav>

  @if($activeTab === 'online')
    <section class="adm-panel" aria-labelledby="judul-online-ppdb">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="judul-online-ppdb">Daftar Calon Siswa (Portal PPDB Online)</h2>
          <span class="adm-panel__badge">{{ $onlinePendaftaran->count() }} pendaftar ditampilkan</span>
        </div>
        <div class="adm-actions">
          <a href="{{ route('admin.ekspor.ppdb-online') }}" class="btn btn--ghost btn--sm">Unduh CSV Online</a>
        </div>
      </div>

      <div class="adm-toolbar">
        <form method="GET" action="{{ route('admin.ppdb') }}" class="adm-filter-form">
          <input type="hidden" name="tab" value="online">
          <label for="cari-online" class="sr-only">Cari nomor pendaftaran, NIK, nama siswa, atau SD asal</label>
          <input id="cari-online" class="adm-input" name="q" type="search" value="{{ request('q') }}" placeholder="Cari No. Pendaftaran, NIK, nama siswa, email, atau SD asal...">

          <label for="filter-jalur" class="sr-only">Filter jalur</label>
          <select id="filter-jalur" class="adm-select" name="jalur_id">
            <option value="">Semua Jalur</option>
            @foreach($jalurList as $j)
              <option value="{{ $j->id }}" @selected((int) request('jalur_id') === (int) $j->id)>{{ $j->nama }}</option>
            @endforeach
          </select>

          <label for="filter-seleksi" class="sr-only">Filter status seleksi</label>
          <select id="filter-seleksi" class="adm-select" name="status_seleksi">
            <option value="">Semua Status Seleksi</option>
            <option value="proses" @selected(request('status_seleksi') === 'proses')>Dalam Proses</option>
            <option value="terverifikasi" @selected(request('status_seleksi') === 'terverifikasi')>Terverifikasi</option>
            <option value="diterima" @selected(request('status_seleksi') === 'diterima')>Diterima</option>
            <option value="ditolak" @selected(request('status_seleksi') === 'ditolak')>Ditolak</option>
          </select>

          <button type="submit" class="btn btn--primary btn--sm">Saring Data</button>
          @if(request()->filled('q') || request()->filled('jalur_id') || request()->filled('status_seleksi'))
            <a href="{{ route('admin.ppdb', ['tab' => 'online']) }}" class="btn btn--quiet btn--sm">Atur Ulang</a>
          @endif
        </form>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th scope="col">No. Pendaftaran</th>
              <th scope="col">Calon Siswa &amp; NIK</th>
              <th scope="col">Jalur &amp; Berkas</th>
              <th scope="col">Pembayaran</th>
              <th scope="col">Status Seleksi</th>
              <th scope="col">Tindakan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($onlinePendaftaran as $row)
              <tr>
                <td>
                  <b>{{ $row->no_pendaftaran }}</b>
                  <span class="adm-cell-sub">{{ $row->created_at?->format('d M Y H:i') }}</span>
                </td>
                <td>
                  <span class="adm-cell-primary">{{ $row->account?->nama_lengkap ?? '-' }}</span>
                  <span class="adm-cell-sub">NIK: {{ $row->account?->nik ?? '-' }} · WA: +{{ $row->account?->whatsapp ?? '-' }}</span>
                </td>
                <td>
                  <span class="adm-cell-primary">{{ $row->jalur?->nama ?? 'Belum pilih jalur' }}</span>
                  <span class="adm-cell-sub">{{ $row->asal_sd ?: 'Biodata belum lengkap' }}</span>
                  <span class="adm-cell-sub">{{ $row->berkas->count() }}/4 berkas · {{ $row->canAccessKartuPeserta() ? 'Siap Cetak' : 'Belum Lengkap' }}</span>
                </td>
                <td>
                  @if($row->pembayaran)
                    <span class="ppdb-status-pill ppdb-status-pill--{{ $row->pembayaran->status }}">
                      {{ $row->pembayaran->statusLabel() }}
                    </span>
                  @else
                    <span class="adm-cell-sub">Belum ada</span>
                  @endif
                </td>
                <td>
                  <span class="ppdb-status-pill ppdb-status-pill--{{ $row->status_seleksi }}">
                    {{ $row->statusSeleksiLabel() }}
                  </span>
                </td>
                <td>
                  <div class="adm-actions">
                    <a href="{{ route('admin.ppdb.online.show', $row) }}" class="btn btn--primary btn--sm">Periksa</a>
                    @if(auth()->user()?->role === 'admin')
                      <form method="POST" action="{{ route('admin.ppdb.online.destroy', $row) }}" class="adm-inline-form" onsubmit="return confirm('Hapus akun dan pendaftaran siswa ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--quiet btn--sm adm-btn-danger">Hapus</button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center;padding:2.5rem 1rem;color:var(--ink-3)">
                  Belum ada pendaftar di Portal PPDB Online yang sesuai dengan saringan.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  @elseif($activeTab === 'jalur')
    <div class="adm-grid-2">
      <section class="adm-panel" aria-labelledby="form-jalur-heading">
        <div class="adm-panel__head">
          <div class="adm-panel__title">
            <h2 id="form-jalur-heading">{{ $editJalur ? 'Sunting Jalur PPDB: ' . $editJalur->nama : 'Tambah Jalur / Gelombang PPDB Baru' }}</h2>
          </div>
          @if($editJalur)
            <a href="{{ route('admin.ppdb', ['tab' => 'jalur']) }}" class="btn btn--ghost btn--sm">Batal Sunting</a>
          @endif
        </div>

        <div class="adm-panel__body">
          <form method="POST" action="{{ $editJalur ? route('admin.ppdb.jalur.update', $editJalur) : route('admin.ppdb.jalur.store') }}">
            @csrf
            @if($editJalur)
              @method('PATCH')
            @endif

            <div class="adm-form-grid-2">
              <div class="field">
                <label for="jalur-nama">Nama Jalur / Gelombang <span class="req">*</span></label>
                <input id="jalur-nama" name="nama" type="text" value="{{ old('nama', $editJalur->nama ?? '') }}" placeholder="Contoh: Jalur Prestasi" required>
              </div>
              <div class="field">
                <label for="jalur-kode">Kode Slug <span class="req">*</span></label>
                <input id="jalur-kode" name="kode" type="text" value="{{ old('kode', $editJalur->kode ?? '') }}" placeholder="Contoh: prestasi" required>
              </div>
            </div>

            <div class="adm-form-grid-2">
              <div class="field">
                <label for="jalur-mulai">Tanggal Mulai <span class="req">*</span></label>
                <input id="jalur-mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai', $editJalur?->tanggal_mulai?->toDateString() ?? now()->toDateString()) }}" required>
              </div>
              <div class="field">
                <label for="jalur-selesai">Tanggal Selesai <span class="req">*</span></label>
                <input id="jalur-selesai" name="tanggal_selesai" type="date" value="{{ old('tanggal_selesai', $editJalur?->tanggal_selesai?->toDateString() ?? now()->addMonths(3)->toDateString()) }}" required>
              </div>
            </div>

            <div class="adm-form-grid-2">
              <div class="field">
                <label for="jalur-biaya">Biaya Pendaftaran (Rp, isi 0 jika Gratis) <span class="req">*</span></label>
                <input id="jalur-biaya" name="biaya_daftar" type="number" min="0" value="{{ old('biaya_daftar', $editJalur->biaya_daftar ?? 0) }}" required>
              </div>
              <div class="field">
                <label for="jalur-kuota">Kuota Kursi (Opsional)</label>
                <input id="jalur-kuota" name="kuota" type="number" min="1" value="{{ old('kuota', $editJalur->kuota ?? 64) }}">
              </div>
            </div>

            <div class="adm-form-grid-2">
              <div class="field">
                <label for="jalur-urutan">Urutan Tampil</label>
                <input id="jalur-urutan" name="urutan" type="number" min="0" value="{{ old('urutan', $editJalur->urutan ?? 1) }}">
              </div>
              <div class="field">
                <label for="jalur-dibuka">Status Pembukaan Jalur</label>
                <select id="jalur-dibuka" name="dibuka">
                  <option value="1" @selected((bool) old('dibuka', $editJalur ? $editJalur->dibuka : true))>Dibuka (Bisa Dipilih Siswa)</option>
                  <option value="0" @selected(! (bool) old('dibuka', $editJalur ? $editJalur->dibuka : true))>Ditutup</option>
                </select>
              </div>
            </div>

            <div class="field">
              <label for="jalur-deskripsi">Deskripsi Singkat Jalur</label>
              <textarea id="jalur-deskripsi" name="deskripsi" rows="2" placeholder="Penjelasan singkat mengenai jalur ini">{{ old('deskripsi', $editJalur->deskripsi ?? '') }}</textarea>
            </div>

            <div class="field">
              <label for="jalur-syarat">Ketentuan &amp; Keunggulan Jalur</label>
              <textarea id="jalur-syarat" name="persyaratan" rows="2" placeholder="Syarat khusus atau manfaat jalur pendaftaran">{{ old('persyaratan', $editJalur->persyaratan ?? '') }}</textarea>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn--primary">{{ $editJalur ? 'Simpan Perubahan Jalur' : 'Tambahkan Jalur PPDB' }}</button>
            </div>
          </form>
        </div>
      </section>

      <section class="adm-panel" aria-labelledby="daftar-jalur-heading">
        <div class="adm-panel__head">
          <div class="adm-panel__title">
            <h2 id="daftar-jalur-heading">Daftar Jalur Terdaftar</h2>
            <span class="adm-panel__badge">{{ $jalurList->count() }} jalur</span>
          </div>
        </div>

        <div class="adm-table-wrap">
          <table class="adm-table">
            <thead>
              <tr>
                <th>Jalur &amp; Periode</th>
                <th>Biaya &amp; Kuota</th>
                <th>Status</th>
                <th>Pendaftar</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($jalurList as $j)
                <tr>
                  <td>
                    <span class="adm-cell-primary">{{ $j->nama }}</span>
                    <span class="adm-cell-sub">{{ $j->periodeLabel() }}</span>
                  </td>
                  <td>
                    <span class="adm-cell-primary">{{ $j->biayaLabel() }}</span>
                    <span class="adm-cell-sub">{{ $j->kuota ? $j->kuota . ' kursi' : 'Tanpa batas' }}</span>
                  </td>
                  <td>
                    <span class="ppdb-jalur-pill {{ $j->dibuka ? 'is-open' : 'is-closed' }}">
                      {{ $j->dibuka ? 'Dibuka' : 'Ditutup' }}
                    </span>
                  </td>
                  <td><b>{{ $j->pendaftaran_count ?? 0 }}</b> siswa</td>
                  <td>
                    <div class="adm-actions">
                      <a href="{{ route('admin.ppdb', ['tab' => 'jalur', 'edit_jalur' => $j->id]) }}" class="btn btn--ghost btn--sm">Sunting</a>
                      @if(auth()->user()?->role === 'admin')
                        <form method="POST" action="{{ route('admin.ppdb.jalur.destroy', $j) }}" class="adm-inline-form" onsubmit="return confirm('Hapus jalur ini?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn--quiet btn--sm adm-btn-danger">Hapus</button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" style="text-align:center;padding:2rem">Belum ada jalur PPDB.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </div>
  @else
    <section class="adm-panel" aria-labelledby="judul-tabel-pendaftar">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="judul-tabel-pendaftar">Daftar Pesan Cepat / Konsultasi PPDB</h2>
          <span class="adm-panel__badge">{{ $pendaftar->count() }} baris ditampilkan</span>
        </div>
        <div class="adm-actions">
          <a href="{{ route('admin.ekspor.pendaftar') }}" class="btn btn--ghost btn--sm">Unduh CSV</a>
        </div>
      </div>

      <div class="adm-toolbar">
        <form method="GET" action="{{ route('admin.ppdb') }}" class="adm-filter-form">
          <input type="hidden" name="tab" value="pesan">
          <label for="cari-nama" class="sr-only">Cari nama siswa, wali, atau SD asal</label>
          <input id="cari-nama" class="adm-input" name="q" type="search" value="{{ request('q') }}" placeholder="Cari nama siswa, wali, nomor WA, atau SD asal...">

          <label for="filter-status" class="sr-only">Filter status pendaftaran</label>
          <select id="filter-status" class="adm-select" name="status">
            <option value="">Semua status</option>
            <option value="baru" @selected(request('status') === 'baru')>Status: Baru</option>
            <option value="dihubungi" @selected(request('status') === 'dihubungi')>Status: Dihubungi</option>
            <option value="diterima" @selected(request('status') === 'diterima')>Status: Diterima</option>
            <option value="ditolak" @selected(request('status') === 'ditolak')>Status: Ditolak</option>
          </select>

          <button type="submit" class="btn btn--primary btn--sm">Saring Data</button>
          @if(request()->filled('q') || request()->filled('status'))
            <a href="{{ route('admin.ppdb', ['tab' => 'pesan']) }}" class="btn btn--quiet btn--sm">Atur Ulang</a>
          @endif
        </form>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Calon Siswa &amp; Asal SD</th>
              <th scope="col">Orang Tua / Wali</th>
              <th scope="col">WhatsApp</th>
              <th scope="col">Catatan Tambahan</th>
              <th scope="col">Status</th>
              <th scope="col">Tindakan Petugas</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pendaftar as $item)
              <tr>
                <td><b>#{{ str_pad((string) $item->id, 3, '0', STR_PAD_LEFT) }}</b></td>
                <td>
                  <span class="adm-cell-primary">{{ $item->nama_siswa }}</span>
                  <span class="adm-cell-sub">{{ $item->asal_sd }}</span>
                </td>
                <td>
                  <span class="adm-cell-primary">{{ $item->nama_wali }}</span>
                  <span class="adm-cell-sub">{{ $item->created_at?->format('d M Y H:i') }}</span>
                </td>
                <td>
                  <a class="adm-wa-link" href="https://wa.me/{{ preg_replace('/^0/', '62', $item->whatsapp) }}" target="_blank" rel="noopener">
                    <svg class="i" aria-hidden="true"><use href="#i-phone"/></svg>
                    <span>{{ $item->whatsapp }}</span>
                  </a>
                </td>
                <td>{{ $item->catatan ?: '-' }}</td>
                <td>
                  <span class="adm-status adm-status--{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                </td>
                <td>
                  <div class="adm-actions">
                    <form method="POST" action="{{ route('admin.pendaftar.status', $item) }}" class="adm-inline-form">
                      @csrf
                      @method('PATCH')
                      <select class="adm-select" name="status" aria-label="Ubah status {{ $item->nama_siswa }}">
                        @foreach(['baru', 'dihubungi', 'diterima', 'ditolak'] as $st)
                          <option value="{{ $st }}" @selected($item->status === $st)>{{ ucfirst($st) }}</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn--ghost btn--sm">Simpan</button>
                    </form>

                    @if(auth()->user()?->role === 'admin')
                      <form method="POST" action="{{ route('admin.pendaftar.destroy', $item) }}" class="adm-inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--quiet btn--sm adm-btn-danger">Hapus</button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center;padding:2.5rem 1rem;color:var(--ink-3)">
                  Belum ada data calon siswa yang sesuai dengan saringan.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  @endif
@endsection
