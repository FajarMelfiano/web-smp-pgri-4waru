@extends('layouts.ppdb-portal')

@section('title', 'Kuesioner Calon Siswa | Portal PPDB ' . ($siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru'))

@php
  $saved = $pendaftaran->kuesioner ?? [];
  $selectedEkskul = old('minat_ekstrakurikuler', $saved['minat_ekstrakurikuler'] ?? []);
  if (! is_array($selectedEkskul)) {
      $selectedEkskul = [];
  }
  $ekskulOptions = [
    ['nama' => 'Pramuka', 'ket' => 'Kepanduan, kemandirian & kepemimpinan'],
    ['nama' => 'Futsal & Sepak Bola', 'ket' => 'Olahraga permainan & pembinaan turnamen'],
    ['nama' => 'Bola Voli', 'ket' => 'Ketangkasan fisik & kerja sama tim'],
    ['nama' => 'Seni Tari & Karawitan', 'ket' => 'Pelestarian budaya & seni pertunjukan'],
    ['nama' => 'Banjari & Hadrah', 'ket' => 'Seni musik Islami & kegiatan keagamaan'],
    ['nama' => 'Paskibra', 'ket' => 'Ketertiban baris-berbaris & tata upacara'],
    ['nama' => 'English & Sains Club', 'ket' => 'Pengembangan bahasa & olimpiade akademik'],
    ['nama' => 'Jurnalistik & Multimedia', 'ket' => 'Fotografi, desain grafis & publikasi sekolah'],
  ];
@endphp

@section('content')
<div class="ppdb-portal-stack">
  <section class="adm-panel" aria-labelledby="ppdb-kuesioner-heading">
    <div class="adm-panel__head">
      <div>
        <span class="eyebrow">Tahap 5 dari 6</span>
        <h1 class="adm-panel__title" id="ppdb-kuesioner-heading">Kuesioner &amp; Pernyataan Calon Siswa</h1>
        <p class="adm-panel__sub">Isi kuesioner singkat ini untuk membantu sekolah memetakan minat bakat siswa dan menyelesaikan pendaftaran.</p>
      </div>
    </div>

    <div class="adm-panel__body">
      @if($errors->any())
        <div class="form-alert form-alert--error" role="alert" style="margin-bottom:1.25rem">
          <svg class="i" aria-hidden="true"><use href="#i-info"/></svg>
          <div class="form-alert__body">
            <strong>Periksa kembali jawaban kuesioner:</strong>
            <ul>
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('ppdb.portal.kuesioner.store') }}" class="ppdb-portal-form">
        @csrf

        <div class="field">
          <label for="kues-sumber">1. Dari mana Anda mendapatkan informasi PPDB {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}? <span class="req">*</span></label>
          <select id="kues-sumber" name="sumber_info" required>
            <option value="">-- Pilih Sumber Informasi --</option>
            @foreach(['Sosialisasi ke SD / MI', 'Rekomendasi Alumni / Keluarga', 'Website Resmi Sekolah', 'Media Sosial (Instagram / TikTok / Facebook)', 'Spanduk / Brosur di Lingkungan Waru', 'Lainnya'] as $sumberOpt)
              <option value="{{ $sumberOpt }}" @selected(old('sumber_info', $saved['sumber_info'] ?? '') === $sumberOpt)>{{ $sumberOpt }}</option>
            @endforeach
          </select>
        </div>

        <div class="field">
          <label for="kues-alasan">2. Apa alasan utama Anda dan orang tua memilih {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}? <span class="req">*</span></label>
          <textarea id="kues-alasan" name="alasan_memilih" rows="3" placeholder="Contoh: Lokasi strategis di Waru, pembinaan karakter & keagamaan yang baik, serta ekstrakurikuler yang aktif." required>{{ old('alasan_memilih', $saved['alasan_memilih'] ?? '') }}</textarea>
        </div>

        <div class="field">
          <label>3. Minat Ekstrakurikuler (Boleh pilih maksimal 5)</label>
          <div class="ppdb-check-grid">
            @foreach($ekskulOptions as $eks)
              <label class="ppdb-check-card">
                <input type="checkbox" class="ppdb-check-card__input" name="minat_ekstrakurikuler[]" value="{{ $eks['nama'] }}" @checked(in_array($eks['nama'], $selectedEkskul, true))>
                <span class="ppdb-check-card__box" aria-hidden="true">
                  <svg class="i"><use href="#i-check"/></svg>
                </span>
                <span class="ppdb-check-card__text">
                  <strong>{{ $eks['nama'] }}</strong>
                  <small>{{ $eks['ket'] }}</small>
                </span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="field">
          <label for="kues-saran">4. Harapan atau Pesan untuk Panitia PPDB (Opsional)</label>
          <textarea id="kues-saran" name="saran" rows="2" placeholder="Tuliskan harapan Anda selama menempuh pendidikan di SMP PGRI 4 Waru">{{ old('saran', $saved['saran'] ?? '') }}</textarea>
        </div>

        <div class="ppdb-pledge-box">
          <label class="ppdb-check-card ppdb-check-card--pledge">
            <input type="checkbox" class="ppdb-check-card__input" name="siap_tata_tertib" value="1" @checked(old('siap_tata_tertib', $saved['siap_tata_tertib'] ?? false)) required>
            <span class="ppdb-check-card__box" aria-hidden="true">
              <svg class="i"><use href="#i-check"/></svg>
            </span>
            <span class="ppdb-check-card__text">
              <strong>Pernyataan Kebenaran Data &amp; Tata Tertib <span class="req">*</span></strong>
              <small>Saya menyatakan bahwa seluruh data dan berkas yang diunggah adalah benar, serta calon siswa bersedia mematuhi seluruh tata tertib {{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}.</small>
            </span>
          </label>
        </div>

        <div class="ppdb-form-foot">
          <button type="submit" class="btn btn--primary">
            <span>Simpan Kuesioner &amp; Buka Kartu Peserta</span>
            <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
          </button>
        </div>
      </form>
    </div>
  </section>
</div>
@endsection
