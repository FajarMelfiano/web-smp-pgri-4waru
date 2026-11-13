// Render out/index.html -> out/guidebook-smp-pgri-4waru-v1.pdf (cetak, bleed).
// Call: node topdf.js
const { chromium } = require("playwright");
const path = require("path");

const ROOT = path.join(__dirname, "..");
const FILE = "file://" + path.join(ROOT, "out", "index.html").replace(/\\/g, "/");
const OUT_PDF = path.join(ROOT, "out", "guidebook-smp-pgri-4waru-v1.pdf");

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.goto(FILE, { waitUntil: "networkidle" });
  // pastikan font terunduh
  await page.evaluate(() => document.fonts.ready);
  await page.pdf({
    path: OUT_PDF,
    width: "216mm",
    height: "303mm",
    printBackground: true,
    preferCSSPageSize: true,
    margin: { top: 0, right: 0, bottom: 0, left: 0 },
  });
  await browser.close();
  console.log("pdf:", OUT_PDF);
})().catch((e) => {
  console.error(e);
  process.exit(1);
});