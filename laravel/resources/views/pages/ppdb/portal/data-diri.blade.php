@extends('layouts.ppdb-portal')

@section('title', 'Pengisian Data Diri | Portal PPDB ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))

@section('content')
<div class="ppdb-portal-stack">
  <section class="adm-panel" aria-labelledby="ppdb-biodata-heading">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Tahap 3 dari 6</span>
        <h1 class="adm-panel__title" id="ppdb-biodata-heading">Pengisian Data Diri &amp; Orang Tua</h1>
        <p class="adm-panel__sub">Pastikan seluruh data diisi sesuai dengan Kartu Keluarga (KK), Akta Kelahiran, dan Rapor/SKL SD/MI.</p>
      </div>
      @if($pendaftaran->hasCompletedBiodata())
        <span class="ppdb-status-pill ppdb-status-pill--lunas">Tersimpan ({{ $pendaftaran->data_diri_lengkap_at->format('d/m/Y H:i') }})</span>
      @endif
    </div>

    @if($errors->any())
      <div class="form-alert form-alert--error" role="alert" style="margin-bottom:1.25rem">
        <strong>Periksa kembali pengisian data diri:</strong>
        <ul>
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('ppdb.portal.data-diri.update') }}" class="ppdb-portal-form">
      @csrf

      <section class="ppdb-fieldset" aria-labelledby="bio-sec-a">
        <h2 class="ppdb-fieldset__title" id="bio-sec-a">A. Identitas Calon Siswa</h2>
        <div class="ppdb-form-grid">
          <div class="field">
            <label>Nama Lengkap (Sesuai Akun)</label>
            <input type="text" value="{{ $account->nama_lengkap }}" disabled>
          </div>
          <div class="field">
            <label>NIK (16 Digit)</label>
            <input type="text" value="{{ $account->nik }}" disabled>
          </div>
          <div class="field">
            <label>Tanggal Lahir</label>
            <input type="text" value="{{ $account->tanggal_lahir?->format('d/m/Y') ?? '-' }}" disabled>
          </div>
          <div class="field">
            <label for="bio-nisn">NISN (Nomor Induk Siswa Nasional)</label>
            <input type="text" id="bio-nisn" name="nisn" value="{{ old('nisn', $pendaftaran->nisn) }}" placeholder="10 digit angka NISN (opsional bila belum terbit)" inputmode="numeric" maxlength="12">
          </div>
          <div class="field">
            <label for="bio-jk">Jenis Kelamin <span class="req">*</span></label>
            <select id="bio-jk" name="jenis_kelamin" required>
              <option value="">-- Pilih Jenis Kelamin --</option>
              <option value="Laki-laki" @selected(old('jenis_kelamin', $pendaftaran->jenis_kelamin) === 'Laki-laki')>Laki-laki</option>
              <option value="Perempuan" @selected(old('jenis_kelamin', $pendaftaran->jenis_kelamin) === 'Perempuan')>Perempuan</option>
            </select>
          </div>
          <div class="field">
            <label for="bio-tempat-lahir">Tempat Lahir <span class="req">*</span></label>
            <input type="text" id="bio-tempat-lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $pendaftaran->tempat_lahir) }}" placeholder="Contoh: Sidoarjo" required>
          </div>
          <div class="field">
            <label for="bio-agama">Agama <span class="req">*</span></label>
            <select id="bio-agama" name="agama" required>
              <option value="">-- Pilih Agama --</option>
              @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agamaOpt)
                <option value="{{ $agamaOpt }}" @selected(old('agama', $pendaftaran->agama) === $agamaOpt)>{{ $agamaOpt }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="bio-anak-ke">Anak Ke-</label>
            <input type="number" id="bio-anak-ke" name="anak_ke" value="{{ old('anak_ke', $pendaftaran->anak_ke) }}" min="1" max="20" placeholder="Contoh: 1">
          </div>
          <div class="field">
            <label for="bio-jml-saudara">Jumlah Saudara Kandung</label>
            <input type="number" id="bio-jml-saudara" name="jumlah_saudara" value="{{ old('jumlah_saudara', $pendaftaran->jumlah_saudara) }}" min="0" max="20" placeholder="Contoh: 2">
          </div>
        </div>
      </section>

      <section class="ppdb-fieldset" aria-labelledby="bio-sec-b">
        <h2 class="ppdb-fieldset__title" id="bio-sec-b">B. Alamat Domisili &amp; Asal Sekolah Dasar (SD/MI)</h2>
        <div class="ppdb-form-grid">
          <div class="field ppdb-field-span-full">
            <label for="bio-alamat">Alamat Lengkap Jalan / RT / RW <span class="req">*</span></label>
            <textarea id="bio-alamat" name="alamat_lengkap" rows="2" placeholder="Contoh: Jl. Anggrek II No. 14 RT 03 RW 02" required>{{ old('alamat_lengkap', $pendaftaran->alamat_lengkap) }}</textarea>
          </div>
          <div class="field">
            <label for="bio-desa">Kelurahan / Desa <span class="req">*</span></label>
            <input type="text" id="bio-desa" name="kelurahan_desa" value="{{ old('kelurahan_desa', $pendaftaran->kelurahan_desa) }}" placeholder="Contoh: Kureksari" required>
          </div>
          <div class="field">
            <label for="bio-kec">Kecamatan <span class="req">*</span></label>
            <input type="text" id="bio-kec" name="kecamatan" value="{{ old('kecamatan', $pendaftaran->kecamatan) }}" placeholder="Contoh: Waru" required>
          </div>
          <div class="field">
            <label for="bio-kota">Kabupaten / Kota <span class="req">*</span></label>
            <input type="text" id="bio-kota" name="kabupaten_kota" value="{{ old('kabupaten_kota', $pendaftaran->kabupaten_kota ?: 'Sidoarjo') }}" placeholder="Contoh: Sidoarjo" required>
          </div>
          <div class="field">
            <label for="bio-sd">Asal SD / MI <span class="req">*</span></label>
            <input type="text" id="bio-sd" name="asal_sd" value="{{ old('asal_sd', $pendaftaran->asal_sd) }}" placeholder="Contoh: SDN Kureksari 1 Waru" required>
          </div>
          <div class="field">
            <label for="bio-npsn-sd">NPSN SD / MI (Opsional)</label>
            <input type="text" id="bio-npsn-sd" name="npsn_sd" value="{{ old('npsn_sd', $pendaftaran->npsn_sd) }}" placeholder="8 digit kode NPSN sekolah asal">
          </div>
          <div class="field">
            <label for="bio-tahun-lulus">Tahun Lulus SD / MI <span class="req">*</span></label>
            <input type="number" id="bio-tahun-lulus" name="tahun_lulus_sd" value="{{ old('tahun_lulus_sd', $pendaftaran->tahun_lulus_sd ?: 2026) }}" min="2020" max="2030" required>
          </div>
        </div>
      </section>

      <section class="ppdb-fieldset" aria-labelledby="bio-sec-c">
        <h2 class="ppdb-fieldset__title" id="bio-sec-c">C. Data Orang Tua / Wali</h2>
        <div class="ppdb-form-grid">
          <div class="field">
            <label for="bio-ayah">Nama Lengkap Ayah <span class="req">*</span></label>
            <input type="text" id="bio-ayah" name="nama_ayah" value="{{ old('nama_ayah', $pendaftaran->nama_ayah) }}" placeholder="Nama ayah kandung / wali" required>
          </div>
          <div class="field">
            <label for="bio-kerja-ayah">Pekerjaan Ayah</label>
            <input type="text" id="bio-kerja-ayah" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $pendaftaran->pekerjaan_ayah) }}" placeholder="Contoh: Karyawan Swasta / Wiraswasta">
          </div>
          <div class="field">
            <label for="bio-hp-ayah">No. HP / WhatsApp Ayah</label>
            <input type="tel" id="bio-hp-ayah" name="no_hp_ayah" value="{{ old('no_hp_ayah', $pendaftaran->no_hp_ayah) }}" placeholder="Contoh: 081234567890">
          </div>

          <div class="field">
            <label for="bio-ibu">Nama Lengkap Ibu Kandung <span class="req">*</span></label>
            <input type="text" id="bio-ibu" name="nama_ibu" value="{{ old('nama_ibu', $pendaftaran->nama_ibu) }}" placeholder="Nama ibu kandung sesuai KK" required>
          </div>
          <div class="field">
            <label for="bio-kerja-ibu">Pekerjaan Ibu</label>
            <input type="text" id="bio-kerja-ibu" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $pendaftaran->pekerjaan_ibu) }}" placeholder="Contoh: Ibu Rumah Tangga / Guru">
          </div>
          <div class="field">
            <label for="bio-hp-ibu">No. HP / WhatsApp Ibu <span class="req">*</span></label>
            <input type="tel" id="bio-hp-ibu" name="no_hp_ibu" value="{{ old('no_hp_ibu', $pendaftaran->no_hp_ibu ?: ('0' . substr($account->whatsapp, 2))) }}" placeholder="Contoh: 081234567890" required>
          </div>

          <div class="field">
            <label for="bio-wali">Nama Wali (Opsional)</label>
            <input type="text" id="bio-wali" name="nama_wali" value="{{ old('nama_wali', $pendaftaran->nama_wali) }}" placeholder="Isi bila tinggal bersama wali">
          </div>
          <div class="field">
            <label for="bio-hub-wali">Hubungan Wali (Opsional)</label>
            <input type="text" id="bio-hub-wali" name="hubungan_wali" value="{{ old('hubungan_wali', $pendaftaran->hubungan_wali) }}" placeholder="Contoh: Paman / Kakek">
          </div>
          <div class="field">
            <label for="bio-penghasilan">Rentang Penghasilan Orang Tua</label>
            <select id="bio-penghasilan" name="penghasilan_ortu">
              <option value="">-- Pilih Rentang Penghasilan --</option>
              @foreach(['< Rp 2.000.000', 'Rp 2.000.000 - Rp 4.000.000', 'Rp 4.000.000 - Rp 7.000.000', '> Rp 7.000.000'] as $incOpt)
                <option value="{{ $incOpt }}" @selected(old('penghasilan_ortu', $pendaftaran->penghasilan_ortu) === $incOpt)>{{ $incOpt }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </section>

      <div class="ppdb-form-foot">
        <button type="submit" class="btn btn--primary">
          <span>Simpan Data Diri &amp; Lanjut</span>
          <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
        </button>
        @if($pendaftaran->canAccessBerkas())
          <a href="{{ route('ppdb.portal.berkas') }}" class="btn btn--ghost">Ke Tahap 4: Berkas</a>
        @endif
      </div>
    </form>
  </section>
</div>
@endsection
