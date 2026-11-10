@extends('layouts.admin')

@section('title', 'Pengaturan & Kustomisasi Situs — Admin SMP PGRI 4 Waru')
@section('description', 'Kustomisasi penuh identitas sekolah, unggah logo, gambar hero beranda, brosur PPDB, serta teks halaman publik.')
@section('crumb', 'Pengaturan & Kustomisasi Situs')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Pengaturan &amp; Kustomisasi Penuh Situs</h1>
      <p>Unggah dan ganti logo sekolah, gambar utama beranda, dokumen brosur PPDB, serta perbarui seluruh teks identitas dan kontak situs publik secara langsung.</p>
    </div>
    <div class="adm-actions">
      <a class="btn btn--ghost btn--sm" href="{{ route('beranda') }}">
        <span>Pratinjau Beranda Publik</span>
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

  <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
    @csrf

    <section class="adm-panel" aria-labelledby="unggah-media-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="unggah-media-heading">1. Unggah Media, Logo &amp; Dokumen Situs</h2>
          <span class="adm-panel__badge">Unggah Berkas</span>
        </div>
      </div>

      <div class="adm-panel__body">
        <div class="adm-upload-grid">
          <div class="adm-upload-card">
            <div>
              <span class="adm-cell-primary">Logo Sekolah (Header &amp; Admin)</span>
              <span class="adm-cell-sub">Format JPG, PNG, WebP, atau SVG (maks. 2 MB). Secara bawaan menggunakan lambang resmi SMP PGRI 4 Waru.</span>
            </div>

            <div class="adm-upload-preview">
              @if(!empty($settings['logo_path']))
                <img src="{{ asset('storage/'.$settings['logo_path']) }}" alt="Pratinjau logo sekolah kustom">
              @else
                <img src="/images/logo-smp-pgri4-waru.png" alt="Logo resmi SMP PGRI 4 Waru">
              @endif
            </div>

            <div class="field" style="margin-bottom:0">
              <label for="upload-logo">Pilih berkas logo baru</label>
              <input id="upload-logo" name="logo_sekolah" type="file" accept=".jpg,.jpeg,.png,.webp,.svg">
              @if(!empty($settings['logo_path']))
                <label style="display:inline-flex;align-items:center;gap:0.45rem;margin-top:0.55rem;font-weight:600;cursor:pointer">
                  <input type="checkbox" name="hapus_logo" value="1" style="width:auto">
                  <span>Kembalikan ke logo bawaan</span>
                </label>
              @endif
            </div>
          </div>

          <div class="adm-upload-card">
            <div>
              <span class="adm-cell-primary">Gambar Utama Beranda (Hero Banner)</span>
              <span class="adm-cell-sub">Format JPG, PNG, atau WebP (maks. 4 MB). Ditampilkan pada bagian pembuka halaman Beranda.</span>
            </div>

            <div class="adm-upload-preview">
              @if(!empty($settings['hero_image_path']))
                <img src="{{ asset('storage/'.$settings['hero_image_path']) }}" alt="Pratinjau gambar hero beranda">
              @else
                <span class="adm-cell-sub">Menggunakan ilustrasi kartu identitas bawaan</span>
              @endif
            </div>

            <div class="field" style="margin-bottom:0">
              <label for="upload-hero">Pilih gambar beranda baru</label>
              <input id="upload-hero" name="hero_image" type="file" accept=".jpg,.jpeg,.png,.webp">
              @if(!empty($settings['hero_image_path']))
                <label style="display:inline-flex;align-items:center;gap:0.45rem;margin-top:0.55rem;font-weight:600;cursor:pointer">
                  <input type="checkbox" name="hapus_hero" value="1" style="width:auto">
                  <span>Hapus gambar beranda kustom</span>
                </label>
              @endif
            </div>
          </div>

          <div class="adm-upload-card">
            <div>
              <span class="adm-cell-primary">Brosur / Panduan PPDB</span>
              <span class="adm-cell-sub">Format PDF, JPG, PNG, atau WebP (maks. 5 MB). Dapat diunduh calon wali murid di halaman PPDB.</span>
            </div>

            <div class="adm-upload-preview">
              @if(!empty($settings['brosur_ppdb_path']))
                <a class="btn btn--ghost btn--sm" href="{{ asset('storage/'.$settings['brosur_ppdb_path']) }}" target="_blank" rel="noopener">
                  Lihat Berkas Brosur Aktif
                </a>
              @else
                <span class="adm-cell-sub">Belum ada brosur yang diunggah</span>
              @endif
            </div>

            <div class="field" style="margin-bottom:0">
              <label for="upload-brosur">Pilih berkas brosur PPDB</label>
              <input id="upload-brosur" name="brosur_ppdb" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp">
              @if(!empty($settings['brosur_ppdb_path']))
                <label style="display:inline-flex;align-items:center;gap:0.45rem;margin-top:0.55rem;font-weight:600;cursor:pointer">
                  <input type="checkbox" name="hapus_brosur" value="1" style="width:auto">
                  <span>Hapus berkas brosur</span>
                </label>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="adm-grid-2">
      <section class="adm-panel" aria-labelledby="identitas-sekolah-heading">
        <div class="adm-panel__head">
          <div class="adm-panel__title">
            <h2 id="identitas-sekolah-heading">2. Identitas &amp; Data Kelembagaan</h2>
          </div>
        </div>

        <div class="adm-panel__body">
          <div class="adm-form-grid-2">
            <div class="field">
              <label for="set-nama">Nama resmi sekolah</label>
              <input id="set-nama" name="nama_sekolah" type="text" value="{{ old('nama_sekolah', $settings['nama_sekolah'] ?? 'SMP PGRI 4 Waru') }}" required>
            </div>
            <div class="field">
              <label for="set-tagline">Subjudul lokasi (Tagline Header)</label>
              <input id="set-tagline" name="tagline" type="text" value="{{ old('tagline', $settings['tagline'] ?? 'Kureksari, Sidoarjo') }}">
            </div>
          </div>

          <div class="adm-form-grid-2">
            <div class="field">
              <label for="set-npsn">Nomor Pokok Sekolah Nasional (NPSN)</label>
              <input id="set-npsn" name="npsn" type="text" value="{{ old('npsn', $settings['npsn'] ?? '20540020') }}">
            </div>
            <div class="field">
              <label for="set-tahun">Tahun berdiri</label>
              <input id="set-tahun" name="tahun_berdiri" type="text" value="{{ old('tahun_berdiri', $settings['tahun_berdiri'] ?? '1979') }}">
            </div>
          </div>

          <div class="field">
            <label for="set-naungan">Yayasan / Lembaga penyelenggara</label>
            <input id="set-naungan" name="naungan" type="text" value="{{ old('naungan', $settings['naungan'] ?? 'YPLP DASMEN PGRI Jawa Timur') }}">
          </div>

          <div class="adm-form-grid-2">
            <div class="field">
              <label for="set-sk">Nomor SK Pendirian</label>
              <input id="set-sk" name="sk_pendirian" type="text" value="{{ old('sk_pendirian', $settings['sk_pendirian'] ?? '421.3/2829/404.3.1/1979') }}">
            </div>
            <div class="field">
              <label for="set-luas">Luas tanah sekolah</label>
              <input id="set-luas" name="luas_tanah" type="text" value="{{ old('luas_tanah', $settings['luas_tanah'] ?? '660 m²') }}">
            </div>
          </div>

          <div class="field">
            <label for="set-waktu">Waktu kegiatan belajar</label>
            <input id="set-waktu" name="waktu_belajar" type="text" value="{{ old('waktu_belajar', $settings['waktu_belajar'] ?? 'Pagi hari, enam hari dalam seminggu') }}">
          </div>
        </div>
      </section>

      <section class="adm-panel" aria-labelledby="kontak-lokasi-heading">
        <div class="adm-panel__head">
          <div class="adm-panel__title">
            <h2 id="kontak-lokasi-heading">3. Kontak, Alamat &amp; Jam Layanan</h2>
          </div>
        </div>

        <div class="adm-panel__body">
          <div class="adm-form-grid-2">
            <div class="field">
              <label for="set-telepon">Nomor telepon kantor</label>
              <input id="set-telepon" name="telepon" type="text" value="{{ old('telepon', $settings['telepon'] ?? '(031) 8538447') }}" required>
            </div>
            <div class="field">
              <label for="set-email">Alamat email resmi</label>
              <input id="set-email" name="email" type="email" value="{{ old('email', $settings['email'] ?? 'smp.pgri4waru@gmail.com') }}" required>
            </div>
          </div>

          <div class="adm-form-grid-2">
            <div class="field">
              <label for="set-ig">Akun Instagram</label>
              <input id="set-ig" name="instagram" type="text" value="{{ old('instagram', $settings['instagram'] ?? '@smppgri4waru') }}">
            </div>
            <div class="field">
              <label for="set-pos">Kode pos</label>
              <input id="set-pos" name="kode_pos" type="text" value="{{ old('kode_pos', $settings['kode_pos'] ?? '61256') }}">
            </div>
          </div>

          <div class="field">
            <label for="set-alamat">Alamat lengkap jalan &amp; desa</label>
            <input id="set-alamat" name="alamat" type="text" value="{{ old('alamat', $settings['alamat'] ?? 'Jl. Anggrek No. 109, Kureksari, Kec. Waru, Kab. Sidoarjo') }}">
          </div>

          <div class="adm-form-grid-2">
            <div class="field">
              <label for="set-koordinat">Koordinat peta (Lintang, Bujur)</label>
              <input id="set-koordinat" name="koordinat" type="text" value="{{ old('koordinat', $settings['koordinat'] ?? '-7.3619, 112.7327') }}">
            </div>
            <div class="field">
              <label for="set-jam">Jam layanan tata usaha</label>
              <input id="set-jam" name="jam_layanan" type="text" value="{{ old('jam_layanan', $settings['jam_layanan'] ?? 'Pagi hari, enam hari dalam seminggu') }}" required>
            </div>
          </div>
        </div>
      </section>
    </div>

    <section class="adm-panel" aria-labelledby="konten-publik-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="konten-publik-heading">4. Kustomisasi Teks Beranda, Visi &amp; Informasi PPDB</h2>
        </div>
      </div>

      <div class="adm-panel__body">
        <div class="adm-form-grid-2">
          <div class="field">
            <label for="set-eyebrow">Teks sapaan atas (Hero Eyebrow)</label>
            <input id="set-eyebrow" name="hero_eyebrow" type="text" value="{{ old('hero_eyebrow', $settings['hero_eyebrow'] ?? 'Selamat datang di SMP PGRI 4 Waru') }}">
          </div>
          <div class="field">
            <label for="set-judul-hero">Judul utama halaman Beranda (Hero Title)</label>
            <input id="set-judul-hero" name="hero_judul" type="text" value="{{ old('hero_judul', $settings['hero_judul'] ?? 'Sekolah menengah pertama untuk keluarga Kureksari sejak 1979.') }}">
          </div>
        </div>

        <div class="field">
          <label for="set-deskripsi-hero">Paragraf pengantar Beranda (Hero Lead)</label>
          <textarea id="set-deskripsi-hero" name="hero_deskripsi" rows="2">{{ old('hero_deskripsi', $settings['hero_deskripsi'] ?? '') }}</textarea>
        </div>

        <div class="field">
          <label for="set-visi">Rumusan Visi Sekolah (ditampilkan di Beranda &amp; Profil)</label>
          <textarea id="set-visi" name="visi_sekolah" rows="2">{{ old('visi_sekolah', $settings['visi_sekolah'] ?? '') }}</textarea>
        </div>

        <div class="adm-form-grid-2">
          <div class="field">
            <label for="set-tahun-ajaran">Tahun ajaran PPDB aktif</label>
            <input id="set-tahun-ajaran" name="tahun_ajaran" type="text" value="{{ old('tahun_ajaran', $settings['tahun_ajaran'] ?? '2026/2027') }}">
          </div>
          <div class="field">
            <label for="set-status-ppdb">Label status pendaftaran PPDB</label>
            <input id="set-status-ppdb" name="status_ppdb" type="text" value="{{ old('status_ppdb', $settings['status_ppdb'] ?? 'Dibuka — Layanan Kantor Sekolah') }}">
          </div>
        </div>

        <div class="field">
          <label for="set-ppdb">Catatan / Pengumuman khusus PPDB (ditampilkan pada halaman PPDB)</label>
          <textarea id="set-ppdb" name="pengumuman_ppdb" rows="3">{{ old('pengumuman_ppdb', $settings['pengumuman_ppdb'] ?? '') }}</textarea>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn--primary">Simpan Seluruh Pengaturan &amp; Kustomisasi</button>
        </div>
      </div>
    </section>
  </form>
@endsection
