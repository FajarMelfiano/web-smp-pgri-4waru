const { chromium } = require("playwright");
const path = require("path");
const FILE = "file://" + path.join(__dirname, "..", "out", "index.html");
(async () => {
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 700, height: 1200 } });
  await p.goto(FILE, { waitUntil: "networkidle" });
  await p.evaluate(() => document.fonts.ready);
  const r = await p.evaluate(() =>
    Array.from(document.querySelectorAll(".gb-page")).map((sec, i) => {
      const trim = sec.querySelector(".trim");
      const steps = sec.querySelectorAll("ol.steps > li").length;
      const fig = sec.querySelector(".fig");
      const call = sec.querySelector(".callout");
      return { n: i + 1, steps, fig: !!fig, call: !!call };
    })
  );
  console.log(r.filter(x => x.steps > 5).map(x => `p${x.n}: ${x.steps} steps`).join("\n"));
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
