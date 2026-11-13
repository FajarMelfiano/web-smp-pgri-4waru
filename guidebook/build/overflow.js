// Check each .gb-page .trim content height vs trim height (overflow detection)
const { chromium } = require("playwright");
const path = require("path");
const FILE = "file://" + path.join(__dirname, "..", "out", "index.html");
(async () => {
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 700, height: 1200 } });
  await p.goto(FILE, { waitUntil: "networkidle" });
  await p.evaluate(() => document.fonts.ready);
  const over = await p.evaluate(() => {
    const out = [];
    document.querySelectorAll(".gb-page").forEach((sec, i) => {
      const trim = sec.querySelector(".trim");
      const sh = trim.scrollHeight, h = trim.offsetHeight;
      // exclude the last content block's bottom padding; check inner overhang
      if (sh > h + 1) out.push({ n: i + 1, scroll: sh, fit: h, over: sh - h });
    });
    return out;
  });
  console.log("overflowing:", JSON.stringify(over, null, 1));
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
