@extends('layouts.app')

@section('title', 'Kontak — SMP PGRI 4 Waru')
@section('description', 'Alamat, telepon, email, dan peta lokasi SMP PGRI 4 Waru di Jl. Anggrek No. 109, Kureksari, Waru, Sidoarjo 61256.')
@section('og_title', 'Kontak SMP PGRI 4 Waru')

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:540px;left:50%;top:-240px;transform:translateX(-50%)"></span>
      <span class="deco__ring deco__ring--dash" style="width:800px;left:50%;top:-380px;transform:translateX(-50%)"></span>
      <span class="deco__diamond deco__diamond--sm" style="left:16%;top:48%"></span>
      <span class="deco__dots" style="right:8%;bottom:14%"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> Kontak</p>
      <h1>Kontak dan lokasi</h1>
      <p class="lead">SMP PGRI 4 Waru berada di Jl. Anggrek No. 109, Kureksari, Kecamatan Waru, Kabupaten Sidoarjo. Telepon dulu sebelum berangkat.</p>
    </div>
  </div>

  <section class="section" aria-labelledby="lokasi">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Lokasi</p>
        <h2 id="lokasi">Alamat <span class="accent">sekolah</span></h2>
        <span class="rule"></span>
        <p>Koordinat -7.3619, 112.7327 mengarah ke halaman sekolah di Jalan Anggrek.</p>
      </div>

      <div class="loc">
        <div class="rise">
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

  <section class="section section--tint" aria-labelledby="kanal">
    <div class="wrap">
      <div class="sec-head sec-head--center rise">
        <p class="eyebrow">Kanal</p>
        <h2 id="kanal">Tiga cara <span class="accent">menghubungi</span></h2>
        <span class="rule"></span>
        <p>Semua kanal menuju langsung ke sekolah, bukan ke formulir perantara.</p>
      </div>

      <div class="cards cards--trio">
        <article class="card rise">
          <span class="card__icon"><svg class="i" aria-hidden="true"><use href="#i-phone"/></svg></span>
          <h3>Telepon</h3>
          <p><a href="tel:+62318538447">(031) 8538447</a> — jalur tercepat untuk pertanyaan pendaftaran dan administrasi.</p>
        </article>
        <article class="card card--red rise">
          <span class="card__icon"><svg class="i" aria-hidden="true"><use href="#i-mail"/></svg></span>
          <h3>Email</h3>
          <p>smp.pgri4waru@gmail.com — untuk surat resmi, permintaan dokumen, atau pertanyaan yang perlu lampiran.</p>
        </article>
        <article class="card rise">
          <span class="card__icon"><svg class="i" aria-hidden="true"><use href="#i-ig"/></svg></span>
          <h3>Instagram</h3>
          <p><a href="https://www.instagram.com/smppgri4waru/">@smppgri4waru</a> — kabar kegiatan sekolah dan informasi singkat.</p>
        </article>
      </div>

      <p class="note">Halaman ini tidak punya formulir pesan karena situs purwarupa belum punya server email. Gunakan telepon atau email sekolah.</p>
    </div>
  </section>

  <section class="section" aria-labelledby="datang">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Kunjungan</p>
        <h2 id="datang">Kalau datang <span class="accent">langsung</span></h2>
        <span class="rule"></span>
        <p>Kegiatan belajar berlangsung pagi hari, enam hari dalam seminggu.</p>
      </div>

      <div class="row">
        <figure class="figure rise">
          <span class="deco__dots" aria-hidden="true"></span>
          <div class="figure__block" aria-hidden="true"></div>
          <div class="figure__sheet">
            <span class="figure__label">Catatan</span>
            <p class="figure__title">Sebelum berangkat</p>
            <p class="figure__note">Jam layanan yang pasti tidak dipublikasikan. Konfirmasi lewat telepon.</p>
            <ul class="figure__rows">
              <li><span>Hari belajar</span><b>Enam hari</b></li>
              <li><span>Waktu</span><b>Pagi</b></li>
              <li><span>Konfirmasi</span><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></li>
            </ul>
          </div>
        </figure>

        <div class="row__body rise">
          <h3>Datang pada jam kerja sekolah</h3>
          <p>Supaya ada petugas yang bisa membantu, datang pada jam kerja sekolah. Untuk keperluan administrasi, datang lebih awal memberi waktu lebih longgar sebelum kegiatan belajar selesai.</p>
          <p>Kalau membawa dokumen, susun dalam satu map agar pemeriksaan lebih cepat. Daftar dokumen pendaftaran ada di <a href="{{ route('ppdb') }}">halaman PPDB</a>.</p>
          <p class="note">Bisa datang tanpa menelepon, tetapi tidak ada jaminan ada petugas yang menerima. Telepon dulu lebih aman, terutama untuk urusan pendaftaran.</p>
          <a class="btn btn--ghost" href="tel:+62318538447">Telepon sebelum datang <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
        </div>
      </div>
    </div>
  </section>

  <section class="section section--tint" aria-labelledby="tanya">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Pertanyaan</p>
        <h2 id="tanya">Sebelum <span class="accent">datang</span></h2>
      </div>

      <div class="faq rise">
        <details>
          <summary>Bisa datang tanpa menelepon dulu?</summary>
          <div class="a"><p>Bisa, tetapi tidak ada jaminan ada petugas yang menerima. Telepon dulu lebih aman, terutama untuk urusan pendaftaran.</p></div>
        </details>
        <details>
          <summary>Apakah ada formulir pesan di halaman ini?</summary>
          <div class="a"><p>Belum. Situs purwarupa ini tidak punya server email, jadi semua pesan disampaikan lewat telepon, email, atau datang langsung.</p></div>
        </details>
        <details>
          <summary>Ke mana mengirim berkas untuk keperluan resmi?</summary>
          <div class="a"><p>Kirim ke <a href="mailto:smp.pgri4waru@gmail.com">smp.pgri4waru@gmail.com</a>, lalu konfirmasi lewat telepon agar tidak tertunda.</p></div>
        </details>
        <details>
          <summary>Apakah alamat ini sama dengan yang ada di Google Maps?</summary>
          <div class="a"><p>Ya. Koordinat -7.3619, 112.7327 mengarah ke Jl. Anggrek No. 109, Kureksari. Gunakan tautan rute di atas untuk menghindari salah titik.</p></div>
        </details>
      </div>
    </div>
  </section>
@endsection
