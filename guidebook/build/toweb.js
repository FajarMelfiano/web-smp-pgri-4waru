// Render out/index.html -> out/guidebook-smp-pgri-4waru-v1-web.pdf
// Versi ringan untuk dibagikan lewat web: tanpa bleed, tanpa tanda potong.
const { chromium } = require("playwright");
const path = require("path");

const ROOT = path.join(__dirname, "..");
const FILE = "file://" + path.join(ROOT, "out", "index.html").replace(/\\/g, "/");
const OUT = path.join(ROOT, "out", "guidebook-smp-pgri-4waru-v1-web.pdf");

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  // sembunyikan tanda potong & area bleed via class tambahan tidak mudah; pakai
  // ukuran halaman trim dan overlay marks invisible dengan CSS injection.
  await page.goto(FILE, { waitUntil: "networkidle" });
  await page.evaluate(() => document.fonts.ready);
  await page.addStyleTag({ content: "@page{size:210mm 297mm!important;margin:0!important}.marks{display:none!important}.gb-page{width:210mm!important;height:297mm!important;padding:0!important;margin:0!important}" });
  await page.pdf({
    path: OUT,
    width: "210mm",
    height: "297mm",
    printBackground: true,
    preferCSSPageSize: true,
    margin: { top: 0, right: 0, bottom: 0, left: 0 },
  });
  await browser.close();
  console.log("web pdf:", OUT);
})().catch((e) => { console.error(e); process.exit(1); });