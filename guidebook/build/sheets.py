#!/usr/bin/env python3
"""Contact sheet untuk memeriksa hasil tangkapan layar (hemat waktu baca)."""
import os
import sys
from PIL import Image, ImageDraw, ImageFont

SRC = "/home/melfiano/Documents/web-smp-pgri-4waru/guidebook/screenshots"
OUT = "/tmp/opencode/sheets"
os.makedirs(OUT, exist_ok=True)

COLS, ROWS = 4, 3
TW, TH = 470, 294
PAD = 10


def font(size):
    for p in ["/usr/share/fonts/TTF/DejaVuSans-Bold.ttf", "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"]:
        if os.path.exists(p):
            return ImageFont.truetype(p, size)
    return ImageFont.load_default()


def sheet(files, name):
    f = font(18)
    W = COLS * (TW + PAD) + PAD
    H = ROWS * (TH + 30 + PAD) + PAD
    img = Image.new("RGB", (W, H), (235, 236, 238))
    d = ImageDraw.Draw(img)
    for i, fp in enumerate(files):
        x = PAD + (i % COLS) * (TW + PAD)
        y = PAD + (i // COLS) * (TH + 30 + PAD)
        try:
            im = Image.open(fp).convert("RGB")
        except Exception as e:
            d.text((x, y), f"ERR {e}", font=f, fill=(200, 0, 0))
            continue
        im.thumbnail((TW, TH))
        img.paste(im, (x + (TW - im.width) // 2, y))
        d.text((x, y + TH + 4), os.path.basename(fp)[:-4], font=f, fill=(20, 20, 20))
    img.save(f"{OUT}/{name}.png")
    print(name, len(files))


if __name__ == "__main__":
    names = sys.argv[1:]
    if not names:
        names = sorted(n[:-4] for n in os.listdir(SRC) if n.endswith(".png"))
    files = [f"{SRC}/{n}.png" for n in names if os.path.exists(f"{SRC}/{n}.png")]
    per = COLS * ROWS
    for i in range(0, len(files), per):
        sheet(files[i : i + per], f"sheet-{i // per + 1:02d}")