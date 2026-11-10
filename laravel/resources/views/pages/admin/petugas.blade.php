@extends('layouts.admin')

@section('title', 'Petugas & Hak Akses — Admin SMP PGRI 4 Waru')
@section('description', 'Kelola akun petugas tata usaha dan administrator sistem SMP PGRI 4 Waru.')
@section('crumb', 'Petugas & Hak Akses')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Petugas &amp; Hak Akses Sistem</h1>
      <p>Kelola akun pengguna yang berwenang memverifikasi pendaftar PPDB, menulis berita, dan memperbarui konfigurasi situs.</p>
    </div>
  </div>

  @if(session('status'))
    <div class="adm-alert" role="status">
      <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  <div class="adm-grid-2">
    <section class="adm-panel" aria-labelledby="daftar-petugas-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="daftar-petugas-heading">Daftar Akun Terdaftar</h2>
          <span class="adm-panel__badge">{{ $users->count() }} akun</span>
        </div>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th scope="col">Nama Petugas</th>
              <th scope="col">Email Masuk</th>
              <th scope="col">Peran</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr>
                <td>
                  <span class="adm-cell-primary">{{ $user->name }}</span>
                  @if(auth()->id() === $user->id)
                    <span class="adm-cell-sub">Sesi Anda saat ini</span>
                  @endif
                </td>
                <td>{{ $user->email }}</td>
                <td>
                  <span class="adm-role-badge">{{ $user->role }}</span>
                </td>
                <td>
                  @if(auth()->user()?->role === 'admin' && auth()->id() !== $user->id)
                    <form method="POST" action="{{ route('admin.petugas.destroy', $user) }}" class="adm-inline-form">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn--quiet btn--sm adm-btn-danger">Hapus Akun</button>
                    </form>
                  @else
                    <span class="adm-cell-sub">Terlindungi</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

    <section class="adm-panel" aria-labelledby="tambah-petugas-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="tambah-petugas-heading">Tambah Petugas Baru</h2>
        </div>
      </div>

      @if(auth()->user()?->role === 'admin')
        <form method="POST" action="{{ route('admin.petugas.store') }}" class="adm-panel__body">
          @csrf
          <div class="field @error('name') has-error @enderror">
            <label for="petugas-nama">Nama lengkap petugas</label>
            <input id="petugas-nama" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Staf Tata Usaha" required>
            @error('name')
              <span class="error">{{ $message }}</span>
            @enderror
          </div>

          <div class="field @error('email') has-error @enderror">
            <label for="petugas-email">Alamat email login</label>
            <input id="petugas-email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@smppgri4waru.sch.id" required>
            @error('email')
              <span class="error">{{ $message }}</span>
            @enderror
          </div>

          <div class="adm-form-grid-2">
            <div class="field @error('role') has-error @enderror">
              <label for="petugas-role">Peran akses</label>
              <select id="petugas-role" name="role">
                <option value="operator" @selected(old('role') === 'operator')>Operator (Verifikasi &amp; Berita)</option>
                <option value="admin" @selected(old('role') === 'admin')>Administrator Penuh</option>
              </select>
            </div>

            <div class="field @error('password') has-error @enderror">
              <label for="petugas-password">Kata sandi awal (min. 8 karakter)</label>
              <input id="petugas-password" name="password" type="password" placeholder="Minimal 8 karakter" required>
              @error('password')
                <span class="error">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn--primary">Tambahkan Petugas</button>
          </div>
        </form>
      @else
        <div class="adm-panel__body">
          <p style="color:var(--ink-3);font-size:0.9rem">Hanya pengguna dengan peran <b>Admin</b> yang dapat menambahkan atau menghapus akun petugas.</p>
        </div>
      @endif
    </section>
  </div>
@endsection
