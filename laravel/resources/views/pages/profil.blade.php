@extends('layouts.app')

@section('title', 'Profil — SMP PGRI 4 Waru')
@section('description', 'Identitas, riwayat, visi misi, dan pengelolaan SMP PGRI 4 Waru di Kureksari, Kecamatan Waru, Sidoarjo.')
@section('og_title', 'Profil SMP PGRI 4 Waru')

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:520px;left:-140px;top:-180px"></span>
      <span class="deco__ring deco__ring--dash" style="width:720px;right:-260px;top:-220px"></span>
      <span class="deco__diamond deco__diamond--sm" style="right:14%;top:34%"></span>
      <span class="deco__dots" style="left:8%;bottom:14%"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> Profil</p>
      <h1>Profil sekolah</h1>
      <p class="lead">Identitas, riwayat, dan pengelolaan SMP PGRI 4 Waru di Kureksari, Kecamatan Waru, Kabupaten Sidoarjo.</p>
    </div>
  </div>

  <section class="section" aria-labelledby="identitas">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Identitas</p>
        <h2 id="identitas">Data pokok <span class="accent">sekolah</span></h2>
        <span class="rule"></span>
        <p>Angka dan alamat di bawah ini dipakai di seluruh halaman situs.</p>
      </div>

      <div class="data rise">
        <div class="data__head">
          <span>Identitas sekolah</span>
          <b>NPSN 20540020</b>
        </div>
        <dl class="data__rows">
          <div class="data__row"><dt>Nama</dt><dd>SMP PGRI 4 Waru</dd></div>
          <div class="data__row"><dt>Status</dt><dd>Swasta</dd></div>
          <div class="data__row"><dt>Naungan</dt><dd>YPLP DASMEN PGRI Jawa Timur</dd></div>
          <div class="data__row"><dt>SK pendirian</dt><dd>421.3/2829/404.3.1/1979</dd></div>
          <div class="data__row"><dt>Tahun berdiri</dt><dd>1979</dd></div>
          <div class="data__row"><dt>Luas tanah</dt><dd>660 m²</dd></div>
          <div class="data__row"><dt>Alamat</dt><dd>Jl. Anggrek No. 109, Kureksari, Kec. Waru, Kab. Sidoarjo</dd></div>
          <div class="data__row"><dt>Kode pos</dt><dd>61256</dd></div>
          <div class="data__row"><dt>Koordinat</dt><dd>-7.3619, 112.7327</dd></div>
          <div class="data__row"><dt>Waktu belajar</dt><dd>Pagi, enam hari dalam seminggu</dd></div>
          <div class="data__row"><dt>Telepon</dt><dd><a href="tel:+62318538447">(031) 8538447</a></dd></div>
          <div class="data__row"><dt>Email</dt><dd><a href="mailto:smp.pgri4waru@gmail.com">smp.pgri4waru@gmail.com</a></dd></div>
        </dl>
        <div class="data__foot">Sumber: Data Referensi Kemendikdasmen dan profil sekolah yang dipublikasikan.</div>
      </div>
      <p class="note">Akreditasi tidak dicantumkan karena sumber yang tersedia saling bertentangan. Status resmi sebaiknya ditanyakan langsung ke sekolah.</p>
    </div>
  </section>

  <section class="section section--tint" aria-labelledby="sejarah">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Riwayat</p>
        <h2 id="sejarah">Berdiri 1979, <span class="accent">alamatnya tidak pindah</span></h2>
        <span class="rule"></span>
      </div>

      <div class="row">
        <figure class="figure rise">
          <span class="deco__dots" aria-hidden="true"></span>
          <div class="figure__block" aria-hidden="true"></div>
          <div class="figure__sheet">
            <span class="figure__label">Kutipan arsip</span>
            <p class="figure__title">Surat Keputusan 1979</p>
            <p class="figure__note">Nomor ditulis apa adanya dari data publik. Salinan resmi hanya dikeluarkan kantor sekolah.</p>
            <ul class="figure__rows">
              <li><span>Izin pendirian</span><b>1979</b></li>
              <li><span>Penyelenggara</span><b>YPLP DASMEN</b></li>
              <li><span>Alamat</span><b>Jalan Anggrek</b></li>
            </ul>
          </div>
        </figure>

        <div class="row__body rise">
          <h3>Sekolah ini tumbuh bersama permukiman di Jalan Anggrek</h3>
          <p>SMP PGRI 4 Waru mulai berjalan pada 1979. Izin pendiriannya tercatat dalam Surat Keputusan 421.3/2829/404.3.1/1979, dan penyelenggaraannya berada di bawah YPLP DASMEN PGRI Jawa Timur.</p>
          <p>Sejak awal, sekolah ini melayani keluarga di Kureksari dan desa-desa sekitar Kecamatan Waru. Kegiatan belajar berlangsung pagi hari, enam hari dalam seminggu, di tanah seluas 660 meter persegi.</p>
          <p>Alamat sekolah tidak berubah: Jl. Anggrek No. 109, Kureksari, Kecamatan Waru, Kabupaten Sidoarjo, Jawa Timur 61256.</p>
          <a class="btn btn--ghost" href="{{ route('kontak') }}">Lihat lokasi</a>
        </div>
      </div>
    </div>
  </section>

  <section class="section" aria-labelledby="visi">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Visi dan misi</p>
        <h2 id="visi">Arah yang <span class="accent">dipegang sekolah</span></h2>
        <span class="rule"></span>
      </div>

      <div class="row">
        <div class="rise">
          <h3 style="font-size:var(--f-h3)">Visi</h3>
          <p class="draft-flag"><b>Draf.</b> Teks ini disusun untuk purwarupa dan belum dikonfirmasi sekolah. Teks resmi mengikuti dokumen yang berlaku.</p>
          <div class="prose" style="margin-top:1.25rem">
            <p>{{ $siteSettings['visi_sekolah'] ?? 'Menjadi sekolah menengah pertama yang dekat dengan warganya, tempat siswa Kureksari belajar dengan tertib, percaya diri, dan siap melanjutkan pendidikan.' }}</p>
          </div>
        </div>

        <div class="rise">
          <h3 style="font-size:var(--f-h3)">Misi</h3>
          <ol class="steps" style="margin-top:1rem">
            <li><span class="n">01</span><span><span class="t">Pembelajaran tertib</span><span class="d">Kegiatan belajar yang teratur dan mudah diikuti siswa.</span></span></li>
            <li><span class="n">02</span><span><span class="t">Pendampingan siswa</span><span class="d">Dampingan wali kelas dan guru mata pelajaran.</span></span></li>
            <li><span class="n">03</span><span><span class="t">Hubungan dengan wali</span><span class="d">Komunikasi dengan orang tua dan lingkungan sekitar.</span></span></li>
            <li><span class="n">04</span><span><span class="t">Kebiasaan baik</span><span class="d">Disiplin, kejujuran, dan kepedulian dalam kegiatan harian.</span></span></li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="section section--tint" aria-labelledby="pengelola">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Pengelolaan</p>
        <h2 id="pengelola">Peran yang <span class="accent">menjalankan sekolah</span></h2>
        <span class="rule"></span>
        <p>Nama dan susunan pengurus belum ditampilkan pada purwarupa ini.</p>
      </div>

      <ol class="steps steps--grid rise">
        <li><span class="n">01</span><span><span class="t">Kepala sekolah</span><span class="d">Memimpin penyelenggaraan pendidikan dan mewakili sekolah.</span></span></li>
        <li><span class="n">02</span><span><span class="t">Wakil kepala sekolah</span><span class="d">Kurikulum, kesiswaan, dan hubungan dengan wali siswa.</span></span></li>
        <li><span class="n">03</span><span><span class="t">Guru mata pelajaran</span><span class="d">Menyusun dan menjalankan pembelajaran di kelas.</span></span></li>
        <li><span class="n">04</span><span><span class="t">Wali kelas</span><span class="d">Mendampingi satu kelas dan menjadi penghubung orang tua.</span></span></li>
        <li><span class="n">05</span><span><span class="t">Tenaga kependidikan</span><span class="d">Administrasi, perpustakaan, dan sarana sekolah.</span></span></li>
      </ol>
    </div>
  </section>

  <section class="section" aria-labelledby="tanya">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Pertanyaan</p>
        <h2 id="tanya">Yang sering <span class="accent">ditanyakan</span></h2>
      </div>

      <div class="faq rise">
        <details>
          <summary>Apakah situs ini situs resmi sekolah?</summary>
          <div class="a"><p>Bukan. Situs ini purwarupa untuk tugas sekolah. Data pokok diambil dari Data Referensi Kemendikdasmen, bukan dari kantor sekolah.</p></div>
        </details>
        <details>
          <summary>Berapa akreditasinya?</summary>
          <div class="a"><p>Tidak dicantumkan di sini karena sumber publik yang tersedia saling bertentangan. Tanyakan status terbaru ke kantor sekolah.</p></div>
        </details>
        <details>
          <summary>Apakah ada informasi jumlah siswa dan guru?</summary>
          <div class="a"><p>Belum. Angka kepegawaian dan kesiswaan berubah setiap tahun dan hanya sekolah yang bisa memastikan.</p></div>
        </details>
        <details>
          <summary>Kalau ingin mengoreksi data, ke mana?</summary>
          <div class="a"><p>Hubungi kantor sekolah di <a href="tel:+62318538447">(031) 8538447</a> atau <a href="mailto:smp.pgri4waru@gmail.com">smp.pgri4waru@gmail.com</a>.</p></div>
        </details>
      </div>
    </div>
  </section>
@endsection
