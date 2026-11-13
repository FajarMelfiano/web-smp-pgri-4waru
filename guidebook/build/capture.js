// Tangkapan layar guidebook: anotasi nomor langkah + sensor baris data nyata.
// Usage: node capture.js [--only=name1,name2]
const { chromium } = require("playwright");
const fs = require("fs");
const path = require("path");

const BASE = "http://127.0.0.1:8000";
const OUT = path.join(__dirname, "..", "screenshots");
fs.mkdirSync(OUT, { recursive: true });

const only = (process.argv.find((a) => a.startsWith("--only=")) || "").replace("--only=", "");
const want = only ? new Set(only.split(",")) : null;

const ADMIN = { email: "admin@smppgri4waru.id", pass: "password123" };
const STU = { id: "anindita.kirana@contoh.id", pass: "DemoSiswa2026" };
const DEMO = 29; // Anindita — semua tahap selesai
const LOCKED = 34; // Dimas — baru akun + jalur
const PAYPEND = 31; // Zahra — pembayaran menunggu verifikasi
const BERKASPART = 32; // Rizky — 2 dari 4 berkas

const misses = [];

async function draw(page, items, kind) {
  const scroll = await page.evaluate(() => ({ x: window.scrollX, y: window.scrollY }));
  await page.evaluate(
    ({ items, kind, scroll }) => {
      document.getElementById("gb-layer")?.remove();
      const layer = document.createElement("div");
      layer.id = "gb-layer";
      layer.style.cssText = "position:absolute;left:0;top:0;width:100%;height:100%;pointer-events:none;z-index:2147483647";
      for (const it of items) {
        const box = { l: it.x + scroll.x, t: it.y + scroll.y, w: it.width, h: it.height };
        if (kind === "mask") {
          const m = document.createElement("div");
          m.style.cssText = `position:absolute;left:${box.l}px;top:${box.t}px;width:${box.w}px;height:${box.h}px;background:#c9ccd1;background-image:repeating-linear-gradient(45deg,#c9ccd1 0 8px,#bfc3c8 8px 16px)`;
          layer.appendChild(m);
          continue;
        }
        if (it.ring !== false) {
          const r = document.createElement("div");
          r.style.cssText = `position:absolute;left:${box.l - 4}px;top:${box.t - 4}px;width:${box.w + 8}px;height:${box.h + 8}px;border:2px solid #0e5e8f;border-radius:4px`;
          layer.appendChild(r);
        }
        const D = 30;
        let cx = box.l - 14;
        let cy = box.t + box.h / 2 - D / 2;
        if (it.at === "right") cx = box.l + box.w + 2;
        if (it.at === "top") { cx = box.l + box.w / 2 - D / 2; cy = box.t - D - 2; }
        if (it.at === "inside-right") { cx = box.l + box.w - D - 6; cy = box.t + box.h / 2 - D / 2; }
        const c = document.createElement("div");
        c.textContent = String(it.n);
        c.style.cssText = `position:absolute;left:${cx}px;top:${cy}px;width:${D}px;height:${D}px;border-radius:50%;background:#0e5e8f;color:#fff;font:700 16px/30px system-ui,sans-serif;text-align:center;box-shadow:0 0 0 2px #fff`;
        layer.appendChild(c);
      }
      document.body.appendChild(layer);
    },
    { items, kind, scroll }
  );
}

async function resolve(page, list) {
  const out = [];
  for (const m of list) {
    const loc = m.sel ? page.locator(m.sel).first() : page.getByText(m.text, { exact: false }).first();
    try {
      await loc.waitFor({ state: "visible", timeout: 3000 });
    } catch {
      misses.push(`MISS ${m.text || m.sel}`);
      continue;
    }
    const bb = await loc.boundingBox();
    if (!bb) {
      misses.push(`NOBOX ${m.text || m.sel}`);
      continue;
    }
    out.push({ ...bb, n: m.n, at: m.at, ring: m.ring });
  }
  return out;
}

async function annotate(page, marks = [], masks = []) {
  const boxed = marks.length ? await resolve(page, marks) : [];
  if (boxed.length) await draw(page, boxed, "mark");
  if (masks.length) await draw(page, await resolve(page, masks), "mask");
  return boxed;
}

async function clear(page) {
  await page.evaluate(() => document.getElementById("gb-layer")?.remove());
}

async function shot(page, name, opts = {}) {
  if (want && !want.has(name)) return;
  await page.waitForTimeout(opts.wait ?? 450);
  const boxes = await annotate(page, opts.marks, opts.masks);
  const scroll = await page.evaluate(() => ({ x: window.scrollX, y: window.scrollY }));
  await page.screenshot({ path: `${OUT}/${name}.png`, fullPage: !!opts.full });
  await clear(page);
  fs.writeFileSync(
    `${OUT}/${name}.json`,
    JSON.stringify(
      {
        name,
        vp: page.viewportSize(),
        full: !!opts.full,
        scroll,
        boxes: boxes.map((b) => ({ n: b.n, x: b.x + scroll.x, y: b.y + scroll.y, width: b.width, height: b.height })),
      },
      null,
      1
    )
  );
  console.log("shot", name);
}

(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 2 });
  const page = await ctx.newPage();

  // ---------------- publik ----------------
  const pub = [
    ["pub-beranda", "/", { marks: [{ text: "Alur pendaftaran", n: 1 }, { text: "Kenali sekolah", n: 2 }] }],
    ["pub-beranda-modul", "/", {}],
    ["pub-ppdb", "/ppdb", { full: true }],
    ["pub-daftar", "/ppdb/daftar", {}],
    ["pub-daftar-identitas", "/ppdb/daftar", {}],
    ["pub-masuk", "/ppdb/masuk", {}],
    ["pub-lupa-sandi", "/ppdb/lupa-sandi", {}],
    ["pub-berita", "/berita", {}],
    ["pub-berita-detail", "/berita", {}],
    ["pub-profil", "/profil", {}],
    ["pub-akademik", "/akademik", {}],
    ["pub-kontak", "/kontak", {}],
  ];
  for (const [name, url, opts] of pub) {
    await page.goto(BASE + url, { waitUntil: "domcontentloaded" });
    await page.waitForTimeout(500);
    if (name === "pub-daftar") {
      await page.evaluate(() => window.scrollTo(0, 260));
      opts.marks = [
        { text: "Email Calon Siswa", n: 1, at: "left" },
        { text: "Kata Sandi", n: 2, at: "left" },
        { text: "Ulangi Kata Sandi", n: 3, at: "left" },
      ];
    }
    if (name === "pub-daftar-identitas") {
      await page.evaluate(() => window.scrollTo(0, 900));
      opts.marks = [];
    }
    if (name === "pub-masuk") {
      opts.marks = [
        { text: "Email atau 16 Digit NIK", n: 1, at: "left" },
        { text: "Kata Sandi", n: 2, at: "left" },
        { text: "Lupa kata sandi", n: 3, at: "right" },
      ];
    }
    if (name === "pub-berita-detail") {
      await page.getByText("Informasi Awal Tahun Ajaran", { exact: false }).first().click().catch(() => {});
      await page.waitForTimeout(900);
    }
    await shot(page, name, opts);
  }

  // galat login siswa (kata sandi salah)
  await page.goto(BASE + "/ppdb/masuk");
  await page.fill('input[name="identifier"]', STU.id);
  await page.fill('input[name="password"]', "salah-sekali");
  await Promise.all([page.waitForNavigation(), page.locator('form button[type="submit"]').last().click()]);
  await shot(page, "pub-masuk-galat", {});

  // ---------------- admin ----------------
  await page.goto(BASE + "/login");
  await page.waitForTimeout(400);
  await shot(page, "adm-login", {
    marks: [
      { text: "Email", n: 1, at: "left" },
      { text: "Kata Sandi", n: 2, at: "left" },
    ],
  });

  await page.fill('input[name="email"]', ADMIN.email);
  await page.fill('input[name="password"]', "salah-sekali");
  await Promise.all([page.waitForNavigation(), page.locator('form button[type="submit"]').last().click()]);
  await shot(page, "adm-login-galat", {});

  await page.goto(BASE + "/admin/lupa-sandi");
  await page.waitForTimeout(400);
  await shot(page, "adm-lupa-sandi", { marks: [{ text: "Email", n: 1, at: "left" }] });

  await page.goto(BASE + "/login");
  await page.fill('input[name="email"]', ADMIN.email);
  await page.fill('input[name="password"]', ADMIN.pass);
  await Promise.all([page.waitForNavigation(), page.locator('form button[type="submit"]').last().click()]);
  console.log("admin in:", page.url().replace(BASE, ""));

  await page.goto(BASE + "/admin", { waitUntil: "domcontentloaded" });
  await shot(page, "adm-ringkasan", {
    marks: [
      { text: "Total Pendaftar", n: 1, at: "top" },
      { text: "Akses Cepat Modul", n: 2, at: "left" },
    ],
  });
  await page.evaluate(() => window.scrollTo(0, 620));
  await shot(page, "adm-ringkasan-distribusi", {
    marks: [{ text: "Distribusi Status Pendaftar", n: 3, at: "left" }],
  });

  // daftar pendaftar
  await page.goto(BASE + "/admin/ppdb", { waitUntil: "domcontentloaded" });
  await shot(page, "adm-ppdb-list", {
    marks: [
      { sel: "#cari-online", n: 1, at: "left" },
      { text: "Saring Data", n: 2, at: "right" },
      { text: "Unduh CSV Online", n: 3, at: "right" },
    ],
  });
  await page.goto(BASE + "/admin/ppdb?status=diterima", { waitUntil: "domcontentloaded" });
  await shot(page, "adm-ppdb-saring", {
  });

  // tab lain pada modul PPDB
  await page.goto(BASE + "/admin/ppdb?tab=jalur", { waitUntil: "domcontentloaded" });
  await shot(page, "adm-ppdb-jalur", {
    marks: [
      { text: "Jalur & Gelombang PPDB", n: 1, at: "top" },
      { text: "Tambah Jalur", n: 2, at: "right" },
    ],
  });
  await page.goto(BASE + "/admin/ppdb?tab=pesan", { waitUntil: "domcontentloaded" });
  await shot(page, "adm-ppdb-pesan", {
  });

  // detail pendaftar demo
  for (const [id, tag] of [[DEMO, "diterima"], [PAYPEND, "bayar"]]) {
    await page.goto(`${BASE}/admin/ppdb/online/${id}`, { waitUntil: "domcontentloaded" });
    await shot(page, `adm-detail-${tag}`, { full: true });
    await page.evaluate(() => window.scrollTo(0, 300));
    await shot(page, `adm-detail-${tag}-seleksi`, {
      marks: [
        { text: "Status Seleksi Calon Siswa", n: 1, at: "left" },
        { text: "Catatan Resmi Panitia", n: 2, at: "left" },
        { text: "Simpan Status Seleksi", n: 3, at: "right" },
      ],
    });
    await page.evaluate(() => window.scrollTo(0, 1100));
    await shot(page, `adm-detail-${tag}-bayar`, {
      marks: [
        { sel: 'a[href*="/bukti"]', n: 1, at: "right" },
        { text: "Status Pembayaran", n: 2, at: "left" },
        { text: "Perbarui Status Pembayaran", n: 3, at: "right" },
      ],
    });
    await page.evaluate(() => {
      const h = [...document.querySelectorAll("h2")].find((e) => e.textContent.includes("Pemeriksaan Berkas"));
      h?.scrollIntoView({ block: "start" });
      window.scrollBy(0, -90);
    });
    await shot(page, `adm-detail-${tag}-berkas`, {
      marks: [
        { sel: '.adm-inline-form select', n: 1, at: "left" },
        { text: "Simpan", n: 3, at: "right" },
      ],
    });
    await page.evaluate(() => {
      const h = [...document.querySelectorAll("h2")].find((e) => e.textContent.includes("Biodata Lengkap"));
      h?.scrollIntoView({ block: "start" });
      window.scrollBy(0, -90);
    });
    await shot(page, `adm-detail-${tag}-biodata`, { full: true });
  }

  // berita
  await page.goto(BASE + "/admin/berita", { waitUntil: "domcontentloaded" });
  await shot(page, "adm-berita-daftar", {
    marks: [
      { text: "Buka di Studio", n: 1, at: "right" },
      { text: "Jadikan Draf", n: 2, at: "right" },
      { text: "Hapus", n: 3, at: "right" },
    ],
  });
  await page.evaluate(() => window.scrollTo(0, 0));
  await shot(page, "adm-berita-editor", {
    marks: [
      { text: "Judul Berita", n: 1, at: "left" },
      { text: "Visual", n: 2, at: "top" },
      { text: "Setelan Entri", n: 3, at: "left" },
    ],
  });
  await page.getByText("Pratinjau", { exact: true }).first().click().catch(() => misses.push("MISS tombol Pratinjau"));
  await shot(page, "adm-berita-pratinjau", {});
  await page.getByText("HTML", { exact: true }).first().click().catch(() => misses.push("MISS tombol HTML"));
  await shot(page, "adm-berita-html", {});
  await page.getByText("Visual", { exact: true }).first().click().catch(() => {});
  await page.evaluate(() => {
    const e = [...document.querySelectorAll("*")].find((x) => x.children.length === 0 && x.textContent.trim() === "Gambar Sampul");
    e?.scrollIntoView({ block: "center" });
  });
  await shot(page, "adm-berita-sampul", {
    marks: [
      { text: "Gambar Sampul", n: 1, at: "left" },
      { text: "Ringkasan", n: 2, at: "left" },
    ],
  });

  const pages2 = [
    ["adm-galeri", "/admin/galeri", { marks: [{ text: "Unggah", n: 1, at: "right" }] }],
    ["adm-alumni", "/admin/alumni", {}],
    ["adm-akademik", "/admin/akademik", {}],
    ["adm-pengaturan", "/admin/pengaturan", { full: true }],
    ["adm-petugas", "/admin/petugas", { marks: [{ text: "Tambah", n: 1, at: "right" }] }],
    ["adm-sandi-petugas", "/admin/petugas/2/sandi", {}],
    ["adm-akun", "/admin/akun", {}],
  ];
  for (const [name, url, opts] of pages2) {
    await page.goto(BASE + url, { waitUntil: "domcontentloaded" });
    await shot(page, name, opts);
  }

  await page.goto(BASE + "/admin/akun", { waitUntil: "domcontentloaded" });
  await page.fill('input[name="current_password"]', "salah-sekali").catch(() => misses.push("MISS current_password"));
  await page.locator('form button[type="submit"]').last().click().catch(() => {});
  await page.waitForTimeout(700);
  await shot(page, "adm-akun-galat", {});

  // ---------------- portal siswa ----------------
  await ctx.clearCookies();
  await page.goto(BASE + "/ppdb/masuk");
  await page.fill('input[name="identifier"]', STU.id);
  await page.fill('input[name="password"]', STU.pass);
  await Promise.all([page.waitForNavigation(), page.locator('form button[type="submit"]').last().click()]);
  console.log("siswa in:", page.url().replace(BASE, ""));

  const portal = [
    ["por-beranda", "/ppdb/portal", {}],
    ["por-jalur", "/ppdb/portal/jalur", {}],
    ["por-pembayaran", "/ppdb/portal/pembayaran", {}],
    ["por-data-diri", "/ppdb/portal/data-diri", {}],
    ["por-berkas", "/ppdb/portal/berkas", {}],
    ["por-kuesioner", "/ppdb/portal/kuesioner", {}],
    ["por-kartu", "/ppdb/portal/kartu-peserta", { full: true }],
    ["por-pengumuman", "/ppdb/portal/pengumuman", {}],
    ["por-bantuan", "/ppdb/portal/bantuan", {}],
    ["por-akun", "/ppdb/portal/akun", {}],
  ];
  for (const [name, url, opts] of portal) {
    await page.goto(BASE + url, { waitUntil: "domcontentloaded" });
    await shot(page, name, opts);
  }
  await page.goto(BASE + "/ppdb/portal", { waitUntil: "domcontentloaded" });
  await shot(page, "por-beranda-tahap", {
    marks: [
      { sel: ".ppdb-flow-card:nth-child(1) .ppdb-flow-card__num", n: 1, at: "left" },
      { sel: ".ppdb-flow-card:nth-child(2) .ppdb-flow-card__num", n: 2, at: "left" },
      { sel: ".ppdb-flow-card:nth-child(3) .ppdb-flow-card__num", n: 3, at: "left" },
      { sel: ".ppdb-flow-card:nth-child(4) .ppdb-flow-card__num", n: 4, at: "left" },
      { sel: ".ppdb-flow-card:nth-child(5) .ppdb-flow-card__num", n: 5, at: "left" },
      { sel: ".ppdb-flow-card:nth-child(6) .ppdb-flow-card__num", n: 6, at: "left" },
    ],
  });

  // jalur belum lengkap (Dimas)
  await ctx.clearCookies();
  await page.goto(BASE + "/ppdb/masuk");
  await page.fill('input[name="identifier"]', "dimas.aditya@contoh.id");
  await page.fill('input[name="password"]', "DemoSiswa2026");
  await Promise.all([page.waitForNavigation(), page.locator('form button[type="submit"]').last().click()]);
  await page.goto(BASE + "/ppdb/portal", { waitUntil: "domcontentloaded" });
  await shot(page, "por-beranda-terkunci", {});
  await page.goto(BASE + "/ppdb/portal/berkas", { waitUntil: "domcontentloaded" });
  await shot(page, "por-berkas-kosong", {});

  // ---------------- versi HP ----------------
  const m = await b.newContext({
    viewport: { width: 390, height: 844 },
    deviceScaleFactor: 2,
    isMobile: true,
    hasTouch: true,
    userAgent:
      "Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1",
  });
  const mp = await m.newPage();
  for (const [name, url] of [
    ["mob-beranda", "/"],
    ["mob-ppdb", "/ppdb"],
    ["mob-daftar", "/ppdb/daftar"],
    ["mob-berita", "/berita"],
  ]) {
    await mp.goto(BASE + url, { waitUntil: "domcontentloaded" });
    await shot(mp, name, {});
  }
  await mp.goto(BASE + "/ppdb/masuk");
  await mp.fill('input[name="identifier"]', STU.id);
  await mp.fill('input[name="password"]', STU.pass);
  await Promise.all([mp.waitForNavigation(), mp.locator('form button[type="submit"]').last().click()]);
  for (const [name, url] of [
    ["mob-portal", "/ppdb/portal"],
    ["mob-portal-berkas", "/ppdb/portal/berkas"],
  ]) {
    await mp.goto(BASE + url, { waitUntil: "domcontentloaded" });
    await shot(mp, name, {});
  }

  await b.close();
  console.log("\nmisses:\n" + (misses.length ? [...new Set(misses)].join("\n") : "none"));
})();