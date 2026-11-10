@extends('layouts.app')

@section('title', 'PPDB — SMP PGRI 4 Waru')
@section('description', 'Alur pendaftaran siswa baru SMP PGRI 4 Waru: empat langkah di kantor sekolah, dokumen yang disiapkan, dan formulir pesan.')
@section('og_title', 'Penerimaan siswa baru SMP PGRI 4 Waru')

@section('content')
  <div class="page-head">
    <div class="deco" aria-hidden="true">
      <span class="deco__ring" style="width:560px;left:50%;top:-260px;transform:translateX(-50%)"></span>
      <span class="deco__ring deco__ring--dash" style="width:820px;left:50%;top:-400px;transform:translateX(-50%)"></span>
      <span class="deco__diamond deco__diamond--sm" style="right:18%;top:52%"></span>
      <span class="deco__dots" style="left:7%;bottom:12%"></span>
    </div>
    <div class="wrap">
      <p class="crumbs"><a href="{{ route('beranda') }}">Beranda</a> <span aria-hidden="true">/</span> PPDB</p>
      <h1>Penerimaan siswa baru</h1>
      <p class="lead">Pendaftaran dilakukan di kantor sekolah. Halaman ini menjelaskan alurnya, dokumen yang perlu disiapkan, dan menyusun pesan yang bisa kamu kirim sendiri.</p>
      <div class="hero__actions no-print">
        <a class="btn btn--primary" href="tel:+62318538447">Telepon sekolah <svg class="i i--arrow" aria-hidden="true"><use href="#i-arrow"/></svg></a>
        @if(!empty($siteSettings['brosur_ppdb_path']))
          <a class="btn btn--ghost" href="{{ asset('storage/'.$siteSettings['brosur_ppdb_path']) }}" target="_blank" rel="noopener">Unduh Brosur PPDB <svg class="i" aria-hidden="true"><use href="#i-upright"/></svg></a>
        @endif
        <button class="btn btn--ghost" type="button" data-print>Cetak halaman ini <svg class="i" aria-hidden="true"><use href="#i-print"/></svg></button>
      </div>
      @if(!empty($siteSettings['pengumuman_ppdb']))
        <p class="note" style="margin-top:1.1rem">{{ $siteSettings['pengumuman_ppdb'] }}</p>
      @endif
    </div>
  </div>

  <section class="section" aria-labelledby="alur">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Alur</p>
        <h2 id="alur">Empat langkah <span class="accent">di kantor sekolah</span></h2>
        <span class="rule"></span>
        <p>Tidak ada pendaftaran daring. Situs ini hanya membantu kamu menyiapkan pertanyaan sebelum datang.</p>
      </div>

      <ol class="steps steps--grid rise">
        <li><span class="n">01</span><span><span class="t">Hubungi kantor sekolah</span><span class="d">Telepon (031) 8538447 untuk menanyakan jadwal pendaftaran dan sisa kursi tahun ajaran berjalan.</span></span></li>
        <li><span class="n">02</span><span><span class="t">Ambil dan isi formulir</span><span class="d">Formulir diambil langsung di kantor, lalu diisi dengan data calon siswa dan orang tua.</span></span></li>
        <li><span class="n">03</span><span><span class="t">Serahkan dokumen</span><span class="d">Fotokopi Kartu Keluarga, akta kelahiran, keterangan lulus SD, dan pas foto terbaru.</span></span></li>
        <li><span class="n">04</span><span><span class="t">Tunggu konfirmasi</span><span class="d">Sekolah menghubungi wali siswa untuk menyampaikan hasil dan langkah berikutnya.</span></span></li>
      </ol>
      <p class="note">Jadwal gelombang, biaya, dan kuota tidak ditampilkan karena berubah setiap tahun ajaran. Angka resmi hanya keluar dari kantor sekolah.</p>
    </div>
  </section>

  <section class="section section--tint" aria-labelledby="dokumen">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Dokumen</p>
        <h2 id="dokumen">Berkas yang <span class="accent">disiapkan</span></h2>
        <span class="rule"></span>
        <p>Empat berkas. Semuanya fotokopi, kecuali pas foto.</p>
      </div>

      <div class="doc-check-head rise">
        <div class="doc-check-progress">
          <div class="doc-check-progress__meta">
            <b>Cek kesiapan berkas Anda</b>
            <span id="doc-check-label" aria-live="polite">0 dari 4 berkas siap</span>
          </div>
          <div class="doc-check-bar" aria-hidden="true">
            <span id="doc-check-fill" style="width:0%"></span>
          </div>
        </div>
        <button type="button" class="btn btn--quiet btn--sm" id="doc-check-reset">Atur ulang</button>
      </div>

      <ul class="steps steps--grid doc-checklist rise" id="ppdb-doc-checklist">
        <li class="doc-check-item" data-doc-key="kk">
          <span class="n">01</span>
          <label class="doc-check-label">
            <input type="checkbox" class="doc-check-input" value="kk">
            <span class="doc-check-box" aria-hidden="true"><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></span>
            <span><span class="t">Fotokopi Kartu Keluarga</span><span class="d">Satu lembar, nama calon siswa harus tercantum.</span></span>
          </label>
        </li>
        <li class="doc-check-item" data-doc-key="akta">
          <span class="n">02</span>
          <label class="doc-check-label">
            <input type="checkbox" class="doc-check-input" value="akta">
            <span class="doc-check-box" aria-hidden="true"><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></span>
            <span><span class="t">Fotokopi akta kelahiran</span><span class="d">Untuk memastikan usia dan nama lengkap.</span></span>
          </label>
        </li>
        <li class="doc-check-item" data-doc-key="skl">
          <span class="n">03</span>
          <label class="doc-check-label">
            <input type="checkbox" class="doc-check-input" value="skl">
            <span class="doc-check-box" aria-hidden="true"><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></span>
            <span><span class="t">Keterangan lulus SD</span><span class="d">Atau fotokopi ijazah jenjang SD sederajat.</span></span>
          </label>
        </li>
        <li class="doc-check-item" data-doc-key="foto">
          <span class="n">04</span>
          <label class="doc-check-label">
            <input type="checkbox" class="doc-check-input" value="foto">
            <span class="doc-check-box" aria-hidden="true"><svg class="i" aria-hidden="true"><use href="#i-check"/></svg></span>
            <span><span class="t">Pas foto terbaru</span><span class="d">Ukuran dan jumlah mengikuti permintaan petugas.</span></span>
          </label>
        </li>
      </ul>
      <p class="note">Daftar ini ketentuan umum jenjang SMP. Syarat resmi mengikuti pengumuman sekolah pada tahun ajaran berjalan.</p>
    </div>
  </section>

  <section class="section" id="formulir" aria-labelledby="judul-formulir">
    <div class="wrap">
      <div class="row">
        <div class="row__body rise">
          <p class="eyebrow">Formulir pesan</p>
          <h2 id="judul-formulir" style="margin-top:.75rem;font-size:var(--f-h2);letter-spacing:-.03em">Susun pesan <span class="accent">pendaftaran</span></h2>
          <p>Tiga langkah. Pesan disusun di perangkatmu, tidak dikirim ke server mana pun.</p>
          <p class="note">Setelah pesan tersusun, kamu bisa membukanya di aplikasi email atau menyalinnya ke WhatsApp.</p>

          <ol class="steps" style="margin-top:1.5rem">
            <li><span class="n">A</span><span><span class="t">Periksa sekali lagi</span><span class="d">Nama dan nomor yang salah membuat kantor harus menghubungi ulang.</span></span></li>
            <li><span class="n">B</span><span><span class="t">Kirim lewat email atau WhatsApp</span><span class="d">Kantor membalas pada hari kerja, bukan di luar jam sekolah.</span></span></li>
            <li><span class="n">C</span><span><span class="t">Bawa salinannya saat datang</span><span class="d">Formulir resmi tetap diisi di kantor bersama petugas.</span></span></li>
          </ol>
        </div>

        <form id="ppdb-form" method="POST" action="{{ route('ppdb.store') }}" class="rise" novalidate>
          @csrf
          <input type="hidden" name="website_url" value="">
          <div class="form-sheet">
            <div class="form-head">
              <span data-step-label>Langkah 1 dari 3</span>
              <span class="form-steps" data-form-bars aria-hidden="true"><span></span><span></span><span></span></span>
            </div>
            <div class="form-body">
              <fieldset class="wizard-step" data-step="0">
                <legend tabindex="-1">Data calon siswa</legend>
                <div class="field">
                  <label for="siswa">Nama lengkap calon siswa</label>
                  <input id="siswa" name="siswa" type="text" autocomplete="off" aria-describedby="siswa-error">
                  <span class="error" id="siswa-error"></span>
                </div>
                <div class="field">
                  <label for="asal">Sekolah dasar asal</label>
                  <input id="asal" name="asal" type="text" autocomplete="off" placeholder="Contoh: SDN Kureksari" aria-describedby="asal-error">
                  <span class="error" id="asal-error"></span>
                </div>
                <div class="form-actions">
                  <button class="btn btn--primary" type="button" data-next>Lanjut</button>
                </div>
              </fieldset>

              <fieldset class="wizard-step" data-step="1" hidden>
                <legend tabindex="-1">Data orang tua atau wali</legend>
                <div class="field">
                  <label for="wali">Nama orang tua atau wali</label>
                  <input id="wali" name="wali" type="text" autocomplete="name" aria-describedby="wali-error">
                  <span class="error" id="wali-error"></span>
                </div>
                <div class="field">
                  <label for="wa">Nomor WhatsApp</label>
                  <input id="wa" name="wa" type="tel" inputmode="tel" autocomplete="tel" placeholder="08xxxxxxxxxx" aria-describedby="wa-hint wa-error">
                  <span class="hint" id="wa-hint">Angka saja, 9 sampai 15 digit. Dipakai sekolah untuk membalas.</span>
                  <span class="error" id="wa-error"></span>
                </div>
                <div class="field">
                  <label for="catatan">Pertanyaan (boleh dikosongkan)</label>
                  <textarea id="catatan" name="catatan" rows="3" placeholder="Contoh: apakah masih ada kursi untuk kelas VII?"></textarea>
                </div>
                <div class="form-actions">
                  <button class="btn btn--quiet" type="button" data-back>Kembali</button>
                  <button class="btn btn--primary" type="button" data-next>Lanjut</button>
                </div>
              </fieldset>

              <fieldset class="wizard-step" data-step="2" hidden>
                <legend tabindex="-1">Tinjau dan susun pesan</legend>
                <dl class="form-review" data-review></dl>
                <div class="form-actions">
                  <button class="btn btn--quiet" type="button" data-back>Kembali</button>
                  <button class="btn btn--primary" type="submit" data-submit>Susun pesan</button>
                </div>
              </fieldset>

              <div class="form-status" id="form-status" role="region" aria-live="polite" tabindex="-1" hidden>
                <h3>Pesan siap dikirim</h3>
                <p>Periksa sekali lagi, lalu kirim lewat email atau salin ke WhatsApp. Sekolah membalas pada hari kerja.</p>
                <pre id="form-summary"></pre>
                <div class="form-actions">
                  <a class="btn btn--primary" id="form-mail" href="mailto:smp.pgri4waru@gmail.com">Buka aplikasi email</a>
                  <button class="btn btn--quiet" type="button" id="copy-btn">Salin pesan</button>
                  <button class="btn btn--quiet" type="button" data-edit>Ubah data</button>
                </div>
                <p class="hint" id="copy-note"></p>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>

  <section class="section section--tint" aria-labelledby="tanya">
    <div class="wrap">
      <div class="sec-head rise">
        <p class="eyebrow">Pertanyaan</p>
        <h2 id="tanya">Sebelum <span class="accent">menghubungi sekolah</span></h2>
      </div>

      <div class="faq rise">
        <details>
          <summary>Berapa biaya pendaftaran dan SPP?</summary>
          <div class="a"><p>Biaya tidak dicantumkan di situs ini karena berbeda tiap tahun ajaran. Tanyakan rinciannya langsung ke kantor sekolah.</p></div>
        </details>
        <details>
          <summary>Kapan pendaftaran dibuka?</summary>
          <div class="a"><p>Jadwal gelombang ditentukan sekolah setiap tahun. Telepon <a href="tel:+62318538447">(031) 8538447</a> untuk jadwal yang berlaku sekarang.</p></div>
        </details>
        <details>
          <summary>Apakah menerima siswa pindahan?</summary>
          <div class="a"><p>Kebijakan pindahan bergantung pada ketersediaan kursi dan kelengkapan dokumen. Sampaikan langsung ke kantor sekolah.</p></div>
        </details>
        <details>
          <summary>Apakah pesan dari halaman ini langsung mendaftarkan siswa?</summary>
          <div class="a"><p>Tidak. Halaman ini hanya menyusun pesan. Pendaftaran sah hanya setelah formulir diserahkan di kantor sekolah.</p></div>
        </details>
      </div>
    </div>
  </section>
@endsection
