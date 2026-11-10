@extends('layouts.app')

@section('content')

<section class="hero" aria-labelledby="judul">
  <div class="deco" aria-hidden="true">
    <span class="deco__ring" style="width:660px;left:50%;top:-190px;transform:translateX(-50%)"></span>
    <span class="deco__ring deco__ring--dash" style="width:900px;left:50%;top:-330px;transform:translateX(-50%)"></span>
    <span class="deco__diamond" style="left:7%;top:24%"></span>
    <span class="deco__diamond deco__diamond--sm" style="right:12%;top:16%"></span>
    <span class="deco__diamond deco__diamond--sm" style="right:24%;bottom:24%"></span>
    <span class="deco__dots" style="left:5%;bottom:16%"></span>
    <span class="deco__dots" style="right:5%;top:34%"></span>
    <span class="deco__plus" style="right:26%;top:9%"></span>
  </div>

  <div class="wrap hero__inner">
    <p class="eyebrow">{{ $siteSettings['hero_eyebrow'] ?? 'Selamat datang di' }} @if(empty($siteSettings['hero_eyebrow']))<b>{{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}</b>@endif</p>
    @if(!empty($siteSettings['hero_judul']))
      <h1 class="hero__title" id="judul">{{ $siteSettings['hero_judul'] }}</h1>
    @else
      <h1 class="hero__title" id="judul">Sekolah menengah pertama untuk keluarga Kureksari <span class="accent">sejak 1979.</span></h1>
    @endif
    <p class="hero__lead">{{ $siteSettings['hero_deskripsi'] ?? 'Sekolah swasta di bawah YPLP DASMEN PGRI Jawa Timur. Alamatnya tidak pernah pindah dari Jalan Anggrek No. 109, dan kegiatan belajar berlangsung pagi hari, enam hari dalam seminggu.' }}</p>

    <div class="hero__actions">
      <a class="btn btn--primary" href="{{ route('ppdb') }}">Alur pendaftaran <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
      <a class="btn btn--ghost" href="{{ route('profil') }}">Kenali sekolah</a>
    </div>

    @if(!empty($siteSettings['hero_image_path']))
      <figure style="margin:2rem auto 0;max-width:860px;border-radius:var(--r-lg);overflow:hidden;border:1px solid var(--line);box-shadow:var(--shadow-md)">
        <img src="{{ asset('storage/'.$siteSettings['hero_image_path']) }}" alt="{{ $siteSettings['nama_sekolah'] ?? 'SMP PGRI 4 Waru' }}" style="width:100%;max-height:420px;object-fit:cover;display:block">
      </figure>
    @endif

    <ul class="facts">
      <li><b>{{ $siteSettings['tahun_berdiri'] ?? '1979' }}</b><span>Tahun berdiri</span></li>
      <li><b>{{ $siteSettings['luas_tanah'] ?? '660 m²' }}</b><span>Luas tanah</span></li>
      <li><b>6 hari</b><span>Belajar per minggu</span></li>
      <li><b>{{ $siteSettings['npsn'] ?? '20540020' }}</b><span>NPSN</span></li>
    </ul>
    <p class="hero__meta">Purwarupa tugas sekolah. Data pokok dirangkum dari Data Referensi Kemendikdasmen.</p>
  </div>
</section>

<section class="section section--tint" aria-labelledby="alasan">
  <div class="wrap">
    <div class="sec-head sec-head--center rise">
      <p class="eyebrow">Kenapa sekolah ini</p>
      <h2 id="alasan">Yang membuat sekolah ini <span class="accent">mudah dikenali</span></h2>
      <span class="rule"></span>
      <p>Bukan janji promosi. Ini yang tercatat di dokumen pendirian dan data referensi sekolah.</p>
    </div>

    <div class="cards">
      <article class="card card--red rise">
        <span class="card__icon"><svg class="i" aria-hidden="true"><use href="#i-calendar"/></svg></span>
        <h3>Berdiri sejak 1979</h3>
        <p>Izin pendirian tercatat dalam SK 421.3/2829/404.3.1/1979.</p>
      </article>
      <article class="card rise">
        <span class="card__icon"><svg class="i" aria-hidden="true"><use href="#i-pin"/></svg></span>
        <h3>Alamat tidak pindah</h3>
        <p>Jl. Anggrek No. 109, Kureksari, Kecamatan Waru, Kabupaten Sidoarjo.</p>
      </article>
      <article class="card rise">
        <span class="card__icon"><svg class="i" aria-hidden="true"><use href="#i-shield"/></svg></span>
        <h3>Naungan jelas</h3>
        <p>Penyelenggaraan sekolah berada di bawah YPLP DASMEN PGRI Jawa Timur.</p>
      </article>
      <article class="card rise">
        <span class="card__icon"><svg class="i" aria-hidden="true"><use href="#i-clock"/></svg></span>
        <h3>Belajar pagi, enam hari</h3>
        <p>Kegiatan belajar berlangsung pagi hari, enam hari dalam seminggu.</p>
      </article>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="akademik">
  <div class="deco" aria-hidden="true">
    <span class="deco__ring" style="width:520px;right:-220px;top:10%"></span>
    <span class="deco__diamond" style="left:4%;top:18%"></span>
    <span class="deco__plus" style="right:10%;bottom:12%"></span>
  </div>

  <div class="wrap">
    <div class="sec-head sec-head--center rise">
      <p class="eyebrow">Akademik</p>
      <h2 id="akademik">Dari kelas VII sampai <span class="accent">kelas IX</span></h2>
      <span class="rule"></span>
      <p>Tiga tingkat, sebelas mata pelajaran umum jenjang SMP, dan penilaian yang dilaporkan setiap semester.</p>
    </div>

    <div class="row">
      <figure class="figure rise">
        <span class="deco__dots" aria-hidden="true"></span>
        <div class="figure__block" aria-hidden="true"></div>
        <div class="figure__sheet">
          <span class="figure__label">Tingkat</span>
          <p class="figure__title">Kelas VII sampai IX</p>
          <p class="figure__note">Wali kelas mendampingi satu rombongan belajar di setiap tingkat.</p>
          <ul class="figure__rows">
            <li><span>Kelas VII</span><b>Tahun pertama</b></li>
            <li><span>Kelas VIII</span><b>Pendalaman</b></li>
            <li><span>Kelas IX</span><b>Persiapan akhir</b></li>
          </ul>
        </div>
      </figure>

      <div class="row__body rise">
        <h3>Tiga tingkat, satu alur belajar</h3>
        <p>Setiap tingkat punya wali kelas yang mendampingi satu rombongan belajar. Tahun pertama dipakai untuk menyesuaikan diri dari SD, tahun kedua untuk mendalami materi, tahun ketiga untuk menyiapkan penilaian akhir.</p>
        <div class="row__group">
          <span class="row__label">Kelompok mata pelajaran</span>
          <div class="chips">
            <span class="chip chip--static">Bahasa</span>
            <span class="chip chip--static">Sains</span>
            <span class="chip chip--static">Sosial</span>
            <span class="chip chip--static">Keterampilan</span>
          </div>
        </div>
        <a class="btn btn--ghost" href="{{ route('akademik') }}">Lihat halaman akademik <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
      </div>
    </div>

    <div class="row row--flip">
      <figure class="figure rise">
        <span class="deco__dots" aria-hidden="true"></span>
        <div class="figure__block" aria-hidden="true"></div>
        <div class="figure__sheet">
          <span class="figure__label">Penilaian</span>
          <p class="figure__title">Dilaporkan tiap semester</p>
          <p class="figure__note">Guru menilai proses dan hasil, lalu melaporkannya ke wali siswa.</p>
          <ul class="figure__rows">
            <li><span>Tugas</span><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></li>
            <li><span>Ulangan harian</span><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></li>
            <li><span>Penilaian akhir semester</span><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></li>
          </ul>
        </div>
      </figure>

      <div class="row__body rise">
        <h3>Nilai dilaporkan, bukan diumumkan di situs</h3>
        <p>Guru menilai proses dan hasil lewat tugas, ulangan harian, serta penilaian akhir semester. Hasilnya disampaikan kepada wali siswa pada akhir semester, bukan lewat halaman publik.</p>
        <div class="row__group">
          <span class="row__label">Yang tidak dipublikasikan</span>
          <div class="chips">
            <span class="chip chip--static">Rapor siswa</span>
            <span class="chip chip--static">Jadwal ujian</span>
            <span class="chip chip--static">Kalender akademik</span>
          </div>
        </div>
        <a class="btn btn--ghost" href="{{ route('akademik') }}#penilaian">Cara penilaian <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
      </div>
    </div>
  </div>
</section>

<section class="section section--tint" aria-labelledby="ppdb">
  <div class="wrap">
    <div class="banner rise">
      <div class="deco" aria-hidden="true">
        <span class="deco__ring" style="width:420px;right:-120px;top:-160px"></span>
        <span class="deco__ring deco__ring--dash" style="width:280px;right:40px;bottom:-140px"></span>
        <span class="deco__diamond" style="left:-6px;bottom:32px"></span>
      </div>
      <div class="banner__body">
        <p class="eyebrow">Pendaftaran</p>
        <h2 id="ppdb">Pendaftaran berlangsung <span class="accent">di kantor sekolah</span></h2>
        <p>Empat langkah, tanpa pendaftaran daring dan tanpa biaya yang ditagih lewat situs ini. Halaman PPDB membantu kamu menyiapkan pertanyaan sebelum datang.</p>
        <div class="banner__actions">
          <a class="btn btn--primary" href="{{ route('ppdb') }}">Buka halaman PPDB <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
          <a class="btn btn--ghost" href="tel:+62318538447">Telepon sekolah</a>
        </div>
      </div>
      <ul class="ticket">
        <li><span class="n">01</span><span><b>Hubungi kantor</b><span>Tanyakan jadwal dan sisa kursi tahun ajaran berjalan.</span></span></li>
        <li><span class="n">02</span><span><b>Ambil formulir</b><span>Diisi di kantor bersama petugas.</span></span></li>
        <li><span class="n">03</span><span><b>Serahkan dokumen</b><span>KK, akta kelahiran, keterangan lulus SD, pas foto.</span></span></li>
      </ul>
    </div>
  </div>
</section>

<section class="section section--white" aria-labelledby="kabar-sekolah">
  <div class="wrap">
    <div class="sec-head rise">
      <p class="eyebrow">Kabar &amp; Pengumuman</p>
      <h2 id="kabar-sekolah">Berita terbaru <span class="accent">sekolah</span></h2>
      <p>Informasi resmi kegiatan belajar, pengumuman tata usaha, dan agenda terkini SMP PGRI 4 Waru.</p>
      <span class="rule"></span>
    </div>

    @if(isset($beritaTerbaru) && $beritaTerbaru->isNotEmpty())
      <div class="news-grid">
        @foreach($beritaTerbaru as $post)
          <article class="news-card rise">
            @if($post->cover_path)
              <a href="{{ route('berita.show', $post->slug) }}" class="news-card__media" tabindex="-1" aria-hidden="true">
                <img src="{{ str_starts_with($post->cover_path, 'http') || str_starts_with($post->cover_path, '/') ? $post->cover_path : asset('storage/'.$post->cover_path) }}" alt="" loading="lazy">
              </a>
            @endif
            <div class="news-card__body">
              <div class="news-card__meta">
                <span class="news-card__badge">Berita Sekolah</span>
                <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('d M Y') ?? $post->created_at?->translatedFormat('d M Y') }}</time>
              </div>
              <h3 class="news-card__title">
                <a href="{{ route('berita.show', $post->slug) }}">{{ $post->judul }}</a>
              </h3>
              <p class="news-card__desc">{{ $post->excerpt(120) }}</p>
              <a class="link-arrow" href="{{ route('berita.show', $post->slug) }}">Baca selengkapnya <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
            </div>
          </article>
        @endforeach
      </div>
    @else
      <div class="card rise">
        <h3>Belum ada berita yang diterbitkan</h3>
        <p>Pengumuman terbaru dari kantor sekolah akan ditampilkan di halaman ini.</p>
      </div>
    @endif

    <div class="sec-foot rise">
      <a class="btn btn--ghost" href="{{ route('berita.index') }}">Lihat semua berita &amp; foto <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="galeri-sekolah">
  <div class="wrap">
    <div class="sec-head rise">
      <p class="eyebrow">Dokumentasi Visual</p>
      <h2 id="galeri-sekolah">Galeri foto <span class="accent">kegiatan</span></h2>
      <p>Potret lingkungan belajar, kegiatan siswa, dan fasilitas di Jl. Anggrek No. 109, Kureksari.</p>
      <span class="rule"></span>
    </div>

    @if(isset($galeriBeranda) && $galeriBeranda->isNotEmpty())
      @php
        $kategoriGaleriBeranda = $galeriBeranda->pluck('kategori')->filter()->unique()->values();
      @endphp
      <div class="interactive-toolbar rise" data-gallery-scope="beranda">
        <div class="filter-bar" role="group" aria-label="Saring kategori foto kegiatan">
          <button type="button" class="pill" data-gallery-filter="semua" aria-pressed="true">Semua Foto ({{ $galeriBeranda->count() }})</button>
          @foreach($kategoriGaleriBeranda as $kat)
            <button type="button" class="pill" data-gallery-filter="{{ $kat }}" aria-pressed="false">{{ $kat }}</button>
          @endforeach
        </div>
        <span class="interactive-toolbar__hint">Klik foto untuk tampilan layar penuh</span>
      </div>

      <div class="photo-grid" data-gallery-grid="beranda">
        @foreach($galeriBeranda as $foto)
          @php
            $fotoSrc = str_starts_with($foto->image_path, 'http') || str_starts_with($foto->image_path, '/') ? $foto->image_path : asset('storage/'.$foto->image_path);
          @endphp
          <figure class="photo-card rise" data-gallery-item data-cat="{{ $foto->kategori }}">
            <button
              type="button"
              class="photo-card__trigger"
              data-lightbox-trigger
              data-src="{{ $fotoSrc }}"
              data-title="{{ $foto->judul }}"
              data-desc="{{ $foto->keterangan }}"
              data-tag="{{ $foto->kategori }}"
              aria-label="Perbesar foto {{ $foto->judul }}"
            >
              <div class="photo-card__img">
                <img src="{{ $fotoSrc }}" alt="{{ $foto->judul }}" loading="lazy">
                <span class="photo-card__tag">{{ $foto->kategori }}</span>
                <span class="photo-card__zoom" aria-hidden="true">
                  <svg class="i" aria-hidden="true"><use href="#i-expand"/></svg>
                </span>
              </div>
              <figcaption class="photo-card__cap">
                <b>{{ $foto->judul }}</b>
                @if($foto->keterangan)
                  <span>{{ $foto->keterangan }}</span>
                @endif
              </figcaption>
            </button>
          </figure>
        @endforeach
      </div>
    @else
      <div class="card rise">
        <h3>Dokumentasi foto sedang disiapkan</h3>
        <p>Petugas sekolah dapat menambahkan foto kegiatan baru melalui panel administrasi galeri.</p>
      </div>
    @endif
  </div>
</section>

<section class="section section--white" aria-labelledby="suara-alumni">
  <div class="wrap">
    <div class="sec-head rise">
      <p class="eyebrow">Jejak Lulusan</p>
      <h2 id="suara-alumni">Kesan &amp; ulasan <span class="accent">para alumni</span></h2>
      <p>Cerita pengalaman belajar dari para alumni yang pernah menempuh pendidikan di SMP PGRI 4 Waru.</p>
      <span class="rule"></span>
    </div>

    @if(isset($ulasanAlumni) && $ulasanAlumni->isNotEmpty())
      <div class="alumni-grid">
        @foreach($ulasanAlumni as $rev)
          <article class="alumni-card rise">
            <p class="alumni-card__quote">“{{ $rev->ulasan }}”</p>
            <div class="alumni-card__author">
              @if($rev->foto_path)
                <img class="alumni-card__avatar" src="{{ str_starts_with($rev->foto_path, 'http') || str_starts_with($rev->foto_path, '/') ? $rev->foto_path : asset('storage/'.$rev->foto_path) }}" alt="{{ $rev->nama }}" loading="lazy">
              @else
                <span class="alumni-card__avatar alumni-card__avatar--initial" aria-hidden="true">{{ mb_strtoupper(mb_substr($rev->nama, 0, 1)) }}</span>
              @endif
              <div>
                <b class="alumni-card__name">{{ $rev->nama }}</b>
                <span class="alumni-card__meta">Lulusan {{ $rev->angkatan }}@if($rev->pekerjaan) · {{ $rev->pekerjaan }}@endif</span>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    @else
      <div class="card rise">
        <h3>Jadilah alumni pertama yang berbagi kesan</h3>
        <p>Bagikan cerita masa sekolah Anda untuk menginspirasi adik-adik kelas di SMP PGRI 4 Waru.</p>
      </div>
    @endif

    <div class="sec-foot rise">
      <a class="btn btn--primary" href="{{ route('alumni.index') }}">Buka halaman alumni &amp; tulis ulasan <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="lokasi">
  <div class="wrap">
    <div class="sec-head rise">
      <p class="eyebrow">Lokasi</p>
      <h2 id="lokasi">Datang ke <span class="accent">Jalan Anggrek</span></h2>
      <p>Sekolah berada di Kureksari, Kecamatan Waru, Kabupaten Sidoarjo. Hubungi dulu sebelum berangkat agar ada petugas yang menerima.</p>
    </div>

    <div class="loc">
      <div class="rise">
        <h3>Alamat sekolah</h3>
        <address class="address">
          <b>SMP PGRI 4 Waru</b>
          Jl. Anggrek No. 109, Kureksari<br>
          Kec. Waru, Kab. Sidoarjo<br>
          Jawa Timur 61256
        </address>
        <div class="loc__lines">
          <a href="https://www.google.com/maps/search/?api=1&amp;query=-7.3619,112.7327"><svg class="i" aria-hidden="true"><use href="#i-pin"/></svg> Buka rute</a>
          <a href="tel:+62318538447"><svg class="i" aria-hidden="true"><use href="#i-phone"/></svg> (031) 8538447</a>
          <a href="mailto:smp.pgri4waru@gmail.com"><svg class="i" aria-hidden="true"><use href="#i-mail"/></svg> Email</a>
        </div>

        <div class="social-pills">
          <a class="social-pill" href="tel:+62318538447">
            <span class="social-pill__icon"><svg class="i" aria-hidden="true"><use href="#i-phone"/></svg></span>
            <span><b>Telepon sekolah</b><span>(031) 8538447</span></span>
            <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg>
          </a>
          <a class="social-pill" href="https://www.instagram.com/smppgri4waru/">
            <span class="social-pill__icon"><svg class="i" aria-hidden="true"><use href="#i-ig"/></svg></span>
            <span><b>Instagram</b><span>@smppgri4waru</span></span>
            <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg>
          </a>
        </div>
      </div>

      <div class="map-frame rise">
        <button class="map-hold" type="button" data-map-hold
          data-src="https://www.openstreetmap.org/export/embed.html?bbox=112.7227%2C-7.3669%2C112.7427%2C-7.3569&amp;layer=mapnik&amp;marker=-7.3619%2C112.7327"
          data-title="Peta lokasi SMP PGRI 4 Waru di Jl. Anggrek No. 109, Kureksari, Waru, Sidoarjo">
          <svg class="map-pin" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" />
            <circle cx="12" cy="10" r="2.6" />
          </svg>
          <span class="map-hold-title">Peta belum dimuat</span>
          <span class="map-hold-sub">OpenStreetMap hanya dihubungi kalau kamu menekan tombol di bawah.</span>
          <span class="map-hold-action">Tampilkan peta</span>
        </button>
      </div>
    </div>
  </div>
</section>

@endsection