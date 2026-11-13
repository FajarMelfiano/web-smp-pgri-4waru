#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Siapkan figur buku dari tangkapan layar mentah.

Tangkapan asli diambil pada viewport 1440x900 dengan deviceScaleFactor 2, jadi
berkasnya 2880x1800 (rasio 16:10). Halaman yang panjang (fullPage) dipotong ke
jendela 16:10 dari atas. Hasilnya dikecilkan ke lebar 2000 px agar PDF tidak
membengkak, tetapi tetap ~420 dpi saat dicetak selebar kolom buku.

    python3 figs.py            # buat semua figur yang dipakai content.py
    python3 figs.py --list     # tampilkan figur yang dipakai
"""
import os
import sys

from PIL import Image

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(HERE)
SHOTS = os.path.join(ROOT, "screenshots")
OUT = os.path.join(ROOT, "out", "figs")
os.makedirs(OUT, exist_ok=True)

RATIO = 10 / 16          # tinggi : lebar
TARGET_W = 2000


def build(name):
    src = os.path.join(SHOTS, name + ".png")
    dst = os.path.join(OUT, name + ".png")
    if not os.path.exists(src):
        print("  ! tidak ada tangkapan:", name)
        return None
    im = Image.open(src).convert("RGB")
    W, H = im.size
    want_h = int(round(W * RATIO))
    if H > want_h:
        im = im.crop((0, 0, W, want_h))
    elif H < want_h:
        # gambar lebih pendek dari 16:10: tambahkan latar putih di bawah
        pad = Image.new("RGB", (W, want_h), (255, 255, 255))
        pad.paste(im, (0, 0))
        im = pad
    if im.width > TARGET_W:
        r = TARGET_W / im.width
        im = im.resize((TARGET_W, int(round(im.height * r))), Image.LANCZOS)
    im.save(dst, optimize=True)
    return im.size


if __name__ == "__main__":
    if "--list" in sys.argv:
        import content
        for n in sorted({p["fig"] for p in content.PAGES if p.get("fig")}):
            print(n)
        sys.exit(0)
    import content
    names = sorted({p["fig"] for p in content.PAGES if p.get("fig")})
    ok = 0
    for n in names:
        s = build(n)
        if s:
            ok += 1
            print("%-26s %dx%d" % (n, s[0], s[1]))
    print("figur dibuat:", ok, "dari", len(names))
