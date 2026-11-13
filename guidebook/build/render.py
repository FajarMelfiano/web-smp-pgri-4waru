#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Bangun buku dari content.py -> out/index.html (siap cetak A4).

Menghasilkan:
    out/index.html          halaman bertata letak cetak 216x303 mm (trim 210x297 + bleed)
    out/fonts/*.woff2       font tertanam supaya PDF identik di mesin mana pun

Tahap berikutnya (PDF + PNG) dikerjakan topdf.js / pdftoppm.
"""
import os
import re
import shutil
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(HERE)                       # guidebook/
OUT = os.path.join(ROOT, "out")
FONTS = os.path.join(OUT, "fonts")
FIGSRC = os.path.join(ROOT, "screenshots")
FIGOUT = os.path.join(OUT, "figs")
FONT_SRC = os.path.join(
    os.path.dirname(os.path.dirname(HERE)),
    "laravel", "public", "fonts", "plus-jakarta-sans-var.woff2",
)

sys.path.insert(0, HERE)
import content  # noqa: E402

MARKS = (
    '<div class="marks">'
    '<i class="v tl-v"></i><i class="h tl-h"></i>'
    '<i class="v tr-v"></i><i class="h tr-h"></i>'
    '<i class="v bl-v"></i><i class="h bl-h"></i>'
    '<i class="v br-v"></i><i class="h br-h"></i>'
    '</div>'
)


def fig_path(name):
    """Cari gambar: hasil potong (out/figs) dulu, lalu tangkapan mentah."""
    for base in (FIGOUT, FIGSRC):
        p = os.path.join(base, name + ".png")
        if os.path.exists(p):
            return os.path.relpath(p, OUT)
    return None


def esc(s):
    return s


def render_cover(page):
    return """
<section class="gb-page p-cover">
  <div class="bg"></div>
  <div class="trim">
    <div class="cover">
      <div class="cover__edge"></div>
      <div class="cover__mark"><span class="dot"></span> SMP PGRI 4 WARU · SIDOARJO</div>
      <h1 class="cover__title">Panduan Penggunaan <em>Website</em> Sekolah</h1>
      <p class="cover__sub">PPDB Online, berita, galeri, dan pengaturan situs — untuk
      panitia penerimaan siswa baru.</p>
      <div class="cover__meta">
        <div><b>Versi 1.0</b>November 2026</div>
        <div><b>Tahun ajaran</b>2026 / 2027</div>
        <div><b>Untuk</b>Kepala sekolah · TU · operator · guru</div>
      </div>
    </div>
  </div>
</section>"""


def render_imprint(page, num):
    return """
<section class="gb-page">
  <div class="bg"></div>
  <div class="trim">
    <h1 class="t-page">Tentang buku ini</h1>
    <p class="t-lede">Hak pakai, versi dokumen, dan batas cakupannya.</p>
    <div class="cols cols--2" style="gap:9mm">
      <div>
        <p>Buku ini adalah panduan resmi penggunaan website <b>SMP PGRI 4 Waru</b> untuk
        keperluan operasional sekolah. Isinya disusun dari aplikasi yang benar-benar
        berjalan pada November 2026, sehingga setiap nama menu, tombol, dan kolom sesuai
        dengan yang terlihat di layar.</p>
        <p>Panduan ditujukan bagi kepala sekolah, wakil kepala sekolah, staf tata usaha,
        operator, dan guru. Buku ini <b>bukan</b> panduan teknis untuk pembuat website,
        dan bukan pula panduan bagi calon siswa — bagian untuk mereka ada di Bab 11.</p>
      </div>
      <div>
        <p>Bila suatu saat ada menu, tombol, atau tampilan yang berubah, halaman yang
        bersangkutan perlu diperbarui agar tidak menyesatkan.</p>
        <h3 class="t-h3">Batasan</h3>
        <p>Untuk menjaga keamanan, buku ini tidak memuat kata sandi, tautan pengaturan
        ulang kata sandi, email pribadi nyata, maupun nomor NIK asli. Semua data yang
        tampak pada tangkapan layar adalah data contoh.</p>
      </div>
    </div>

    <div class="hr" style="margin:7mm 0"></div>

    <div class="cols cols--2" style="gap:9mm">
      <div>
        <dl class="kv">
          <dt>Judul</dt><dd>Panduan Penggunaan Website SMP PGRI 4 Waru</dd>
          <dt>Versi</dt><dd>1.0 — November 2026</dd>
          <dt>Tahun ajaran</dt><dd>2026 / 2027</dd>
        </dl>
      </div>
      <div>
        <dl class="kv">
          <dt>Penerbit</dt><dd>SMP PGRI 4 Waru</dd>
          <dt>NPSN</dt><dd>20540020</dd>
          <dt>Alamat</dt><dd>Jl. Anggrek No. 109, Kureksari, Waru, Sidoarjo 61256</dd>
        </dl>
      </div>
    </div>

    <div class="callout callout--accent" style="margin-top:6mm">
      <span class="label">CATATAN</span>
      Salinan digital buku ini disimpan di komputer tata usaha. Bila website berubah,
      catat tanggal perubahannya pada halaman ini.
    </div>
    <div class="spacer"></div>
  </div>
</section>"""


def render_opener(page, num, ch_no):
    items = "".join(
        "<span><b>%02d</b> %s</span>" % (i, it)
        for i, it in enumerate(page["items"], start=1)
    )
    return """
<section class="gb-page p-opener">
  <div class="bg"></div>
  <div class="trim">
    <div class="opener">
      <div class="opener__num">%d</div>
      <h1 class="opener__title">%s</h1>
      <p class="opener__goal">%s</p>
      <div class="opener__list">%s</div>
    </div>
  </div>
</section>""" % (ch_no, page["t"], page["goal"], items)


def render_toc(page, num, entries):
    # entries: list of (group_title, [(label, page, ch), ...])
    out = []
    for grp, rows in entries:
        body = "".join(
            '<div class="row"><span class="t">%s</span><span class="d"></span>'
            '<span class="p">%s</span></div>' % (lbl, pg)
            for lbl, pg in rows
        )
        out.append('<div class="grp"><div class="grp-title">%s</div>%s</div>' % (grp, body))
    return """
<section class="gb-page">
  <div class="bg"></div>
  <div class="trim">
    <h1 class="t-page">Daftar isi</h1>
    <p class="t-lede">Nomor halaman mengacu pada halaman tercetak.</p>
    <div class="toc">%s</div>
    <div class="spacer"></div>
  </div>
</section>""" % "".join(out)


def render_content(page, num, ch_no):
    parts = []
    html = page.get("html", "")
    if page.get("t"):
        # buang judul/pengantar yang sudah ditulis di dalam html agar tidak ganda
        html = html.replace('<h1 class="t-page">%s</h1>' % page["t"], "", 1)
        html = re.sub(r'<p class="t-lede">.*?</p>', "", html, count=1, flags=re.S)
        parts.append('<h1 class="t-page">%s</h1>' % page["t"])
    if page.get("lede"):
        parts.append('<p class="t-lede">%s</p>' % page["lede"])

    if html.strip():
        parts.append(html)

    fig = page.get("fig")
    if fig:
        rel = fig_path(fig)
        if rel:
            parts.append(
                '<figure class="fig"><div class="frame"><img src="%s" alt=""></div>'
                '<figcaption>%s</figcaption></figure>' % (rel, page.get("cap", ""))
            )
        else:
            parts.append(
                '<div class="callout callout--warn"><span class="label">GAMBAR HILANG</span>'
                'Berkas <span class="mono">%s.png</span> tidak ditemukan.</div>' % fig
            )

    if page.get("steps"):
        lis = "".join("<li>%s</li>" % s for s in page["steps"])
        parts.append('<ol class="steps">%s</ol>' % lis)

    note = page.get("note")
    if note:
        kind, label, body = note
        cls = {"warn": " callout--warn", "accent": " callout--accent"}.get(kind or "", "")
        parts.append(
            '<div class="callout%s"><span class="label">%s</span>%s</div>'
            % (cls, label, body)
        )

    folio = make_folio(num, ch_no)
    return """
<section class="gb-page %s">
  <div class="bg"></div>
  <div class="trim">
    %s
    <div class="spacer"></div>
    %s
  </div>
</section>""" % (page.get("cls", ""), "\n".join(parts), folio)


def make_folio(num, ch_no):
    if ch_no:
        return '<div class="folio"><b>Bab %d</b><span class="sep">·</span>%d</div>' % (ch_no, num)
    return '<div class="folio">%d</div>' % num


def render_back(page):
    return """
<section class="gb-page p-back">
  <div class="bg"></div>
  <div class="trim">
    <div class="back">
      <div class="back__title">Website sekolah bekerja paling baik ketika panitia tahu
      ke mana harus menekan.</div>
      <div class="back__rows">
        <div><b>Sekretariat PPDB</b>SMP PGRI 4 Waru — Jl. Anggrek No. 109, Kureksari,
        Waru, Sidoarjo 61256 · Telp (031) 8538447</div>
        <div><b>Bantuan teknis</b>Hubungi Administrator Penuh sekolah untuk masalah kata
        sandi, berkas yang gagal terunggah, atau tampilan yang tidak sesuai.</div>
        <div><b>Versi dokumen</b>1.0 — November 2026 · Tahun ajaran 2026/2027</div>
      </div>
    </div>
  </div>
</section>"""


def main():
    os.makedirs(OUT, exist_ok=True)
    os.makedirs(FONTS, exist_ok=True)
    if os.path.exists(FONT_SRC):
        shutil.copy2(FONT_SRC, os.path.join(FONTS, os.path.basename(FONT_SRC)))

    pages = content.PAGES

    # ---- nomor halaman + nomor bab berjalan
    ch_counter = 0
    numbered = []
    for pg in pages:
        numbered.append(pg)
        if pg.get("kind") == "opener":
            ch_counter += 1

    # hitung nomor halaman (mulai 1) dan peta bab -> halaman mulai
    ch_start = {}
    ch_of = []
    ch_cur = 0
    for i, pg in enumerate(numbered, start=1):
        if pg.get("kind") == "opener":
            ch_cur += 1
            ch_start[ch_cur] = i
        ch_of.append(ch_cur)

    # ---- daftar isi
    front_rows = []
    for i, pg in enumerate(numbered, start=1):
        k = pg.get("kind")
        if k in ("cover", "imprint", "toc", "opener", "back"):
            continue
        if ch_of[i - 1] == 0:
            front_rows.append((pg.get("t", "—"), i))

    front_groups = [
        ("BAGIAN DEPAN", [
            ("Tentang buku ini", 2),
            ("Kata pengantar", next((n for n, pg in enumerate(numbered, 1)
                                     if pg.get("t") == "Kata pengantar"), 3)),
            ("Cara memakai panduan ini", next((n for n, pg in enumerate(numbered, 1)
                                               if pg.get("t") == "Cara memakai panduan ini"), 4)),
            ("Peta sistem", next((n for n, pg in enumerate(numbered, 1)
                                  if pg.get("t") == "Peta sistem"), 6)),
        ])
    ]
    chap_titles = {}
    for pg in numbered:
        if pg.get("kind") == "opener":
            chap_titles[pg["ch"]] = pg["t"]
    chap_groups = [("BAB", [(chap_titles[c], ch_start[c]) for c in sorted(chap_titles)])]

    back_rows = []
    last_ch = max(ch_start) if ch_start else 0
    for i, pg in enumerate(numbered, start=1):
        if pg.get("kind") == "back":
            continue
        # halaman belakang: bab 0 yang berada setelah bab terakhir
        if pg.get("ch") == 0 and pg.get("t") and ch_of[i - 1] == last_ch and i > 6:
            back_rows.append((pg.get("t"), i))
    back_groups = [("BAGIAN BELAKANG", back_rows)] if back_rows else []

    toc_entries = front_groups + chap_groups + back_groups

    # ---- rakit HTML
    body = []
    for i, pg in enumerate(numbered, start=1):
        ch = ch_of[i - 1]
        k = pg.get("kind")
        if k == "cover":
            html = render_cover(pg)
        elif k == "imprint":
            html = render_imprint(pg, i)
        elif k == "toc":
            html = render_toc(pg, i, toc_entries)
        elif k == "opener":
            html = render_opener(pg, i, pg["ch"])
        elif k == "back":
            html = render_back(pg)
        else:
            html = render_content(pg, i, pg.get("ch", ch))
        # sisipkan tanda potong pada tiap halaman
        body.append(html.replace("</section>", MARKS + "\n</section>", 1))

    css = open(os.path.join(HERE, "print.css"), encoding="utf-8").read()
    html = """<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Panduan Penggunaan Website SMP PGRI 4 Waru</title>
<style>
%s
</style>
</head>
<body>
%s
</body>
</html>""" % (css, "\n".join(body))

    with open(os.path.join(OUT, "index.html"), "w", encoding="utf-8") as fh:
        fh.write(html)

    print("halaman:", len(numbered))
    print("bab:", len(chap_titles))
    missing = [pg["fig"] for pg in numbered if pg.get("fig") and not fig_path(pg["fig"])]
    if missing:
        print("gambar belum ada:", ", ".join(sorted(set(missing))))
    print("out/index.html ditulis")


if __name__ == "__main__":
    main()
