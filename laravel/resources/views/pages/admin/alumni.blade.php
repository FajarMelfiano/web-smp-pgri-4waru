@extends('layouts.admin')

@section('title', 'Moderasi Ulasan Alumni — Admin SMP PGRI 4 Waru')
@section('description', 'Kelola dan verifikasi testimoni serta ulasan para alumni SMP PGRI 4 Waru.')
@section('crumb', 'Ulasan Alumni')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Manajemen &amp; moderasi ulasan alumni</h1>
      <p>Tinjau testimoni yang dikirim oleh para alumni melalui halaman publik atau tambahkan ulasan alumni terverifikasi secara langsung.</p>
    </div>
    <div class="adm-actions">
      <a class="btn btn--ghost btn--sm" href="{{ route('alumni.index') }}">
        <span>Buka Halaman Alumni</span>
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

  <nav class="adm-tabs" aria-label="Saringan status ulasan alumni">
    <a class="adm-tab {{ !request()->filled('status') ? 'is-active' : '' }}" href="{{ route('admin.alumni') }}">
      <span>Semua Ulasan</span>
      <span class="adm-tab__count">{{ $stats['alumni_total'] ?? 0 }}</span>
    </a>
    <a class="adm-tab {{ request('status') === 'terbit' ? 'is-active' : '' }}" href="{{ route('admin.alumni', ['status' => 'terbit']) }}">
      <span>Tayang (Terbit)</span>
      <span class="adm-tab__count">{{ $stats['alumni_terbit'] ?? 0 }}</span>
    </a>
    <a class="adm-tab {{ request('status') === 'menunggu' ? 'is-active' : '' }}" href="{{ route('admin.alumni', ['status' => 'menunggu']) }}">
      <span>Menunggu Moderasi</span>
      <span class="adm-tab__count">{{ $stats['alumni_menunggu'] ?? 0 }}</span>
    </a>
  </nav>

  <div class="adm-grid-2">
    <section class="adm-panel" aria-labelledby="daftar-alumni-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="daftar-alumni-heading">Daftar Ulasan Alumni</h2>
          <span class="adm-panel__badge">{{ $ulasan->count() }} data</span>
        </div>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th scope="col">Alumni &amp; Angkatan</th>
              <th scope="col">Isi Ulasan</th>
              <th scope="col">Status</th>
              <th scope="col">Tindakan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($ulasan as $item)
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:0.75rem">
                    @if($item->foto_path)
                      <img src="{{ str_starts_with($item->foto_path, 'http') || str_starts_with($item->foto_path, '/') ? $item->foto_path : asset('storage/'.$item->foto_path) }}" alt="{{ $item->nama }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:1px solid var(--line);flex-shrink:0">
                    @else
                      <span class="alumni-card__avatar alumni-card__avatar--initial" style="width:40px;height:40px;font-size:0.9rem" aria-hidden="true">{{ mb_strtoupper(mb_substr($item->nama, 0, 1)) }}</span>
                    @endif
                    <div>
                      <span class="adm-cell-primary">{{ $item->nama }}</span>
                      <span class="adm-cell-sub">Angkatan {{ $item->angkatan }}@if($item->pekerjaan) · {{ $item->pekerjaan }}@endif</span>
                    </div>
                  </div>
                </td>
                <td>
                  <span style="font-size:0.86rem;color:var(--ink-2);display:block;max-width:340px">“{{ $item->ulasan }}”</span>
                </td>
                <td>
                  <span class="adm-status adm-status--{{ $item->status }}">
                    {{ $item->status === 'terbit' ? 'Tayang' : 'Menunggu' }}
                  </span>
                </td>
                <td>
                  <div class="adm-actions">
                    <form method="POST" action="{{ route('admin.alumni.status', $item) }}" class="adm-inline-form">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn--ghost btn--sm">
                        {{ $item->status === 'terbit' ? 'Sembunyikan' : 'Terbitkan' }}
                      </button>
                    </form>
                    @if(auth()->user()?->role === 'admin')
                      <form method="POST" action="{{ route('admin.alumni.destroy', $item) }}" class="adm-inline-form">
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
                <td colspan="4" style="text-align:center;padding:2rem 1rem;color:var(--ink-3)">
                  Belum ada ulasan alumni.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <section class="adm-panel" aria-labelledby="tambah-alumni-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="tambah-alumni-heading">Tambah Ulasan Alumni</h2>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.alumni.store') }}" enctype="multipart/form-data" class="adm-panel__body">
        @csrf

        <div class="field">
          <label for="nama">Nama Lengkap Alumni *</label>
          <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required maxlength="120" placeholder="Contoh: Bagus Pratama">
        </div>

        <div class="field">
          <label for="angkatan">Tahun Lulus / Angkatan *</label>
          <input type="text" id="angkatan" name="angkatan" value="{{ old('angkatan') }}" required maxlength="20" placeholder="Contoh: 2018">
        </div>

        <div class="field">
          <label for="pekerjaan">Sekolah Lanjutan / Aktivitas</label>
          <input type="text" id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" maxlength="150" placeholder="Contoh: SMAN 1 Waru / Mahasiswa">
        </div>

        <div class="field">
          <label for="ulasan">Isi Ulasan &amp; Kesan *</label>
          <textarea id="ulasan" name="ulasan" rows="4" required maxlength="1200" placeholder="Tuliskan testimoni alumni selama menempuh pendidikan di SMP PGRI 4 Waru...">{{ old('ulasan') }}</textarea>
        </div>

        <div class="field">
          <label for="foto">Foto Profil Alumni (Opsional)</label>
          <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp">
        </div>

        <div class="field">
          <label for="status">Status Penayangan *</label>
          <select id="status" name="status" required>
            <option value="terbit">Langsung Tayangkan (Terbit)</option>
            <option value="menunggu">Simpan Sebagai Menunggu Moderasi</option>
          </select>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn--primary btn--sm">Simpan Ulasan Alumni</button>
        </div>
      </form>
    </section>
  </div>
@endsection
