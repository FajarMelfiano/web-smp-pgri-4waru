#!/usr/bin/env python3
"""Mock dokumen pendaftaran untuk tangkapan layar guidebook.

Semua gambar diberi tanda "CONTOH" dan memakai data fiktif; tidak ada data
pribadi nyata. Dijalankan sekali: python3 make-docs.py
"""
import os
from PIL import Image, ImageDraw, ImageFont

OUT = "/tmp/opencode/mockdocs"
os.makedirs(OUT, exist_ok=True)

W, H = 900, 1200
INK = (24, 24, 27)
INK3 = (107, 107, 116)
LINE = (228, 228, 231)
ACCENT = (14, 94, 143)
TINT = (244, 244, 245)


def font(size, bold=False):
    paths = [
        "/usr/share/fonts/TTF/DejaVuSans-Bold.ttf" if bold else "/usr/share/fonts/TTF/DejaVuSans.ttf",
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf" if bold else "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf",
    ]
    for p in paths:
        if os.path.exists(p):
            return ImageFont.truetype(p, size)
    return ImageFont.load_default()


def base(title, subtitle):
    img = Image.new("RGB", (W, H), "white")
    d = ImageDraw.Draw(img)
    d.rectangle([0, 0, W, 120], fill=ACCENT)
    d.text((48, 34), title, font=font(40, True), fill="white")
    d.text((48, 84), subtitle, font=font(20), fill=(226, 238, 246))
    d.text((W - 300, H - 70), "CONTOH - DATA FIKTIF", font=font(20, True), fill=(200, 60, 40))
    d.rectangle([0, 0, W - 1, H - 1], outline=LINE)
    return img, d


def rows(d, y, data, label_w=280):
    for label, value in data:
        d.text((48, y), label, font=font(22), fill=INK3)
        d.text((48 + label_w, y), value, font=font(22, True), fill=INK)
        d.line([48, y + 38, W - 48, y + 38], fill=LINE)
        y += 56
    return y


def kk():
    img, d = base("KARTU KELUARGA", "No. KK 3515 0000 0000 0002 - contoh")
    y = 180
    d.text((48, y), "KEPALA KELUARGA", font=font(20, True), fill=ACCENT)
    y = rows(d, y + 40, [
        ("Nama", "Surya Wijaya Kusuma"),
        ("NIK", "3515 0000 0000 0002"),
        ("Alamat", "Jl. Melati No. 21, Kureksari"),
        ("RT/RW", "004 / 002"),
        ("Desa / Kelurahan", "Kureksari"),
        ("Kecamatan", "Waru"),
        ("Kabupaten", "Sidoarjo"),
    ])
    img.save(f"{OUT}/kk.png")


def akta():
    img, d = base("AKTA KELAHIRAN", "Kutipan Akta Kelahiran - contoh")
    y = rows(d, 190, [
        ("Nama Anak", "Anindita Kirana Wardani"),
        ("NIK Anak", "3515 0000 0000 0002"),
        ("Tempat / Tanggal Lahir", "Sidoarjo, 14 Maret 2013"),
        ("Jenis Kelamin", "Perempuan"),
        ("Nama Ayah", "Surya Wijaya Kusuma"),
        ("Nama Ibu", "Ratna Dewi Anggraini"),
        ("Nomor Akta", "474.1 / 1204 / 2013"),
    ])
    img.save(f"{OUT}/akta.png")


def skl():
    img, d = base("SURAT KETERANGAN LULUS", "SD Negeri Kureksari 1 - contoh")
    y = rows(d, 190, [
        ("Nama", "Anindita Kirana Wardani"),
        ("NISN", "0000000002"),
        ("Tempat / Tanggal Lahir", "Sidoarjo, 14 Maret 2013"),
        ("Tahun Lulus", "2026"),
        ("Rata-rata Nilai", "88,4"),
        ("Nomor Surat", "421.2 / 108 / 2026"),
    ])
    d.text((48, y + 40), "Menyatakan bahwa siswa tersebut dinyatakan LULUS", font=font(22), fill=INK)
    img.save(f"{OUT}/skl.png")


def foto():
    img = Image.new("RGB", (900, 1200), TINT)
    d = ImageDraw.Draw(img)
    d.rectangle([250, 300, 650, 900], fill=(214, 219, 224))
    d.ellipse([390, 400, 510, 520], fill=(160, 168, 176))
    d.polygon([(300, 900), (450, 620), (600, 900)], fill=(160, 168, 176))
    d.text((48, 60), "PAS FOTO 3x4", font=font(36, True), fill=INK)
    d.text((48, 1080), "CONTOH - DATA FIKTIF", font=font(20, True), fill=(200, 60, 40))
    img.save(f"{OUT}/foto.png")


def prestasi():
    img, d = base("PIAGAM PRESTASI", "Juara 2 Olimpiade Sains tingkat kabupaten - contoh")
    y = rows(d, 190, [
        ("Nama", "Salsabila Maulida Husna"),
        ("Cabang", "Matematika"),
        ("Tingkat", "Kabupaten Sidoarjo"),
        ("Tahun", "2026"),
        ("Penyelenggara", "Dinas Pendidikan Kab. Sidoarjo"),
    ])
    img.save(f"{OUT}/prestasi.png")


def bukti():
    img = Image.new("RGB", (900, 1200), "white")
    d = ImageDraw.Draw(img)
    d.rectangle([0, 0, 900, 200], fill=(15, 76, 129))
    d.text((48, 44), "BUKTI TRANSFER", font=font(38, True), fill="white")
    d.text((48, 100), "Mobile Banking - contoh", font=font(20), fill=(206, 226, 240))
    d.text((48, 150), "Transaksi Berhasil", font=font(26, True), fill=(160, 230, 180))
    y = rows(d, 250, [
        ("Tanggal", "12 November 2026, 08:41"),
        ("Nominal", "Rp100.000"),
        ("Biaya Admin", "Rp0"),
        ("Bank Pengirim", "Bank Jatim"),
        ("Rekening Tujuan", "SMP PGRI 4 Waru - 0011 2233 4455"),
        ("No. Referensi", "2026111208410002"),
        ("Kode Invoice", "INV-20261112-0004"),
        ("Pengirim", "Surya Wijaya Kusuma"),
    ])
    d.rectangle([48, y + 30, 852, y + 150], outline=LINE)
    d.text((70, y + 60), "Catatan: dana masuk ke rekening sekolah,", font=font(22), fill=INK3)
    d.text((70, y + 92), "bukti ini dilampirkan pada portal PPDB.", font=font(22), fill=INK3)
    d.text((48, 1120), "CONTOH - DATA FIKTIF", font=font(20, True), fill=(200, 60, 40))
    img.save(f"{OUT}/bukti-transfer.png")


kk(); akta(); skl(); foto(); prestasi(); bukti()
print("mockdocs:", sorted(os.listdir(OUT)))