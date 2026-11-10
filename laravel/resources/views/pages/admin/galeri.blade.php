@extends('layouts.admin')

@section('title', 'Galeri Foto Sekolah — Admin SMP PGRI 4 Waru')
@section('description', 'Kelola unggahan dokumentasi foto kegiatan belajar dan fasilitas sekolah untuk situs publik.')
@section('crumb', 'Galeri Foto Sekolah')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Galeri foto kegiatan sekolah</h1>
      <p>Tambahkan foto dokumentasi kegiatan belajar, upacara, dan fasilitas yang akan ditampilkan pada Beranda serta halaman Berita &amp; Galeri publik.</p>
    </div>
    <div class="adm-actions">
      <a class="btn btn--ghost btn--sm" href="{{ route('berita.index') }}#galeri">
        <span>Lihat Galeri Publik</span>
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

  @if($errors->any())
    <div class="adm-alert" role="alert" style="border-color:var(--red)">
      <div>
        <b>Periksa kembali unggahan foto:</b>
        <ul style="margin:0.35rem 0 0;padding-left:1.25rem">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <div class="adm-grid-2">
    <section class="adm-panel" aria-labelledby="daftar-foto-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="daftar-foto-heading">Koleksi Foto Terpasang</h2>
          <span class="adm-panel__badge">{{ $galeri->count() }} foto</span>
        </div>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th scope="col">Pratinjau &amp; Judul</th>
              <th scope="col">Kategori &amp; Posisi</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($galeri as $item)
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:0.85rem">
                    <img src="{{ str_starts_with($item->image_path, 'http') || str_starts_with($item->image_path, '/') ? $item->image_path : asset('storage/'.$item->image_path) }}" alt="{{ $item->judul }}" style="width:68px;height:48px;object-fit:cover;border-radius:8px;border:1px solid var(--line);flex-shrink:0">
                    <div>
                      <span class="adm-cell-primary">{{ $item->judul }}</span>
                      @if($item->keterangan)
                        <span class="adm-cell-sub">{{ \Illuminate\Support\Str::limit($item->keterangan, 60) }}</span>
                      @endif
                    </div>
                  </div>
                </td>
                <td>
                  <div style="display:flex;flex-direction:column;align-items:flex-start;gap:0.35rem">
                    <span class="adm-status adm-status--baru">{{ $item->kategori }}</span>
                    @if($item->tampil_beranda)
                      <span class="adm-status adm-status--terbit">Beranda &amp; Berita</span>
                    @else
                      <span class="adm-status adm-status--draf">Halaman Berita</span>
                    @endif
                  </div>
                </td>
                <td>
                  @if(auth()->user()?->role === 'admin')
                    <form method="POST" action="{{ route('admin.galeri.destroy', $item) }}" class="adm-inline-form">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn--quiet btn--sm adm-btn-danger">Hapus</button>
                    </form>
                  @else
                    <span class="adm-cell-sub">Tersimpan</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" style="text-align:center;padding:2rem 1rem;color:var(--ink-3)">
                  Belum ada foto dokumentasi di galeri. Gunakan formulir di samping untuk mengunggah foto pertama.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <section class="adm-panel" aria-labelledby="unggah-foto-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="unggah-foto-heading">Tambah Foto Dokumentasi</h2>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.galeri.store') }}" enctype="multipart/form-data" class="adm-panel__body">
        @csrf

        <div class="field">
          <label for="judul">Judul Foto / Kegiatan *</label>
          <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required maxlength="150" placeholder="Contoh: Kegiatan Upacara Bendera Hari Senin">
        </div>

        <div class="field">
          <label for="kategori">Kategori Dokumentasi *</label>
          <select id="kategori" name="kategori" required>
            @foreach(['Kegiatan Sekolah', 'Pembelajaran Kelas', 'Fasilitas & Lingkungan', 'Ekstrakurikuler', 'Prestasi & Acara'] as $kat)
              <option value="{{ $kat }}" @selected(old('kategori') === $kat)>{{ $kat }}</option>
            @endforeach
          </select>
        </div>

        <div class="field">
          <label for="keterangan">Keterangan Singkat</label>
          <textarea id="keterangan" name="keterangan" rows="3" maxlength="350" placeholder="Penjelasan singkat mengenai momen atau kegiatan di dalam foto...">{{ old('keterangan') }}</textarea>
        </div>

        <div class="field">
          <label for="foto">Berkas Foto (JPG, PNG, WEBP maks 4MB) *</label>
          <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp" required>
        </div>

        <div class="field">
          <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;text-transform:none;font-size:0.86rem;letter-spacing:0">
            <input type="checkbox" name="tampil_beranda" value="1" checked style="width:auto">
            <span>Tampilkan juga di Beranda utama</span>
          </label>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn--primary btn--sm">Unggah ke Galeri Publik</button>
        </div>
      </form>
    </section>
  </div>
@endsection
