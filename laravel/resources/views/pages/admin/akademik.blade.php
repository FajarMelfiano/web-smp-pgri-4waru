@extends('layouts.admin')

@section('title', 'Kurikulum & Profil — Admin SMP PGRI 4 Waru')
@section('description', 'Data struktur kurikulum 11 mata pelajaran dan identitas resmi SMP PGRI 4 Waru.')
@section('crumb', 'Kurikulum & Profil')

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Kurikulum &amp; Identitas Pokok</h1>
      <p>Daftar 11 mata pelajaran jenjang SMP beserta rincian data kelembagaan resmi SMP PGRI 4 Waru.</p>
    </div>
    <div class="adm-actions">
      <a class="btn btn--ghost btn--sm" href="{{ route('akademik') }}">
        <span>Buka Halaman Akademik</span>
        <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg>
      </a>
      <a class="btn btn--primary btn--sm" href="{{ route('admin.pengaturan') }}">
        <svg class="i" aria-hidden="true"><use href="#i-settings"/></svg>
        <span>Sunting Identitas Situs</span>
      </a>
    </div>
  </div>

  <div class="adm-grid-2">
    <section class="adm-panel" aria-labelledby="mapel-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="mapel-heading">Struktur Mata Pelajaran SMP</h2>
          <span class="adm-panel__badge">{{ count($mapel) }} mapel</span>
        </div>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th scope="col">No</th>
              <th scope="col">Mata Pelajaran</th>
              <th scope="col">Kelompok</th>
              <th scope="col">Ruang Lingkup</th>
            </tr>
          </thead>
          <tbody>
            @foreach($mapel as $m)
              <tr>
                <td><b>{{ $m['no'] }}</b></td>
                <td><span class="adm-cell-primary">{{ $m['nama'] }}</span></td>
                <td><span class="adm-status adm-status--baru">{{ $m['kelompok'] }}</span></td>
                <td>{{ $m['fokus'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

    <section class="adm-panel" aria-labelledby="identitas-resmi-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="identitas-resmi-heading">Data Kelembagaan Terverifikasi</h2>
        </div>
        <a href="{{ route('profil') }}" class="btn btn--ghost btn--sm">Lihat Profil Publik</a>
      </div>

      <dl class="adm-meta-grid">
        @foreach($identitas as $label => $nilai)
          <div class="adm-meta-card">
            <dt>{{ $label }}</dt>
            <dd>{{ $nilai }}</dd>
          </div>
        @endforeach
      </dl>
    </section>
  </div>
@endsection
