# -*- coding: utf-8 -*-
"""Isi buku panduan "Panduan Penggunaan Website SMP PGRI 4 Waru".

Satu dict = satu halaman buku. Kunci:
    ch    nomor bab (0 untuk bagian depan/belakang)
    t     judul halaman
    lede  pengantar satu-dua kalimat
    fig   nama berkas tangkapan layar (tanpa .png) -> out/figs/<fig>.png
    cap   keterangan gambar
    steps daftar langkah (maks 7 per halaman)
    note  (jenis, label, isi); jenis: "warn" | "accent" | None
    html  blok HTML tambahan (tabel, diagram, daftar)
    kind  "cover" | "imprint" | "opener" | "toc" | "back"
"""

PAGES = []


def add(*items):
    PAGES.extend(items)


def OP(n, title, goal, items):
    return {"kind": "opener", "ch": n, "t": title, "goal": goal, "items": items}


def P(ch, t, lede="", fig=None, cap="", steps=(), note=None, html="", cls=""):
    return {"ch": ch, "t": t, "lede": lede, "fig": fig, "cap": cap,
            "steps": list(steps), "note": note, "html": html, "cls": cls}


def RAW(ch, html, cls="", t=None, lede=""):
    return {"ch": ch, "t": t, "html": html, "cls": cls, "lede": lede,
            "fig": None, "cap": "", "steps": [], "note": None}


def TBL(head, rows, cls="tb tb--tight"):
    h = "".join("<th>%s</th>" % c for c in head)
    r = "".join("<tr>" + "".join("<td>%s</td>" % c for c in row) + "</tr>" for row in rows)
    return '<table class="%s"><thead><tr>%s</tr></thead><tbody>%s</tbody></table>' % (cls, h, r)


def UL(items):
    return '<ul class="plain">' + "".join("<li>%s</li>" % i for i in items) + '</ul>'


# ============================================================ DEPAN

add({"kind": "cover", "ch": 0})
add({"kind": "imprint", "ch": 0, "t": "Tentang buku ini"})


add(P(0, "Kata pengantar",
      lede="Buku ini ditulis untuk staf sekolah, bukan untuk pembuat website.",
      html="""
<p class="pull">Mulai tahun ajaran 2026/2027 pendaftaran siswa baru SMP PGRI 4 Waru
dilakukan lewat website. Panitia tidak lagi mencatat pendaftar di buku tulis, tetapi
memeriksa data, berkas, dan pembayaran di satu tempat.</p>

<p>Perubahan itu memindahkan pekerjaan, bukan menghilangkannya. Data yang dulu diperiksa
di meja depan sekarang masuk lewat portal siswa. Berkas yang dulu diterima di dalam map
sekarang berupa unggahan. Karena itu setiap petugas perlu tahu persis di mana menekan dan
apa akibatnya.</p>

<p>Panduan ini disusun langsung dari aplikasi yang benar-benar berjalan, dengan tangkapan
layar apa adanya. Setiap nama menu dan tombol ditulis sama dengan yang terlihat di layar.
Bila suatu saat tampilan berubah, versi buku ini perlu diperbarui.</p>

<p>Semoga buku ini membuat pekerjaan panitia lebih tenang, terutama pada pekan-pekan
sibuk ketika pendaftar datang bersamaan.</p>

<p style="margin-top:9mm"><b>Kepala SMP PGRI 4 Waru</b><br>
<span class="small">Sidoarjo, November 2026</span></p>
"""))


add(RAW(0, """
<h1 class="t-page">Cara memakai panduan ini</h1>
<p class="t-lede">Empat hal yang perlu diketahui sebelum membuka bab pertama.</p>

<div class="cols cols--2" style="gap:9mm">
  <div>
    <h3 class="t-h3">1. Bab mengikuti pekerjaan, bukan menu</h3>
    <p>Satu bab mewakili satu jenis pekerjaan. Bab 3 tentang memeriksa pendaftar, Bab 9
    tentang mengelola akun petugas. Bila Anda hanya perlu menyelesaikan satu tugas, buka
    bab itu saja; urutan bab tidak wajib dibaca dari awal.</p>

    <h3 class="t-h3">2. Nomor di gambar sama dengan nomor langkah</h3>
    <p>Lingkaran biru bernomor di dalam tangkapan layar menunjuk elemen yang disebut pada
    langkah bernomor di bawahnya. Angka 3 di gambar berarti langkah 3.</p>

    <h3 class="t-h3">3. Nama tombol ditulis persis</h3>
    <p>Tulisan seperti <span class="ui">Saring Data</span> berarti tombol dengan tulisan
    itu benar-benar ada di layar. Huruf besar-kecil dan spasinya sama; jangan mencari
    tombol lain yang mirip.</p>
  </div>

  <div>
    <h3 class="t-h3">4. Tiga kotak keterangan</h3>

    <div class="callout callout--accent" style="margin-top:2mm">
      <span class="label">CATATAN</span>
      Keterangan tambahan yang membantu, tetapi tidak wajib diikuti.
    </div>

    <div class="callout callout--warn">
      <span class="label">PERHATIAN</span>
      Tindakan yang berakibat langsung ke situs publik atau ke data siswa. Baca dulu
      sebelum menekan.
    </div>

    <div class="callout">
      <span class="label">BILA GAGAL</span>
      Gejala yang sering muncul dan cara menanganinya. Setiap bab diakhiri kotak seperti
      ini.
    </div>

    <p class="tiny" style="margin-top:7mm">Tiga pintu masuk yang dibahas di buku ini:
    panel admin <span class="mono">/login</span>, portal siswa
    <span class="mono">/ppdb/masuk</span>, dan situs publik
    <span class="mono">smppgri4waru.sch.id</span>. Ketiganya memakai akun berbeda dan
    tidak saling menggantikan.</p>
  </div>
</div>
"""))


add({"kind": "toc", "ch": 0, "t": "Daftar isi"})


# peta sistem
_STAGES = [
    (55, "Jalur"), (131, "Bayar"), (207, "Data diri"),
    (283, "Berkas"), (359, "Kuesioner"), (435, "Kartu"),
]
_STAGE_SVG = "".join(
    '<g><circle cx="%d" cy="128" r="15" fill="#0e5e8f"/>'
    '<text x="%d" y="132" fill="#fff" font-weight="700">%d</text>'
    '<text x="%d" y="162" fill="#3f3f46" font-size="8">%s</text>%s</g>' % (
        x, x, i, x, lbl,
        ('<line x1="%d" y1="128" x2="%d" y2="128" stroke="#c7c7cd" stroke-width="1.2"/>'
         % (x + 15, x + 78 - 15)) if i < 6 else "",
    )
    for i, (x, lbl) in enumerate(_STAGES, start=1)
)

add(RAW(0, ("""
<h1 class="t-page">Peta sistem</h1>
<p class="t-lede">Satu website, tiga pintu masuk, dan enam tahap pendaftaran yang
berurutan.</p>

<figure class="diagram">
<svg viewBox="0 0 520 250" width="100%" role="img" aria-label="Diagram tiga pintu masuk dan enam tahap PPDB">
  <g fill="none" stroke="#c7c7cd" stroke-width="1">
    <rect x="1" y="1" width="164" height="72" rx="6"/>
    <rect x="178" y="1" width="164" height="72" rx="6"/>
    <rect x="355" y="1" width="164" height="72" rx="6"/>
  </g>
  <rect x="178" y="1" width="164" height="72" rx="6" fill="rgba(18,110,166,0.06)" stroke="#0e5e8f" stroke-width="1.4"/>
  <g font-family="Plus Jakarta Sans, sans-serif">
    <text x="12" y="22" font-size="11" font-weight="700" fill="#18181b">Situs publik</text>
    <text x="12" y="38" font-size="9" fill="#6b6b74">Tanpa login</text>
    <text x="12" y="58" font-size="9" fill="#3f3f46">/  /profil  /berita</text>

    <text x="189" y="22" font-size="11" font-weight="700" fill="#0e5e8f">Panel admin</text>
    <text x="189" y="38" font-size="9" fill="#6b6b74">Admin &amp; operator</text>
    <text x="189" y="58" font-size="9" fill="#3f3f46">/login</text>

    <text x="366" y="22" font-size="11" font-weight="700" fill="#18181b">Portal siswa</text>
    <text x="366" y="38" font-size="9" fill="#6b6b74">Calon siswa / orang tua</text>
    <text x="366" y="58" font-size="9" fill="#3f3f46">/ppdb/masuk</text>
  </g>

  <line x1="260" y1="73" x2="260" y2="104" stroke="#0e5e8f" stroke-width="1.4"/>
  <text x="266" y="98" font-size="9" fill="#6b6b74" font-family="Plus Jakarta Sans, sans-serif">enam tahap berurutan</text>

  <g font-family="Plus Jakarta Sans, sans-serif" font-size="9" text-anchor="middle">
    """ + _STAGE_SVG + """
  </g>
</svg>
<figcaption>Pintu masuk (atas) dan enam tahap pendaftaran yang harus dilalui siswa secara
berurutan (bawah). Tahap berikutnya terkunci sampai tahap sebelumnya selesai.</figcaption>
</figure>

<p class="small" style="margin-top:4mm">Di panel admin, panitia memeriksa hasil keenam
tahap itu: memvalidasi berkas, memverifikasi pembayaran, menetapkan status seleksi, lalu
memublikasikan hasilnya. Umpan balik langsung terlihat di portal siswa pada menu
<span class="ui">Pengumuman</span>.</p>
""")))


# ============================================================ BAB 1

add(OP(1, "Masuk &amp; keluar",
       "Membuka panel admin dengan aman, keluar bila selesai, dan mengenali pesan galat "
       "saat kata sandi salah atau akun terkunci.",
       ["Masuk ke panel admin", "Lupa kata sandi petugas", "Keluar sesi",
        "Masuk ke portal siswa"]))

add(P(1, "Masuk ke panel admin",
      lede="Panel admin dipakai petugas sekolah untuk mengurus seluruh isi website.",
      fig="adm-login",
      cap="Halaman <span class='mono'>/login</span>. Isian yang tersedia hanya "
          "<b>Alamat email</b> dan <b>Kata sandi</b>.",
      steps=[
          "Buka alamat <span class='mono'>/login</span> pada peramban.",
          "Isi <span class='ui'>Alamat email</span> dengan email petugas yang terdaftar.",
          "Isi <span class='ui'>Kata sandi</span>.",
          "Klik tombol <span class='ui'>Masuk ke panel</span>.",
          "Bila berhasil, halaman <span class='ui'>Ringkasan</span> terbuka dan nama Anda "
          "muncul dengan label peran di kanan atas.",
      ],
      note=("accent", "CATATAN",
            "Alamat email login petugas berakhiran <span class='mono'>@smppgri4waru.id</span>. "
            "Ini berbeda dari akun pendaftar, yang memakai email pribadi atau nomor NIK.")))

add(P(1, "Lupa kata sandi &amp; keluar sesi",
      lede="Bila kata sandi terlupa, minta tautan pengaturan ulang. Bila pekerjaan selesai, "
          "keluar agar sesi tidak tertinggal di komputer bersama.",
      fig="adm-lupa-sandi",
      cap="Halaman permintaan tautan pengaturan kata sandi.",
      steps=[
          "Pada halaman <span class='mono'>/login</span>, klik "
          "<span class='ui'>Minta tautan pengaturan kata sandi</span>.",
          "Isi <span class='ui'>Alamat email</span>, lalu kirim permintaan.",
          "Buka kotak masuk email petugas dan klik tautan yang dikirim.",
          "Isi kata sandi baru (8–16 karakter, huruf besar, huruf kecil, angka).",
          "Untuk keluar: klik <span class='ui'>Keluar sesi</span> di bagian bawah menu kiri.",
      ],
      note=("warn", "PERHATIAN",
            "Tautan pengaturan kata sandi hanya berlaku sekali pakai dan kedaluwarsa. "
            "Bila email tidak masuk, hubungi Administrator Penuh karena pengiriman email "
            "bergantung pada penyetelan SMTP sekolah.")))

add(P(1, "Masuk ke portal siswa",
      lede="Portal siswa dipakai calon siswa dan orang tua untuk mendaftar dan memantau "
          "hasil seleksi.",
      fig="pub-masuk",
      cap="Halaman <span class='mono'>/ppdb/masuk</span> dengan isian "
          "<b>Email atau 16 Digit NIK</b> dan <b>Kata Sandi</b>.",
      steps=[
          "Buka <span class='mono'>/ppdb/masuk</span>.",
          "Isi <span class='ui'>Email atau 16 Digit NIK</span> — boleh memakai email akun "
          "atau nomor NIK tanpa spasi.",
          "Isi <span class='ui'>Kata Sandi</span>.",
          "Klik tombol masuk, lalu halaman <span class='ui'>Beranda</span> portal terbuka.",
      ],
      note=("accent", "CATATAN",
            "Akun portal siswa dibuat sendiri oleh pendaftar lewat <span class='mono'>/ppdb/daftar</span>. "
            "Panitia tidak membuatkan akun ini; yang bisa dilakukan panitia adalah mereset "
            "kata sandinya (lihat Bab 3).")))


# ============================================================ BAB 2

add(OP(2, "Ringkasan operasional",
       "Membaca keadaan pendaftaran dalam sekali lihat: berapa pendaftar, berapa yang "
       "sudah terverifikasi, dan mana yang perlu ditindaklanjuti hari ini.",
       ["Empat kartu angka", "Akses cepat modul", "Distribusi status pendaftar"]))

add(P(2, "Empat kartu angka",
      lede="Halaman <span class='mono'>/admin</span> membuka dengan empat angka utama.",
      fig="adm-ringkasan",
      cap="Halaman <b>Ringkasan</b> setelah masuk sebagai admin.",
      steps=[
          "Baca kartu <span class='ui'>Total Pendaftar</span>: jumlah seluruh pendaftar "
          "yang masuk lewat portal.",
          "Baca kartu <span class='ui'>Diterima &amp; Terverifikasi</span>: pendaftar yang "
          "sudah tuntas berkas dan pembayarannya.",
          "Baca kartu <span class='ui'>Perlu Tindak Lanjut</span>: pendaftar yang menunggu "
          "keputusan panitia.",
          "Baca kartu <span class='ui'>PUBLIKASI BERITA</span>: jumlah berita yang tayang.",
          "Bila angka <i>Perlu Tindak Lanjut</i> naik, buka Bab 3 dan periksa daftarnya.",
      ],
      note=("accent", "CATATAN",
            "Angka pada kartu hanya ringkasan. Selalu periksa daftar lengkapnya sebelum "
            "mengambil keputusan, karena satu pendaftar bisa muncul di lebih dari satu "
            "kelompok.")))

add(P(2, "Akses cepat &amp; distribusi status",
      lede="Di bawah kartu angka tersedia pintasan ke modul yang sering dipakai dan grafik "
          "sebaran status pendaftar.",
      fig="adm-ringkasan-distribusi",
      cap="Blok <b>Distribusi Status Pendaftar PPDB</b> dan pintasan modul.",
      steps=[
          "Klik salah satu kartu pintasan untuk langsung membuka modulnya.",
          "Pintasan <span class='ui'>Ekspor CSV PPDB</span> mengunduh data pendaftar.",
          "Pintasan <span class='ui'>Lihat Situs Publik</span> membuka website dalam tab baru.",
          "Baca grafik <span class='ui'>Distribusi Status Pendaftar PPDB</span> untuk "
          "mengetahui sebaran status Baru / Dihubungi / Diterima / Ditolak.",
      ]))


# ============================================================ BAB 3

add(OP(3, "Pendaftar PPDB online",
       "Memeriksa satu pendaftar sampai tuntas: berkas, pembayaran, data diri, kuesioner, "
       "lalu menetapkan keputusan seleksi yang langsung terlihat siswa.",
       ["Melihat &amp; menyaring daftar", "Membuka detail pendaftar",
        "Menetapkan status seleksi", "Memeriksa &amp; memvalidasi berkas",
        "Memverifikasi pembayaran", "Mengatur jalur &amp; gelombang",
        "Mereset kata sandi siswa", "Mengekspor data ke CSV"]))

add(P(3, "Melihat &amp; menyaring daftar pendaftar",
      lede="Semua pendaftar dari portal terkumpul di satu tabel di "
          "<span class='mono'>/admin/ppdb</span>.",
      fig="adm-ppdb-list",
      cap="Tab <b>Pendaftar Portal Online</b> menampilkan tabel "
          "<b>Daftar Calon Siswa (Portal PPDB Online)</b>.",
      steps=[
          "Buka menu <span class='ui'>Pendaftar PPDB</span> di bilah kiri.",
          "Ketik nama, NIK, nomor pendaftaran, atau SD asal pada kotak "
          "<span class='ui'>Cari nomor pendaftaran, NIK, nama siswa, atau SD asal</span>.",
          "Pilih jalur pada daftar <span class='ui'>Filter jalur</span> (misalnya "
          "<i>Semua Jalur</i>).",
          "Pilih status pada <span class='ui'>Filter status seleksi</span>.",
          "Klik <span class='ui'>Saring Data</span> untuk menerapkan penyaringan.",
          "Klik <span class='ui'>Periksa</span> pada baris yang ingin dibuka.",
      ],
      note=("accent", "CATATAN",
            "Bila penyaringan tidak lagi diperlukan, klik <span class='ui'>Atur Ulang</span> "
            "untuk menampilkan seluruh daftar kembali.")))

add(P(3, "Membuka detail satu pendaftar",
      lede="Klik <span class='ui'>Periksa</span> pada sebuah baris untuk membuka seluruh "
          "data pendaftar tersebut dalam satu halaman.",
      fig="adm-detail-bayar-biodata",
      cap="Halaman detail pendaftar. Panel dikelompokkan berurutan dari keputusan seleksi "
          "sampai keamanan akun.",
      steps=[
          "Panel <b>1. Keputusan Seleksi &amp; Catatan Panitia</b>: menetapkan hasil seleksi.",
          "Panel <b>2. Verifikasi Pembayaran Formulir</b>: status bayar dan catatan TU.",
          "Panel <b>3. Pemeriksaan Berkas Persyaratan</b>: validasi tiap berkas.",
          "Panel <b>4. Biodata Lengkap Calon Siswa &amp; Orang Tua</b>: data diri.",
          "Panel <b>5. Jawaban Kuesioner Siswa</b>: jawaban saat mendaftar.",
          "Panel <b>6. Keamanan Akun Portal Siswa</b>: reset kata sandi siswa.",
      ]))

add(P(3, "Menetapkan status seleksi",
      lede="Keputusan panitia ditetapkan di panel pertama. Status ini langsung terlihat "
          "siswa pada menu <span class='ui'>Pengumuman</span>.",
      fig="adm-detail-diterima-seleksi",
      cap="Panel <b>1. Keputusan Seleksi &amp; Catatan Panitia</b>.",
      steps=[
          "Pilih status pada daftar <span class='ui'>Status Seleksi Calon Siswa</span>: "
          "<i>Dalam Proses</i>, <i>Terverifikasi</i>, <i>Diterima</i>, atau <i>Ditolak</i>.",
          "Tulis keterangan pada <span class='ui'>Catatan Resmi Panitia (Ditampilkan di "
          "Pengumuman Siswa)</span> bila perlu.",
          "Klik <span class='ui'>Simpan Status Seleksi</span>.",
          "Periksa kotak <b>Bila gagal</b> di bawah untuk arti tiap status.",
      ],
      note=("warn", "PERHATIAN",
            "Status <i>Diterima</i> dan <i>Ditolak</i> tampil sebagai pengumuman resmi ke "
            "siswa. Pastikan berkas dan pembayaran sudah benar sebelum menetapkannya.")))

add(RAW(3, """
<h1 class="t-page">Arti tiap status seleksi</h1>
<p class="t-lede">Apa yang dilihat siswa di portal untuk setiap status yang Anda simpan.</p>
""" + TBL(
    ["Status", "Yang terlihat siswa", "Kapan dipakai"],
    [["Dalam Proses", "Pendaftaran Anda sedang dalam proses verifikasi.",
      "Berkas atau pembayaran belum lengkap."],
     ["Terverifikasi", "Berkas &amp; pendaftaran Anda telah terverifikasi.",
      "Berkas lengkap dan pembayaran sah."],
     ["Diterima", "\"Selamat! Anda dinyatakan DITERIMA.\"",
      "Lulus seleksi dan siap daftar ulang."],
     ["Ditolak", "\"Mohon maaf, pendaftaran belum dapat diterima.\"",
      "Tidak memenuhi syarat atau kuota penuh."]]
) + """
<div class="callout callout--accent">
  <span class="label">CATATAN</span>
  Bila hasil seleksi dibatalkan, ubah kembali ke <i>Dalam Proses</i>. Pengumuman lama
  langsung tergantikan.
</div>

<div class="callout">
  <span class="label">BILA GAGAL</span>
  Tombol <span class="ui">Simpan Status Seleksi</span> tidak mengubah apa pun bila
  halaman detail dibuka oleh akun Operator lalu sesi sudah kedaluwarsa — muat ulang
  halaman dan masuk kembali.
</div>
"""))


add(P(3, "Memeriksa &amp; memvalidasi berkas",
      lede="Pendaftar wajib mengunggah empat berkas. Panitia memeriksa setiap berkas dan "
          "menandainya sesuai atau perlu revisi.",
      fig="adm-detail-bayar-berkas",
      cap="Panel <b>3. Pemeriksaan Berkas Persyaratan</b>. Empat berkas wajib: Kartu "
          "Keluarga, Akta Kelahiran, SKL/Rapor, dan Pas Foto.",
      steps=[
          "Buka panel <b>3. Pemeriksaan Berkas Persyaratan</b>.",
          "Klik <span class='ui'>Buka / Unduh Berkas</span> untuk melihat isi berkas.",
          "Tentukan status tiap berkas: <i>Menunggu</i>, <i>Valid / Sesuai</i>, atau "
          "<i>Perlu Revisi</i>.",
          "Tulis catatan bila menolak, agar siswa tahu apa yang harus diperbaiki.",
          "Klik <span class='ui'>Simpan</span> pada bagian bawah panel.",
      ],
      note=("warn", "PERHATIAN",
            "Berkas bertanda <i>Perlu Revisi</i> membuka kembali tahap berkas di portal "
            "siswa, sehingga tahap kuesioner dan kartu peserta ikut terkunci sampai "
            "revisi diterima.")))

add(P(3, "Memverifikasi pembayaran",
      lede="Biaya pendaftaran sebagian jalur gratis, sebagian berbayar. Panitia memastikan "
          "pembayaran benar-benar masuk.",
      fig="adm-detail-bayar-bayar",
      cap="Panel <b>2. Verifikasi Pembayaran Formulir</b> dengan status, catatan, dan "
          "tombol perbarui.",
      steps=[
          "Buka panel <b>2. Verifikasi Pembayaran Formulir</b>.",
          "Klik <span class='ui'>Buka Bukti Transfer</span> untuk memeriksa bukti unggahan "
          "siswa.",
          "Tentukan <span class='ui'>Status Pembayaran</span>: <i>Menunggu</i>, "
          "<i>Lunas</i>, <i>Ditolak</i>, atau <i>Gratis</i>.",
          "Isi <span class='ui'>Catatan Petugas TU</span> bila perlu.",
          "Klik <span class='ui'>Perbarui Status Pembayaran</span>.",
      ],
      note=("accent", "CATATAN",
            "Jalur dengan biaya Rp 0 otomatis bertanda <i>Gratis</i>. Siswa pada jalur ini "
            "tidak perlu mengunggah bukti transfer.")))

add(P(3, "Mengatur jalur &amp; gelombang",
      lede="Jalur pendaftaran menentukan biaya, kuota, dan tenggat yang dipilih siswa di "
          "portal.",
      fig="adm-ppdb-jalur",
      cap="Tab <b>Jalur &amp; Gelombang PPDB</b>.",
      steps=[
          "Buka tab <span class='ui'>Jalur &amp; Gelombang PPDB</span> di "
          "<span class='mono'>/admin/ppdb</span>.",
          "Klik <span class='ui'>Tambah Jalur Baru</span> untuk membuat jalur, atau "
          "<span class='ui'>Sunting</span> untuk mengubahnya.",
          "Isi <span class='ui'>Nama Jalur / Gelombang</span>, <span class='ui'>Biaya "
          "Pendaftaran (Rp, isi 0 jika Gratis)</span>, dan <span class='ui'>Kuota Kursi</span>.",
          "Atur <span class='ui'>Tanggal Mulai</span>, <span class='ui'>Tanggal Selesai</span>, "
          "dan <span class='ui'>Status Pembukaan Jalur</span>.",
          "Klik <span class='ui'>Simpan</span>; jalur langsung muncul di portal siswa.",
      ],
      note=("warn", "PERHATIAN",
            "Menghapus atau menutup jalur membuat pilihan itu hilang dari portal. Siswa "
            "yang sudah memilih jalur tersebut tetap menyimpan pilihannya, jadi jangan hapus "
            "jalur yang sedang dipakai.")))

add(P(3, "Mereset kata sandi siswa &amp; membaca pesan",
      lede="Panitia dapat menolong siswa yang lupa kata sandi, dan membaca pesan cepat dari "
          "halaman publik.",
      fig="adm-detail-diterima-biodata",
      cap="Panel <b>6. Keamanan Akun Portal Siswa</b> untuk mengganti kata sandi siswa.",
      steps=[
          "Buka detail pendaftar, turun ke panel <b>6. Keamanan Akun Portal Siswa</b>.",
          "Isi <span class='ui'>Kata sandi baru</span> dan <span class='ui'>Ulangi kata "
          "sandi baru</span>.",
          "Klik <span class='ui'>Simpan Sandi untuk Akun Ini</span>.",
          "Untuk pesan dari publik, buka tab <span class='ui'>Pesan Cepat / Konsultasi</span> "
          "setelah pesan yang masuk ditindaklanjuti.",
      ],
      note=("accent", "CATATAN",
            "Sampaikan kata sandi baru kepada siswa lewat saluran resmi, dan minta siswa "
            "menggantinya sendiri lewat <span class='ui'>Akun &amp; Kata Sandi</span> di portal.")))


add(P(3, "Mengekspor data pendaftar ke CSV",
      lede="Untuk laporan ke yayasan atau pengolahan di Excel, unduh data sebagai berkas "
          "CSV.",
      fig="adm-ppdb-pesan",
      cap="Tab <b>Pesan Cepat / Konsultasi</b>; tombol ekspor berada pada tab pendaftar.",
      steps=[
          "Buka tab <span class='ui'>Pendaftar Portal Online</span>.",
          "Klik <span class='ui'>Unduh CSV Online</span> untuk mengunduh seluruh pendaftar "
          "portal.",
          "Simpan berkas, lalu buka memakai Excel atau Google Sheets.",
          "Pastikan kolom NIK dan nomor WhatsApp diformat sebagai teks agar nol di depan "
          "tidak hilang.",
      ],
      note=("warn", "PERHATIAN",
            "Berkas CSV memuat data pribadi siswa. Simpan di komputer sekolah dan jangan "
            "dibagikan lewat pesan pribadi.")))


# ============================================================ BAB 4

add(OP(4, "Berita &amp; pengumuman",
       "Menulis, memformat, dan menerbitkan berita sekolah lengkap dengan gambar sampul, "
       "lalu mengelolanya dari daftar artikel.",
       ["Menulis berita", "Mode Visual, HTML &amp; Pratinjau", "Setelan entri &amp; sampul",
        "Mengelola daftar artikel"]))

add(P(4, "Menulis berita baru",
      lede="Semua berita dan pengumuman ditulis di editor di menu "
          "<span class='ui'>Berita &amp; Pengumuman</span>.",
      fig="adm-berita-editor",
      cap="Editor isi berita dengan mode penyuntingan di atas kanvas.",
      steps=[
          "Buka menu <span class='ui'>Berita &amp; Pengumuman</span>.",
          "Klik tombol untuk menulis entri baru.",
          "Isi <span class='ui'>Judul berita atau pengumuman</span> (maksimal 70 karakter).",
          "Tulis isi pada <span class='ui'>Isi lengkap pengumuman (HTML / Teks)</span>.",
          "Pilih <span class='ui'>Status penayangan</span>: <i>Terbit langsung di situs "
          "publik</i> atau <i>Simpan sebagai draf</i>.",
          "Klik simpan; berita berstatus terbit langsung muncul di <span class='mono'>/berita</span>.",
      ],
      note=("accent", "CATATAN",
            "Judul yang terlalu panjang terpotong pada kartu berita di beranda. Jaga "
            "ringkas: satu gagasan utama per berita.")))

add(P(4, "Mode Visual, HTML &amp; Pratinjau",
      lede="Isi berita bisa disunting langsung seperti dokumen, lewat kode HTML, atau "
          "diperiksa lebih dulu dalam mode pratinjau.",
      fig="adm-berita-html",
      cap="Penyuntingan isi: pilihan format, tautan, dan sisipan pada bilah editor.",
      steps=[
          "Mode <b>Visual</b>: menyunting langsung dengan bilah format.",
          "Pilih format di daftar <span class='ui'>Format baris atau judul</span>: "
          "<i>Paragraf Normal</i>, <i>Judul Utama (H2)</i>, <i>Subjudul Bagian (H3)</i>, "
          "atau <i>Kutipan Sorotan</i>.",
          "Klik <span class='ui'>Tambahkan Tautan</span> lalu <span class='ui'>Masukkan ke "
          "Artikel</span> untuk menyisipkan tautan.",
          "Mode <b>HTML</b>: menyunting kode bila perlu susunan rumit.",
          "Mode <b>Pratinjau</b>: melihat hasil akhir sebelum menerbitkan.",
      ],
      note=("warn", "PERHATIAN",
            "Setelah menempel dari dokumen lain (Word/Google Docs), periksa ulang hasil di "
            "mode Pratinjau. Tempelan sering membawa format asing yang merusak tampilan.")))

add(P(4, "Setelan entri: status, sampul &amp; ringkasan",
      lede="Panel Setelan Entri mengatur status tayang, gambar sampul, alamat halaman, dan "
          "ringkasan untuk kartu serta mesin pencari.",
      fig="adm-berita-sampul",
      cap="Panel <b>Setelan Entri</b> pada sisi kanan editor.",
      steps=[
          "Pilih <span class='ui'>Status penayangan</span>.",
          "Unggah gambar pada <span class='ui'>Unggah berkas sampul (JPG/PNG/WebP)</span>, "
          "atau pilih dari galeri sekolah.",
          "Isi <span class='ui'>Slug URL khusus (opsional)</span> bila ingin alamat pendek.",
          "Isi <span class='ui'>Ringkasan kartu &amp; SEO (maks 300 karakter)</span>.",
          "Periksa pratinjau, lalu simpan.",
      ],
      note=("accent", "CATATAN",
            "Sampul sebaiknya berukuran mendatar (rasio 16:9) agar seragam dengan kartu "
            "berita lainnya. Ringkasan 1–2 kalimat sudah cukup.")))

add(P(4, "Mengelola daftar artikel",
      lede="Semua entri terkumpul di daftar artikel dengan pencarian dan filter status.",
      fig="adm-berita-daftar",
      cap="Daftar <b>Artikel &amp; Pengumuman</b> dengan penyaring status.",
      steps=[
          "Buka daftar <span class='ui'>Artikel &amp; Pengumuman</span>.",
          "Cari lewat kotak <span class='ui'>Cari judul atau isi berita</span>.",
          "Saring dengan <span class='ui'>Filter status berita</span>: <i>Semua status</i>, "
          "<i>Status: Terbit</i>, atau <i>Status: Draf</i>.",
          "Klik <span class='ui'>Buka di Studio</span> untuk menyunting entri.",
          "Klik <span class='ui'>Jadikan Draf</span> untuk menarik berita dari situs publik.",
          "Klik <span class='ui'>Hapus</span> untuk menghapus permanen.",
      ],
      note=("warn", "PERHATIAN",
            "Menghapus entri tidak bisa dibatalkan. Bila berita hanya perlu disembunyikan "
            "sementara, ubah ke <i>Draf</i>, jangan hapus.")))


# ============================================================ BAB 5

add(OP(5, "Galeri foto",
       "Mengunggah dan menata dokumentasi kegiatan sekolah yang tampil di halaman publik.",
       ["Unggah foto", "Judul &amp; kategori", "Menghapus foto"]))

add(P(5, "Mengunggah &amp; menata foto galeri",
      lede="Galeri menampung foto kegiatan yang tampil di beranda dan halaman berita.",
      fig="adm-galeri",
      cap="Halaman <b>Galeri Foto</b> dengan tabel foto terpasang dan formulir unggah.",
      steps=[
          "Buka menu <span class='ui'>Galeri Foto</span>.",
          "Pada <span class='ui'>Tambah Foto Dokumentasi</span>, isi "
          "<span class='ui'>Judul Foto / Kegiatan</span>.",
          "Pilih <span class='ui'>Kategori Dokumentasi</span>, misalnya "
          "<i>Kegiatan Sekolah</i> atau <i>Fasilitas &amp; Lingkungan</i>.",
          "Isi <span class='ui'>Keterangan Singkat</span> sebagai teks alternatif gambar.",
          "Pilih berkas pada <span class='ui'>Berkas Foto (JPG, PNG, WEBP maks 4MB)</span>.",
          "Klik <span class='ui'>Unggah ke Galeri Publik</span>.",
      ],
      note=("warn", "PERHATIAN",
            "Gunakan foto milik sekolah atau yang sudah diizinkan. Jangan mengunggah foto "
            "siswa tanpa persetujuan orang tua.")))


# ============================================================ BAB 6

add(OP(6, "Ulasan alumni",
       "Menilai ulasan yang dikirim alumni: mana yang layak tampil, mana yang perlu "
       "disembunyikan atau dihapus.",
       ["Membaca ulasan", "Menambah ulasan", "Moderasi &amp; menghapus"]))

add(P(6, "Memoderasi ulasan alumni",
      lede="Ulasan alumni tampil di halaman publik setelah melewati penilaian panitia.",
      fig="adm-alumni",
      cap="Halaman <b>Daftar Ulasan Alumni</b> dengan kolom status dan tindakan.",
      steps=[
          "Buka menu <span class='ui'>Ulasan Alumni</span>.",
          "Baca tiap ulasan pada kolom <span class='ui'>Isi Ulasan</span>.",
          "Tambahkan ulasan langsung lewat <span class='ui'>Tambah Ulasan Alumni</span> "
          "(nama, angkatan, sekolah lanjutan, dan isi ulasan).",
          "Ubah <span class='ui'>Status</span> menjadi tampil atau sembunyi.",
          "Klik <span class='ui'>Hapus</span> untuk ulasan yang melanggar.",
      ],
      note=("warn", "PERHATIAN",
            "Tolak ulasan yang memuat nomor telepon, alamat rumah, atau ujaran kebencian. "
            "Fokuskan pada kesan belajar di sekolah, bukan data pribadi.")))


# ============================================================ BAB 7

add(OP(7, "Kurikulum &amp; profil",
       "Memperbarui daftar mata pelajaran, jam belajar, dan data pokok sekolah yang muncul "
       "di halaman akademik dan profil.",
       ["Mata pelajaran &amp; jam", "Data pokok sekolah"]))

add(P(7, "Mengubah mata pelajaran &amp; data akademik",
      lede="Halaman <span class='mono'>/akademik</span> mengambil isinya dari menu "
          "<span class='ui'>Kurikulum &amp; Profil</span>.",
      fig="adm-akademik",
      cap="Halaman <b>Kurikulum &amp; Profil</b>.",
      steps=[
          "Buka menu <span class='ui'>Kurikulum &amp; Profil</span>.",
          "Perbarui daftar mata pelajaran wajib dan muatan lokal.",
          "Sesuaikan jam pelajaran tiap tingkat bila ada perubahan.",
          "Simpan; halaman <span class='mono'>/akademik</span> dan "
          "<span class='mono'>/profil</span> ikut berubah.",
      ],
      note=("accent", "CATATAN",
            "Perubahan di sini langsung terlihat publik. Lakukan di luar jam layanan agar "
            "tidak ada pengunjung yang melihat halaman setengah jadi.")))


# ============================================================ BAB 8

add(OP(8, "Pengaturan situs",
       "Mengatur identitas, logo, foto beranda, dan teks halaman. Semua isian di bab ini "
       "langsung tampil di situs publik.",
       ["Identitas sekolah", "Logo &amp; media", "Kontak &amp; alamat", "Teks beranda"]))

add(P(8, "Identitas sekolah &amp; media",
      lede="Bagian ini menyimpan nama resmi, NPSN, logo, dan gambar beranda.",
      fig="adm-pengaturan",
      cap="Halaman <b>Pengaturan Situs</b>, bagian identitas dan media.",
      steps=[
          "Buka menu <span class='ui'>Pengaturan Situs</span>.",
          "Pada bagian <b>1. Unggah Media, Logo &amp; Dokumen Situs</b>, pilih "
          "<span class='ui'>Pilih berkas logo baru</span>.",
          "Pilih <span class='ui'>Pilih gambar beranda baru</span> dan, bila ada, "
          "<span class='ui'>Pilih berkas brosur PPDB</span>.",
          "Perbarui <span class='ui'>Nama resmi sekolah</span>, "
          "<span class='ui'>Nomor Pokok Sekolah Nasional (NPSN)</span>, dan "
          "<span class='ui'>Waktu kegiatan belajar</span>.",
          "Simpan pengaturan.",
      ],
      note=("warn", "PERHATIAN",
            "Perubahan pada halaman ini langsung terlihat publik. Ganti satu bagian saja, "
            "periksa hasilnya di situs, baru lanjut ke bagian berikutnya.")))

add(P(8, "Kontak, alamat &amp; teks beranda",
      lede="Nomor telepon, email, alamat, peta, dan teks sambutan beranda diatur di sini.",
      fig="adm-pengaturan",
      cap="Bagian kontak, alamat, dan kustomisasi teks beranda.",
      steps=[
          "Isi <span class='ui'>Nomor telepon kantor</span> dan "
          "<span class='ui'>Alamat email resmi</span>.",
          "Lengkapi <span class='ui'>Alamat lengkap jalan &amp; desa</span> dan "
          "<span class='ui'>Kode pos</span>.",
          "Isi <span class='ui'>Koordinat peta (Lintang, Bujur)</span> bila peta perlu presisi.",
          "Perbarui <span class='ui'>Jam layanan tata usaha</span>.",
          "Sesuaikan <span class='ui'>Judul utama halaman Beranda (Hero Title)</span> dan "
          "<span class='ui'>Rumusan Visi Sekolah</span>.",
          "Simpan, lalu periksa hasilnya di halaman <span class='mono'>/</span>.",
      ],
      note=("accent", "CATATAN",
            "Format koordinat peta memakai titik desimal, misalnya "
            "<span class='mono'>-7.3521, 112.7170</span>, bukan koma desimal.")))


# ============================================================ BAB 9

add(OP(9, "Petugas &amp; hak akses",
       "Menambah akun petugas, mengatur perannya, dan mengganti kata sandi petugas lain. "
       "Menu ini hanya terbuka untuk Administrator Penuh.",
       ["Tambah petugas", "Peran Operator &amp; Admin", "Ganti sandi petugas lain"]))

add(P(9, "Menambah &amp; mengelola petugas",
      lede="Setiap petugas punya akun sendiri. Peran menentukan menu apa saja yang bisa "
          "dibuka.",
      fig="adm-petugas",
      cap="Halaman <b>Petugas &amp; Hak Akses Sistem</b>.",
      steps=[
          "Buka menu <span class='ui'>Petugas &amp; Hak Akses</span> (hanya untuk Admin).",
          "Pada <span class='ui'>Tambah Petugas Baru</span>, isi "
          "<span class='ui'>Nama lengkap petugas</span>.",
          "Isi <span class='ui'>Alamat email login</span>.",
          "Pilih <span class='ui'>Peran akses</span>: <i>Operator (Verifikasi &amp; Berita)</i> "
          "atau <i>Administrator Penuh</i>.",
          "Isi <span class='ui'>Kata sandi awal (8 - 16 karakter)</span>.",
          "Klik <span class='ui'>Tambahkan Petugas</span>.",
      ],
      note=("accent", "CATATAN",
            "Peran <i>Operator</i> hanya bisa memverifikasi dan menulis berita; menu "
            "Pengaturan Situs dan Petugas &amp; Hak Akses tidak muncul untuknya.")))

add(P(9, "Mengganti kata sandi petugas lain",
      lede="Bila petugas lupa kata sandi, Administrator dapat menetapkan kata sandi baru "
          "lewat halaman khusus per petugas.",
      fig="adm-sandi-petugas",
      cap="Halaman <b>Ganti Sandi</b> untuk satu petugas.",
      steps=[
          "Masuk sebagai Administrator.",
          "Pada daftar petugas, klik <span class='ui'>Ganti sandi</span> pada baris petugas.",
          "Isi <span class='ui'>Kata Sandi Baru</span> dan konfirmasinya sesuai "
          "<span class='ui'>Aturan Kata Sandi</span>.",
          "Simpan, lalu beri tahu petugas yang bersangkutan.",
      ],
      note=("warn", "PERHATIAN",
            "Untuk mengganti kata sandi Anda sendiri, jangan pakai menu Petugas. Gunakan "
            "<span class='ui'>Akun &amp; Kata Sandi</span> (Bab 10). Selain itu, <span class='ui'>Hapus "
            "Akun</span> tidak dapat dibatalkan.")))


# ============================================================ BAB 10

add(OP(10, "Akun &amp; kata sandi sendiri",
       "Mengganti kata sandi akun sendiri dengan aman. Aturan kata sandi berlaku sama "
       "untuk admin, operator, maupun siswa.",
       ["Ganti kata sandi", "Kesalahan umum"]))

add(P(10, "Mengganti kata sandi sendiri",
      lede="Setiap pengguna — admin, operator, maupun siswa — dapat mengganti kata "
          "sandinya sendiri tanpa bantuan orang lain.",
      fig="adm-akun",
      cap="Halaman <b>Akun &amp; Kata Sandi</b>.",
      steps=[
          "Buka menu <span class='ui'>Akun &amp; Kata Sandi</span>.",
          "Isi <span class='ui'>Kata sandi saat ini</span>.",
          "Isi <span class='ui'>Kata sandi baru</span> (8–16 karakter, ada huruf besar, "
          "huruf kecil, dan angka).",
          "Ulangi pada <span class='ui'>Ulangi kata sandi baru</span>.",
          "Klik <span class='ui'>Simpan Kata Sandi Baru</span>.",
      ],
      note=("accent", "CATATAN",
            "Di portal siswa, halaman yang sama bernama <span class='ui'>Akun &amp; Kata "
            "Sandi</span> dan tersedia di menu samping.")))

add(RAW(10, """
<h1 class="t-page">Kesalahan kata sandi yang sering terjadi</h1>
<p class="t-lede">Pesan galat di halaman akun dan artinya.</p>
""" + TBL(
    ["Pesan yang muncul", "Penyebab", "Tindakan"],
    [["Kata sandi saat ini tidak cocok", "Salah mengetik sandi lama.",
      "Periksa huruf besar-kecil, lalu ulangi."],
     ["Konfirmasi kata sandi belum cocok", "Kedua isian baru berbeda.",
      "Ketik ulang isian <i>Ulangi kata sandi baru</i>."],
     ["Kata sandi baru minimal 8 karakter", "Terlalu pendek.",
      "Perpanjang menjadi 8–16 karakter."],
     ["Harus memuat huruf besar, huruf kecil, dan angka", "Kombinasi belum lengkap.",
      "Tambahkan minimal satu huruf besar dan satu angka, misalnya <span class='mono'>Ppdb2026ku</span>."],
     ["Sandi baru tidak boleh sama dengan yang lama", "Memakai sandi lama kembali.",
      "Pilih kombinasi yang benar-benar baru."]]
) + """
<div class="callout">
  <span class="label">BILA GAGAL</span>
  Bila semua syarat sudah benar tetapi tetap ditolak, muat ulang halaman sekali; sesi
  lama kadang membuat formulir menolak pengiriman.
</div>
"""))


# ============================================================ BAB 11

add(OP(11, "Portal calon siswa",
       "Alur yang dilalui calon siswa dan orang tua: dari membuat akun sampai mencetak "
       "kartu peserta. Bab ini juga dipakai panitia untuk menjelaskan ke orang tua.",
       ["Membuat akun", "Enam tahap pendaftaran", "Kartu peserta &amp; pengumuman",
        "Bantuan untuk orang tua"]))

add(P(11, "Membuat akun pendaftar",
      lede="Akun dibuat sendiri oleh calon siswa atau orang tua di "
          "<span class='mono'>/ppdb/daftar</span>, melalui dua langkah singkat.",
      fig="pub-daftar",
      cap="Halaman <b>Buat Akun PPDB</b> — langkah 1 Data Akun.",
      steps=[
          "Buka <span class='mono'>/ppdb/daftar</span>.",
          "<b>Langkah 1 — Data Akun</b>: isi <span class='ui'>Email Calon Siswa / Orang "
          "Tua</span>, <span class='ui'>Kata Sandi</span>, dan "
          "<span class='ui'>Ulangi Kata Sandi</span>.",
          "Klik lanjut ke langkah berikutnya.",
          "<b>Langkah 2 — Identitas Siswa</b>: isi <span class='ui'>Nama Lengkap Calon "
          "Siswa</span>, <span class='ui'>Tanggal Lahir</span>, "
          "<span class='ui'>Nomor WhatsApp Aktif</span>, dan <span class='ui'>Nomor Induk "
          "Kependudukan (NIK)</span>.",
          "Kirim formulir; akun aktif dan portal terbuka.",
      ],
      note=("warn", "PERHATIAN",
            "Isian <i>Jangan diisi</i> pada formulir adalah penangkap bot. Jangan diubah. "
            "Kata sandi wajib 8–16 karakter dengan huruf besar, huruf kecil, dan angka.")))

add(P(11, "Enam tahap pendaftaran",
      lede="Di portal, pendaftaran berjalan berurutan. Tahap berikutnya terkunci sampai "
          "tahap sebelumnya selesai.",
      fig="por-beranda-terkunci",
      cap="Beranda portal saat tahap lanjutan masih <b>terkunci</b>.",
      steps=[
          "<b>Tahap 1 — Pendaftaran Jalur</b>: memilih jalur (Inden, Prestasi, Reguler).",
          "<b>Tahap 2 — Pembayaran / Konfirmasi</b>: gratis otomatis, atau unggah bukti "
          "transfer.",
          "<b>Tahap 3 — Pengisian Data Diri</b>: identitas siswa dan orang tua.",
          "<b>Tahap 4 — Unggah Berkas Persyaratan</b>: empat berkas wajib.",
          "<b>Tahap 5 — Kuesioner Calon Siswa</b>: sumber informasi dan minat.",
          "<b>Tahap 6 — Kartu Peserta &amp; Pengumuman</b>: cetak kartu, pantau hasil.",
      ],
      note=("accent", "CATATAN",
            "Urutan ini sengaja dibuat bertahap agar data masuk lengkap. Bila tahap 4 "
            "berkas ditolak panitia, tahap 5 dan 6 ikut terkunci lagi sampai revisi diterima.")))

add(P(11, "Memilih jalur &amp; mengunggah berkas",
      lede="Tahap pertama memilih jalur; tahap keempat mengunggah dokumen. Keduanya paling "
          "sering ditanyakan orang tua.",
      fig="por-jalur",
      cap="Tahap <b>Pendaftaran Jalur</b> di portal siswa.",
      steps=[
          "Pada <span class='ui'>Pilih Jalur Pendaftaran</span>, pilih satu jalur yang "
          "tersedia.",
          "Lanjut ke tahap pembayaran, lalu isi data diri.",
          "Pada <span class='ui'>Unggah Berkas Persyaratan</span>, unggah "
          "<b>Kartu Keluarga</b>, <b>Akta Kelahiran</b>, <b>SKL/Rapor</b>, dan "
          "<b>Pas Foto</b>.",
          "Tunggu panitia memeriksa; status tiap berkas tampil di halaman yang sama.",
      ],
      note=("warn", "PERHATIAN",
            "Ukuran tiap berkas dibatasi 4 MB dan formatnya JPG, PNG, WebP, atau PDF. "
            "Kesalahan paling umum adalah memotret berkas dengan resolusi terlalu besar.")))

add(P(11, "Kartu peserta &amp; pengumuman",
      lede="Setelah semua tahap selesai, siswa dapat mencetak kartu peserta dan memantau "
          "hasil seleksi.",
      fig="por-kartu",
      cap="Tahap <b>Kartu Tanda Peserta PPDB</b>.",
      steps=[
          "Buka tahap <span class='ui'>Kartu Peserta &amp; Pengumuman</span>.",
          "Periksa data pada <span class='ui'>Kartu Tanda Peserta PPDB</span>.",
          "Cetak atau simpan kartu sebagai PDF.",
          "Buka menu <span class='ui'>Pengumuman</span> untuk melihat hasil seleksi.",
      ],
      note=("accent", "CATATAN",
            "Kartu peserta memuat nomor pendaftaran. Minta siswa membawa nomor ini saat "
            "datang ke sekolah.")))

add(P(11, "Bantuan untuk orang tua",
      lede="Sebelum mulai mendaftar, siapkan berkas dan nomor yang dibutuhkan. Bila "
          "tersendat, buka menu bantuan di portal.",
      fig="por-bantuan",
      cap="Halaman <b>Pusat Bantuan PPDB Online</b>.",
      steps=[
          "Siapkan: Kartu Keluarga, Akta Kelahiran, SKL/Rapor, pas foto, dan nomor WhatsApp "
          "aktif.",
          "Siapkan NIK siswa (16 digit) dan NISN bila sudah ada.",
          "Bila menu <i>Data Diri</i> atau <i>Berkas</i> terkunci, selesaikan tahap "
          "sebelumnya dahulu.",
          "Bila SKL/Ijazah SD belum terbit, unggah rapor sementara dan hubungi sekretariat.",
          "Baca <span class='ui'>Kontak Sekretariat PPDB</span> untuk nomor layanan.",
      ],
      note=("accent", "CATATAN",
            "Bila berkas diminta revisi panitia, siswa dapat menggantinya lewat halaman "
            "berkas selama masa pendaftaran masih dibuka.")))


# ============================================================ BAB 12

add(OP(12, "Situs publik",
       "Halaman yang dilihat pengunjung tanpa login: beranda, profil, akademik, berita, "
       "alumni, PPDB, dan kontak.",
       ["Peta halaman publik", "Dua tombol utama", "Tampilan di ponsel"]))

add(P(12, "Peta halaman publik",
      lede="Ketujuh halaman publik dan isi pokoknya.",
      fig="pub-beranda",
      cap="Beranda situs publik.",
      steps=[
          "<span class='mono'>/</span> — Beranda: sambutan, kartu tahap PPDB, dan berita terbaru.",
          "<span class='mono'>/profil</span> — Profil sekolah dan visi.",
          "<span class='mono'>/akademik</span> — Kurikulum dan mata pelajaran.",
          "<span class='mono'>/berita</span> — Berita &amp; pengumuman.",
          "<span class='mono'>/alumni</span> — Ulasan alumni.",
          "<span class='mono'>/ppdb</span> — Halaman informasi PPDB.",
          "<span class='mono'>/kontak</span> — Alamat, telepon, dan peta.",
      ],
      note=("accent", "CATATAN",
            "Isi halaman ini berasal dari panel admin. Berita dari Bab 4, foto dari Bab 5, "
            "dan identitas dari Bab 8.")))

add(P(12, "Dua tombol utama &amp; tampilan ponsel",
      lede="Beranda mengarahkan pengunjung ke dua tindakan penting, dan wajib nyaman "
          "dibuka dari ponsel.",
      fig="pub-ppdb",
      cap="Halaman informasi PPDB publik.",
      steps=[
          "Tombol <span class='ui'>Alur pendaftaran</span> membuka "
          "<span class='mono'>/ppdb/daftar</span>.",
          "Tombol <span class='ui'>Kenali sekolah</span> membuka "
          "<span class='mono'>/profil</span>.",
          "Sebagian besar pengunjung datang dari ponsel, jadi periksa tampilannya di layar "
          "kecil setelah mengubah isi.",
          "Bila tombol tidak mengarah ke halaman yang benar, periksa Bab 8.",
      ],
      note=("warn", "PERHATIAN",
            "Setelah mengubah berita atau pengaturan, buka situs publik di peramban "
            "penyamaran (mode incognito) untuk memastikan perubahan sudah tampil.")))


# ============================================================ BELAKANG

add(RAW(0, t="Pemecahan masalah",
      lede="Kasus yang paling sering terjadi dan jalan keluarnya. Cari gejala yang Anda alami pada kolom kiri.",
      html="""
<h1 class="t-page">Pemecahan masalah</h1>
<p class="t-lede">Kasus yang paling sering terjadi dan jalan keluarnya. Cari gejala yang
Anda alami pada kolom kiri.</p>
""" + TBL(
    ["Gejala", "Kemungkinan penyebab", "Yang harus dilakukan"],
    [["Tidak bisa masuk panel admin",
      "Email atau kata sandi salah, atau akun tidak aktif.",
      "Periksa alamat email login, lalu minta tautan reset lewat "
      "<span class='ui'>Minta tautan pengaturan kata sandi</span>."],
     ["Email reset tidak masuk",
      "Pengiriman email sekolah bergantung penyetelan SMTP.",
      "Tunggu beberapa menit; periksa folder spam. Bila tetap tidak ada, minta "
      "Administrator mengganti kata sandi lewat menu Petugas (Bab 9)."],
     ["Ingin ganti kata sandi sendiri",
      "—",
      "Buka menu <span class='ui'>Akun &amp; Kata Sandi</span> (Bab 10)."],
     ["Unggahan gagal / ditolak",
      "Ukuran lebih dari 4 MB atau format salah.",
      "Kecilkan gambar atau ubah ke JPG/PDF, lalu unggah ulang."],
     ["Tahap berikutnya terkunci",
      "Tahap sebelumnya belum selesai.",
      "Selesaikan jalur, pembayaran, atau berkas dahulu (Bab 11)."],
     ["Kartu peserta tidak muncul",
      "Berkas atau kuesioner belum tuntas.",
      "Pastikan empat berkas berstatus <i>Valid</i> dan kuesioner sudah diisi."]]
) + """
""" + TBL(
    ["Gejala", "Kemungkinan penyebab", "Yang harus dilakukan"],
    [["Status di portal tidak berubah",
      "Keputusan belum disimpan panitia.",
      "Klik <span class='ui'>Simpan Status Seleksi</span> di detail pendaftar (Bab 3)."],
     ["Pembayaran masih menunggu",
      "Bukti belum diverifikasi TU.",
      "Buka bukti transfer lalu klik <span class='ui'>Perbarui Status Pembayaran</span>."],
     ["Halaman publik tidak berubah setelah simpan",
      "Peramban menampilkan versi lama.",
      "Muat ulang tanpa cache (Ctrl+Shift+R) atau buka di mode incognito."],
     ["Berita tidak muncul di situs",
      "Status masih <i>Draf</i>.",
      "Ubah <span class='ui'>Status penayangan</span> ke <i>Terbit langsung di situs publik</i>."],
     ["Data CSV kacau di Excel",
      "Nol di depan NIK/WA hilang.",
      "Format kolom sebagai teks sebelum menempel."],
     ["Operator ditolak saat membuka menu",
      "Menu itu hanya untuk Administrator.",
      "Minta Administrator membuka menu tersebut."]]
) + """
<div class="callout">
  <span class="label">BILA GAGAL</span>
  Bila masalah tidak teratasi dengan tabel di atas, catat pesan galat yang muncul
  (tuliskan apa adanya), lalu hubungi kontak bantuan di halaman terakhir buku ini.
</div>
"""))


add(RAW(0, t="Daftar istilah",
      lede="Istilah yang sering muncul di panel admin dan portal siswa.",
      html="""
<h1 class="t-page">Daftar istilah</h1>
<p class="t-lede">Istilah yang sering muncul di panel admin dan portal siswa.</p>
""" + TBL(
    ["Istilah", "Arti"],
    [["<b>PPDB</b>", "Penerimaan Peserta Didik Baru — proses penerimaan siswa baru."],
     ["<b>Jalur Inden</b>", "Gelombang pendaftaran awal dengan verifikasi prioritas."],
     ["<b>Jalur Prestasi</b>", "Jalur bagi calon siswa berprestasi akademik atau non-akademik."],
     ["<b>Jalur Reguler</b>", "Jalur penerimaan umum pada gelombang reguler."],
     ["<b>Verifikasi</b>", "Pemeriksaan berkas dan pembayaran oleh panitia sampai dinyatakan sah."],
     ["<b>Draf</b>", "Entri berita yang disimpan tetapi belum tampil di situs publik."],
     ["<b>Permalink / Slug</b>", "Alamat pendek halaman berita, misalnya "
      "<span class='mono'>/berita/informasi-awal-tahun-ajaran</span>."],
     ["<b>NIK</b>", "Nomor Induk Kependudukan, 16 digit, tertera di Kartu Keluarga."],
     ["<b>NISN</b>", "Nomor Induk Siswa Nasional, diberikan sekolah dasar."],
     ["<b>NPSN</b>", "Nomor Pokok Sekolah Nasional identitas sekolah."],
     ["<b>SKL</b>", "Surat Keterangan Lulus dari SD/MI, pengganti ijazah yang belum terbit."]],
    cls="tb"
) + """
"""))


add(RAW(0, t="Checklist panitia",
      lede="Rutinitas harian dan mingguan selama masa pendaftaran. Cetak halaman ini dan centang tiap butir.",
      html="""
<h1 class="t-page">Checklist panitia</h1>
<p class="t-lede">Rutinitas harian dan mingguan selama masa pendaftaran. Cetak halaman ini
dan centang tiap butir.</p>
""" + TBL(
    ["Rutin", "Butir pemeriksaan", "Selesai"],
    [["Harian", "Buka <b>Ringkasan</b> dan catat jumlah pendaftar baru.", '<span class="box"></span>'],
     ["Harian", "Periksa berkas yang menunggu validasi (Bab 3).", '<span class="box"></span>'],
     ["Harian", "Verifikasi pembayaran dan bukti transfer yang masuk.", '<span class="box"></span>'],
     ["Harian", "Periksa tab <b>Pesan Cepat / Konsultasi</b> dari publik.", '<span class="box"></span>'],
     ["Mingguan", "Perbarui status seleksi pendaftar yang sudah lengkap.", '<span class="box"></span>'],
     ["Mingguan", "Terbitkan berita atau pengumuman terbaru.", '<span class="box"></span>'],
     ["Mingguan", "Unduh data CSV dan simpan cadangan di komputer sekolah.", '<span class="box"></span>'],
     ["Mingguan", "Periksa tampilan situs publik dari ponsel.", '<span class="box"></span>']],
    cls="tb"
) + """
<div class="callout callout--accent">
  <span class="label">CATATAN</span>
  Pada pekan-pekan sibuk, utamakan butir harian. Butir mingguan menyusul saat arus
  pendaftar mulai sepi.
</div>
"""))


add(RAW(0, t="Kontak &amp; bantuan",
      lede="Bila ada yang tidak sesuai atau sistem terasa lambat, hubungi sekretariat PPDB.",
      html="""
<h1 class="t-page">Kontak &amp; bantuan</h1>
<p class="t-lede">Bila ada yang tidak sesuai atau sistem terasa lambat, hubungi sekretariat
PPDB.</p>

<div class="cols cols--2" style="gap:10mm">
  <div>
    <h3 class="t-h3">Sekretariat PPDB</h3>
    <dl class="kv">
      <dt>Sekolah</dt><dd>SMP PGRI 4 Waru</dd>
      <dt>Alamat</dt><dd>Jl. Anggrek No. 109, Kureksari,<br>Kec. Waru, Kab. Sidoarjo, Jawa Timur 61256</dd>
      <dt>Telepon</dt><dd>(031) 8538447</dd>
      <dt>NPSN</dt><dd>20540020</dd>
      <dt>Jam layanan</dt><dd>Senin–Sabtu, jam kerja tata usaha</dd>
    </dl>
  </div>
  <div>
    <h3 class="t-h3">Yang perlu disiapkan saat menghubungi</h3>
    """ + UL([
        "Nomor pendaftaran siswa (bila sudah mendaftar).",
        "Menu atau halaman yang bermasalah.",
        "Pesan galat, ditulis apa adanya.",
        "Tangkapan layar bila memungkinkan.",
    ]) + """
    <div class="callout callout--accent" style="margin-top:4mm">
      <span class="label">CATATAN</span>
      Jangan pernah mengirim kata sandi lewat pesan. Bila kata sandi hilang, minta
      tautan pengaturan ulang (Bab 1) atau minta Administrator menetapkan sandi baru (Bab 9).
    </div>
  </div>
</div>

<p class="tiny" style="margin-top:8mm">Panduan Penggunaan Website SMP PGRI 4 Waru —
versi 1.0, November 2026. Tahun ajaran 2026/2027. Perbarui buku ini bila ada menu atau
tampilan yang berubah.</p>
"""))


add({"kind": "back", "ch": 0})
