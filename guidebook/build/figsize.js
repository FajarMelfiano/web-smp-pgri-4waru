const { chromium } = require("playwright");
const path = require("path");
const FILE = "file://" + path.join(__dirname, "..", "out", "index.html");
(async () => {
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 700, height: 1200 } });
  await p.goto(FILE, { waitUntil: "networkidle" });
  await p.evaluate(() => document.fonts.ready);
  const rows = await p.evaluate(() =>
    Array.from(document.querySelectorAll(".gb-page")).map((sec, i) => {
      const fig = sec.querySelector(".fig img");
      const frame = sec.querySelector(".fig .frame");
      if (!fig) return { n: i + 1, fig: false };
      const fr = frame.getBoundingClientRect();
      return { n: i + 1, fig: true, frameW: Math.round(fr.width), frameH: Math.round(fr.height), imgH: Math.round(fig.getBoundingClientRect().height) };
    }).filter(r => r.fig)
  );
  console.log(rows.map(r => `p${r.n}: frame ${r.frameW}x${r.frameH}px imgH ${r.imgH}`).join("\n"));
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
