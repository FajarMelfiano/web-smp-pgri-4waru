// Capture all guidebook screenshots from the live app.
// Usage: node shoot-all.js [--mobile]
const { chromium } = require("playwright");
const BASE = "http://127.0.0.1:8000";
const OUT = "/home/melfiano/Documents/web-smp-pgri-4waru/guidebook/screenshots";
const fs = require("fs");
const mobile = process.argv.includes("--mobile");
const VP = mobile ? { width: 390, height: 844 } : { width: 1440, height: 900 };

(async () => {
  fs.mkdirSync(OUT, { recursive: true });
  const b = await chromium.launch();

  // ---------- public pages (no login) ----------
  const pub = await b.newPage({ viewport: VP, deviceScaleFactor: 2 });
  const pubList = [
    ["beranda-publik", "/", "full"],
    ["profil-publik", "/profil", "full"],
    ["akademik-publik", "/akademik", "full"],
    ["berita-publik", "/berita", "full"],
    ["alumni-publik", "/alumni", "full"],
    ["ppdb-publik", "/ppdb", "full"],
    ["kontak-publik", "/kontak", "full"],
    ["daftar-langkah1", "/ppdb/daftar", "full"],
    ["masuk-portal", "/ppdb/masuk", "full"],
  ];
  for (const [name, url] of pubList) {
    await pub.goto(BASE + url, { waitUntil: "domcontentloaded" });
    await pub.waitForTimeout(600);
    await pub.screenshot({ path: `${OUT}/${name}.png`, fullPage: url === "/" ? false : true });
    console.log("pub", name);
  }

  // ---------- admin ----------
  const adm = await b.newPage({ viewport: VP, deviceScaleFactor: 2 });
  await adm.goto(BASE + "/login");
  await adm.fill('input[name="email"]', process.env.ADMIN_EMAIL || "");
  await adm.fill('input[name="password"]', process.env.ADMIN_PASS || "");
  await Promise.all([adm.waitForNavigation(), adm.locator('form button[type="submit"]').last().click()]);

  const admList = [
    ["ringkasan", "/admin"],
    ["ppdb-daftar-pendaftar", "/admin/ppdb"],
    ["ppdb-detail", "/admin/ppdb/online/21"],
    ["galeri", "/admin/galeri"],
    ["alumni", "/admin/alumni"],
    ["akademik", "/admin/akademik"],
    ["pengaturan", "/admin/pengaturan"],
    ["petugas", "/admin/petugas"],
    ["sandi-petugas", "/admin/petugas/2/sandi"],
    ["akun", "/admin/akun"],
    ["berita-editor", "/admin/berita"],
  ];
  for (const [name, url] of admList) {
    await adm.goto(BASE + url, { waitUntil: "domcontentloaded" });
    await adm.waitForTimeout(600);
    await adm.screenshot({ path: `${OUT}/${name}.png`, fullPage: true });
    console.log("adm", name);
  }

  // ---------- portal siswa ----------
  const por = await b.newPage({ viewport: VP, deviceScaleFactor: 2 });
  await por.goto(BASE + "/ppdb/masuk");
  await por.fill('input[name="identifier"]', process.env.STU_ID || "");
  await por.fill('input[name="password"]', process.env.STU_PASS || "");
  await Promise.all([por.waitForNavigation(), por.locator('form button[type="submit"]').last().click()]);
  const porList = [
    ["portal-beranda", "/ppdb/portal"],
    ["portal-jalur", "/ppdb/portal/jalur"],
    ["portal-pembayaran", "/ppdb/portal/pembayaran"],
    ["portal-data-diri", "/ppdb/portal/data-diri"],
    ["portal-berkas", "/ppdb/portal/berkas"],
    ["portal-kuesioner", "/ppdb/portal/kuesioner"],
    ["portal-kartu", "/ppdb/portal/kartu-peserta"],
    ["portal-pengumuman", "/ppdb/portal/pengumuman"],
    ["portal-bantuan", "/ppdb/portal/bantuan"],
    ["portal-akun", "/ppdb/portal/akun"],
  ];
  for (const [name, url] of porList) {
    await por.goto(BASE + url, { waitUntil: "domcontentloaded" });
    await por.waitForTimeout(600);
    await por.screenshot({ path: `${OUT}/${name}.png`, fullPage: true });
    console.log("por", name);
  }

  await b.close();
  console.log("done");
})();