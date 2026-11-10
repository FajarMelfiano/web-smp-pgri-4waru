@extends('layouts.app')

@section('title', 'Akademik — SMP PGRI 4 Waru')
@section('description', 'Tiga tingkat, sebelas mata pelajaran umum jenjang SMP, dan cara penilaian di SMP PGRI 4 Waru, Kureksari, Waru, Sidoarjo.')
@section('og_title', 'Akademik SMP PGRI 4 Waru')

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:520px;right:-160px;top:-180px"></span>
      <span class="deco__ring deco__ring--dash" style="width:760px;left:-280px;top:-240px"></span>
      <span class="deco__diamond deco__diamond--sm" style="left:12%;top:40%"></span>
      <span class="deco__dots" style="right:9%;bottom:16%"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> Akademik</p>
      <h1>Akademik</h1>
      <p class="lead">Tiga tingkat, sebelas mata pelajaran, dan cara penilaian yang berlaku selama siswa belajar di sini.</p>
    </div>
  </div>

  <section class="section" aria-labelledby="jenjang">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Tingkat</p>
        <h2 id="jenjang">Tiga tahun, <span class="accent">tiga tingkat</span></h2>
        <span class="rule"></span>
        <p>Setiap tingkat punya wali kelas yang mendampingi satu rombongan belajar.</p>
      </div>

      <ol class="steps steps--trio rise">
        <li><span class="n">VII</span><span><span class="t">Kelas VII</span><span class="d">Tahun pertama. Penyesuaian dari SD ke SMP, pengenalan kebiasaan belajar.</span></span></li>
        <li><span class="n">VIII</span><span><span class="t">Kelas VIII</span><span class="d">Tahun kedua. Pendalaman materi dan pemilihan minat di kegiatan sekolah.</span></span></li>
        <li><span class="n">IX</span><span><span class="t">Kelas IX</span><span class="d">Tahun ketiga. Persiapan penilaian akhir dan kelanjutan ke jenjang berikutnya.</span></span></li>
      </ol>
      <p class="note">Kegiatan belajar berlangsung pagi hari, enam hari dalam seminggu. Jadwal pelajaran dan kalender akademik diminta langsung di kantor sekolah.</p>
    </div>
  </section>

  <section class="section section--tint" aria-labelledby="mapel">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Mata pelajaran</p>
        <h2 id="mapel">Indeks mata <span class="accent">pelajaran</span></h2>
        <span class="rule"></span>
        <p>Struktur kurikulum nasional jenjang SMP. Saring menurut kelompok untuk melihat susunannya.</p>
      </div>

      <div class="index-tools rise">
        <div class="interactive-toolbar interactive-toolbar--flush">
          <div class="chips" data-filters role="group" aria-label="Saring mata pelajaran">
            <button class="chip" type="button" data-filter="semua" aria-pressed="true">Semua <span class="count">11</span></button>
            <button class="chip" type="button" data-filter="bahasa" aria-pressed="false">Bahasa <span class="count">2</span></button>
            <button class="chip" type="button" data-filter="sains" aria-pressed="false">Sains <span class="count">2</span></button>
            <button class="chip" type="button" data-filter="sosial" aria-pressed="false">Sosial <span class="count">3</span></button>
            <button class="chip" type="button" data-filter="keterampilan" aria-pressed="false">Keterampilan <span class="count">4</span></button>
          </div>
          <div class="search-pill search-pill--compact">
            <svg class="i" aria-hidden="true"><use href="#i-search"/></svg>
            <label for="subject-search-input" class="sr-only">Cari mata pelajaran</label>
            <input id="subject-search-input" type="search" data-subject-search placeholder="Cari nama atau topik pelajaran...">
          </div>
        </div>

        <ul class="index-list" data-index>
          <li data-cat="sosial"><span class="n">01</span><span><span class="t">Pendidikan Agama dan Budi Pekerti</span><span class="g">Pembiasaan ibadah dan akhlak</span></span></li>
          <li data-cat="sosial"><span class="n">02</span><span><span class="t">Pendidikan Pancasila</span><span class="g">Kewargaan, hak, dan kewajiban</span></span></li>
          <li data-cat="bahasa"><span class="n">03</span><span><span class="t">Bahasa Indonesia</span><span class="g">Membaca, menulis, dan berbicara</span></span></li>
          <li data-cat="sains"><span class="n">04</span><span><span class="t">Matematika</span><span class="g">Bilangan, aljabar, geometri, dan data</span></span></li>
          <li data-cat="sains"><span class="n">05</span><span><span class="t">Ilmu Pengetahuan Alam</span><span class="g">Fisika, biologi, dan kimia dasar</span></span></li>
          <li data-cat="sosial"><span class="n">06</span><span><span class="t">Ilmu Pengetahuan Sosial</span><span class="g">Geografi, sejarah, ekonomi, sosiologi</span></span></li>
          <li data-cat="bahasa"><span class="n">07</span><span><span class="t">Bahasa Inggris</span><span class="g">Menyimak, berbicara, membaca, menulis</span></span></li>
          <li data-cat="keterampilan"><span class="n">08</span><span><span class="t">Pendidikan Jasmani</span><span class="g">Olahraga dan kesehatan</span></span></li>
          <li data-cat="keterampilan"><span class="n">09</span><span><span class="t">Seni dan Budaya</span><span class="g">Seni rupa, musik, atau tari</span></span></li>
          <li data-cat="keterampilan"><span class="n">10</span><span><span class="t">Informatika</span><span class="g">Berpikir komputasional dan literasi digital</span></span></li>
          <li data-cat="keterampilan"><span class="n">11</span><span><span class="t">Prakarya</span><span class="g">Kerajinan, budidaya, dan rekayasa</span></span></li>
        </ul>
        <p class="index-status" data-index-status role="status" aria-live="polite">Menampilkan 11 mata pelajaran.</p>
        <p class="note">Daftar ini mengikuti struktur mata pelajaran umum jenjang SMP. Pelaksanaan per tingkat dan muatan lokal ditetapkan sekolah.</p>
      </div>
    </div>
  </section>

  <section class="section" id="penilaian" aria-labelledby="judul-penilaian">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Penilaian</p>
        <h2 id="judul-penilaian">Cara nilai <span class="accent">dilaporkan</span></h2>
        <span class="rule"></span>
        <p>Guru menilai proses dan hasil, lalu melaporkannya ke wali siswa setiap semester.</p>
      </div>

      <div class="row">
        <figure class="figure rise">
          <span class="deco__dots" aria-hidden="true"></span>
          <div class="figure__block" aria-hidden="true"></div>
          <div class="figure__sheet">
            <span class="figure__label">Penilaian</span>
            <p class="figure__title">Proses dan hasil</p>
            <p class="figure__note">Tiga bentuk penilaian yang dipakai selama satu semester.</p>
            <ul class="figure__rows">
              <li><span>Tugas</span><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></li>
              <li><span>Ulangan harian</span><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></li>
              <li><span>Penilaian akhir semester</span><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></li>
            </ul>
          </div>
        </figure>

        <div class="prose row__body rise">
          <p>Penilaian dilakukan lewat tugas, ulangan harian, dan penilaian akhir semester. Hasilnya dilaporkan kepada wali siswa pada akhir setiap semester.</p>
          <p>Kalau siswa tertinggal pada satu mata pelajaran, wali kelas dan guru mata pelajaran membicarakannya dengan orang tua. Bentuk pendampingan ditentukan per kasus, jadi tidak dijanjikan seragam di halaman ini.</p>
          <p>Pertanyaan tentang nilai, kenaikan kelas, atau kelulusan sebaiknya disampaikan langsung ke wali kelas atau kantor sekolah.</p>
          <p class="note">Rapor, jadwal ujian, dan kalender akademik tidak dipublikasikan di situs ini.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section section--tint" aria-labelledby="tanya">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Pertanyaan</p>
        <h2 id="tanya">Pertanyaan tentang <span class="accent">pembelajaran</span></h2>
      </div>

      <div class="faq rise">
        <details>
          <summary>Apakah ada ekstrakurikuler?</summary>
          <div class="a"><p>Kegiatan di luar jam pelajaran ditentukan sekolah setiap tahun. Daftar resminya belum tersedia di sini, jadi tanyakan di kantor.</p></div>
        </details>
        <details>
          <summary>Apakah jadwal pelajaran bisa diunduh?</summary>
          <div class="a"><p>Belum. Jadwal dibagikan ke siswa dan wali siswa pada awal semester.</p></div>
        </details>
        <details>
          <summary>Bagaimana kalau siswa sakit dan tertinggal materi?</summary>
          <div class="a"><p>Hubungi wali kelas. Wali kelas yang mengatur komunikasi dengan guru mata pelajaran terkait.</p></div>
        </details>
        <details>
          <summary>Apakah sekolah menyediakan lab komputer?</summary>
          <div class="a"><p>Data referensi publik mencatat fasilitas ruang yang terbatas. Rincian sarana terbaru hanya bisa dipastikan sekolah.</p></div>
        </details>
      </div>
    </div>
  </section>
@endsection
