@extends('layouts.admin')

@section('title', 'Pendaftar PPDB — Admin SMP PGRI 4 Waru')
@section('description', 'Pengelolaan data calon siswa baru, verifikasi berkas, dan ekspor CSV PPDB SMP PGRI 4 Waru.')
@section('crumb', 'Pendaftar PPDB')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Data Pendaftar PPDB</h1>
      <p>Verifikasi calon siswa masuk, perbarui status tindak lanjut, hubungi orang tua/wali melalui WhatsApp, atau unduh laporan CSV.</p>
    </div>
    <div class="adm-actions">
      <a class="btn btn--ghost btn--sm" href="{{ route('ppdb') }}">
        <span>Lihat Formulir Publik</span>
        <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg>
      </a>
      <a class="btn btn--primary btn--sm" href="{{ route('admin.ekspor.pendaftar') }}">
        <svg class="i" aria-hidden="true"><use href="#i-download"/></svg>
        <span>Ekspor CSV Lengkap</span>
      </a>
    </div>
  </div>

  @if(session('status'))
    <div class="adm-alert" role="status">
      <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  <nav class="adm-tabs" aria-label="Saringan cepat status PPDB">
    <a class="adm-tab {{ !request()->filled('status') ? 'is-active' : '' }}" href="{{ route('admin.ppdb', request()->only('q')) }}">
      <span>Semua Pendaftar</span>
      <span class="adm-tab__count">{{ $stats['total'] ?? 0 }}</span>
    </a>
    <a class="adm-tab {{ request('status') === 'baru' ? 'is-active' : '' }}" href="{{ route('admin.ppdb', array_filter(['status' => 'baru', 'q' => request('q')])) }}">
      <span>Baru</span>
      <span class="adm-tab__count">{{ $stats['baru'] ?? 0 }}</span>
    </a>
    <a class="adm-tab {{ request('status') === 'dihubungi' ? 'is-active' : '' }}" href="{{ route('admin.ppdb', array_filter(['status' => 'dihubungi', 'q' => request('q')])) }}">
      <span>Dihubungi</span>
      <span class="adm-tab__count">{{ $stats['dihubungi'] ?? 0 }}</span>
    </a>
    <a class="adm-tab {{ request('status') === 'diterima' ? 'is-active' : '' }}" href="{{ route('admin.ppdb', array_filter(['status' => 'diterima', 'q' => request('q')])) }}">
      <span>Diterima</span>
      <span class="adm-tab__count">{{ $stats['diterima'] ?? 0 }}</span>
    </a>
    <a class="adm-tab {{ request('status') === 'ditolak' ? 'is-active' : '' }}" href="{{ route('admin.ppdb', array_filter(['status' => 'ditolak', 'q' => request('q')])) }}">
      <span>Ditolak</span>
      <span class="adm-tab__count">{{ $stats['ditolak'] ?? 0 }}</span>
    </a>
  </nav>

  <section class="adm-panel" aria-labelledby="judul-tabel-pendaftar">
    <div class="adm-panel__head">
      <div class="adm-panel__title">
        <h2 id="judul-tabel-pendaftar">Daftar Calon Siswa</h2>
        <span class="adm-panel__badge">{{ $pendaftar->count() }} baris ditampilkan</span>
      </div>
      <div class="adm-actions">
        <a href="{{ route('admin.ekspor.pendaftar') }}" class="btn btn--ghost btn--sm">Unduh CSV</a>
      </div>
    </div>

    <div class="adm-toolbar">
      <form method="GET" action="{{ route('admin.ppdb') }}" class="adm-filter-form">
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
          <a href="{{ route('admin.ppdb') }}" class="btn btn--quiet btn--sm">Atur Ulang</a>
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
              <td>{{ $item->catatan ?: '—' }}</td>
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
@endsection
