/* SMP PGRI 4 Waru — interaksi.
   Semua modul mandiri; kalau elemennya tidak ada, modul dilewati. */

(function () {
  "use strict";

  var root = document.documentElement;
  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ---------- Tema ---------- */

  var themeBtn = document.querySelector("[data-theme-toggle]");
  var themeLabel = themeBtn && themeBtn.querySelector("[data-theme-label]");

  function paintTheme() {
    if (!themeBtn) return;
    var dark = root.getAttribute("data-theme") === "dark";
    themeBtn.setAttribute("aria-pressed", dark ? "true" : "false");
    if (themeLabel) themeLabel.textContent = dark ? "Terang" : "Gelap";
    themeBtn.setAttribute(
      "aria-label",
      dark ? "Ganti ke tampilan terang" : "Ganti ke tampilan gelap"
    );
  }

  if (themeBtn) {
    paintTheme();
    themeBtn.addEventListener("click", function () {
      var next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
      root.setAttribute("data-theme", next);
      try {
        localStorage.setItem("smp4-theme", next);
      } catch (err) {}
      paintTheme();
    });
  }

  /* ---------- Navigasi mobile ---------- */

  var navToggle = document.querySelector("[data-nav-toggle]");
  var nav = document.getElementById("nav");
  var navLabel = navToggle && navToggle.querySelector("[data-nav-label]");

  function setNavLabel(text) {
    if (navLabel) navLabel.textContent = text;
    else if (navToggle) navToggle.textContent = text;
  }

  function closeNav(returnFocus) {
    if (!navToggle || !nav) return;
    nav.classList.remove("is-open");
    navToggle.setAttribute("aria-expanded", "false");
    setNavLabel("Menu");
    if (returnFocus) navToggle.focus();
  }

  if (navToggle && nav) {
    navToggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      navToggle.setAttribute("aria-expanded", open ? "true" : "false");
      setNavLabel(open ? "Tutup" : "Menu");
    });

    nav.addEventListener("click", function (event) {
      if (event.target.closest("a")) closeNav(false);
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && nav.classList.contains("is-open")) {
        closeNav(true);
      }
    });
  }

  /* ---------- Kepala saat digulir ---------- */

  var head = document.querySelector(".site-head");
  var sentinel = document.getElementById("top-sentinel");

  if (head && sentinel && "IntersectionObserver" in window) {
    new IntersectionObserver(function (entries) {
      head.classList.toggle("is-stuck", !entries[0].isIntersecting);
    }).observe(sentinel);
  }

  /* ---------- Kemunculan ---------- */

  var rises = document.querySelectorAll(".rise");

  if (rises.length && "IntersectionObserver" in window && !reduceMotion) {
    var riseIO = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add("is-in");
          riseIO.unobserve(entry.target);
        });
      },
      { rootMargin: "0px 0px -10% 0px" }
    );
    [].forEach.call(rises, function (el) {
      riseIO.observe(el);
    });
  } else {
    [].forEach.call(rises, function (el) {
      el.classList.add("is-in");
    });
  }

  /* ---------- Penjelajah mata pelajaran ---------- */

  var filterBar = document.querySelector("[data-filters]");
  var indexList = document.querySelector("[data-index]");

  if (filterBar && indexList) {
    var items = [].slice.call(indexList.querySelectorAll("li[data-cat]"));
    var status = document.querySelector("[data-index-status]");
    var buttons = [].slice.call(filterBar.querySelectorAll("[data-filter]"));

    var applyFilter = function (cat) {
      var shown = 0;

      items.forEach(function (item) {
        var match = cat === "semua" || item.getAttribute("data-cat") === cat;
        item.hidden = !match;
        if (match) shown += 1;
      });

      buttons.forEach(function (btn) {
        btn.setAttribute(
          "aria-pressed",
          btn.getAttribute("data-filter") === cat ? "true" : "false"
        );
      });

      if (status) {
        status.textContent =
          cat === "semua"
            ? "Menampilkan " + shown + " mata pelajaran."
            : "Menampilkan " + shown + " mata pelajaran pada kelompok ini.";
      }
    };

    filterBar.addEventListener("click", function (event) {
      var btn = event.target.closest("[data-filter]");
      if (!btn) return;
      applyFilter(btn.getAttribute("data-filter"));
    });

    applyFilter("semua");
  }

  /* ---------- Peta setelah diminta ---------- */

  [].forEach.call(document.querySelectorAll("[data-map-hold]"), function (hold) {
    hold.addEventListener("click", function () {
      var frame = hold.parentElement;
      var src = hold.getAttribute("data-src");
      if (!frame || !src) return;

      var iframe = document.createElement("iframe");
      iframe.title = hold.getAttribute("data-title") || "Peta lokasi sekolah";
      iframe.src = src;
      iframe.loading = "lazy";
      iframe.referrerPolicy = "no-referrer-when-downgrade";
      frame.replaceChild(iframe, hold);
    });
  });

  /* ---------- Formulir PPDB (3 langkah) ---------- */

  var form = document.getElementById("ppdb-form");

  if (form) {
    var fieldsets = [].slice.call(form.querySelectorAll("[data-step]"));
    var bars = [].slice.call(form.querySelectorAll("[data-form-bars] span"));
    var stepLabel = form.querySelector("[data-step-label]");
    var reviewBody = form.querySelector("[data-review]");
    var status = document.getElementById("form-status");
    var summary = document.getElementById("form-summary");
    var mailLink = document.getElementById("form-mail");
    var copyBtn = document.getElementById("copy-btn");
    var copyNote = document.getElementById("copy-note");
    var nextBtns = [].slice.call(form.querySelectorAll("[data-next]"));
    var backBtns = [].slice.call(form.querySelectorAll("[data-back]"));
    var current = 0;
    var readyToSend = false;
    var message = "";

    var rules = [
      { id: "siswa", step: 0, message: "Tulis nama calon siswa." },
      { id: "asal", step: 0, message: "Tulis nama SD asal." },
      { id: "wali", step: 1, message: "Tulis nama orang tua atau wali." },
      {
        id: "wa",
        step: 1,
        message: "Tulis nomor WhatsApp, 9 sampai 15 angka.",
        test: function (value) {
          return /^[0-9]{9,15}$/.test(value.replace(/[\s\-()+]/g, ""));
        }
      }
    ];

    function fieldOf(id) {
      var input = document.getElementById(id);
      return input ? input.closest(".field") : null;
    }

    function setError(id, msg) {
      var field = fieldOf(id);
      var input = document.getElementById(id);
      if (!field || !input) return;
      var slot = field.querySelector(".error");
      field.classList.toggle("has-error", Boolean(msg));
      input.setAttribute("aria-invalid", msg ? "true" : "false");
      if (slot) slot.textContent = msg || "";
    }

    function valueOf(id) {
      var el = document.getElementById(id);
      return el ? el.value.trim() : "";
    }

    function validateStep(index) {
      var firstBad = null;

      rules.forEach(function (rule) {
        if (rule.step !== index) return;
        var value = valueOf(rule.id);
        var ok = value.length > 0 && (!rule.test || rule.test(value));
        setError(rule.id, ok ? "" : rule.message);
        if (!ok && !firstBad) firstBad = document.getElementById(rule.id);
      });

      return firstBad;
    }

    function paintStep() {
      fieldsets.forEach(function (set, i) {
        set.hidden = i !== current;
      });
      bars.forEach(function (bar, i) {
        bar.classList.toggle("is-on", i <= current);
      });
      if (stepLabel) {
        stepLabel.textContent = "Langkah " + (current + 1) + " dari " + fieldsets.length;
      }
      if (current === fieldsets.length - 1) buildReview();
    }

    function buildReview() {
      if (!reviewBody) return;
      reviewBody.innerHTML = "";

      [
        ["Nama calon siswa", valueOf("siswa")],
        ["Sekolah dasar asal", valueOf("asal")],
        ["Orang tua atau wali", valueOf("wali")],
        ["WhatsApp", valueOf("wa")],
        ["Pertanyaan", valueOf("catatan") || "Tidak ada"]
      ].forEach(function (pair) {
        var row = document.createElement("div");
        var dt = document.createElement("dt");
        var dd = document.createElement("dd");
        dt.textContent = pair[0];
        dd.textContent = pair[1];
        row.appendChild(dt);
        row.appendChild(dd);
        reviewBody.appendChild(row);
      });
    }

    rules.forEach(function (rule) {
      var input = document.getElementById(rule.id);
      if (!input) return;
      input.addEventListener("input", function () {
        var field = input.closest(".field");
        if (field && field.classList.contains("has-error")) {
          setError(rule.id, input.value.trim() ? "" : rule.message);
        }
      });
    });

    nextBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        var bad = validateStep(current);
        if (bad) {
          bad.focus();
          return;
        }
        if (current < fieldsets.length - 1) {
          current += 1;
          paintStep();
          var legend = fieldsets[current].querySelector("legend");
          if (legend) legend.focus();
          form.scrollIntoView({ block: "nearest" });
        }
      });
    });

    backBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (current > 0) {
          current -= 1;
          paintStep();
        }
      });
    });

    form.addEventListener("submit", function (event) {
      event.preventDefault();

      var bad = null;
      fieldsets.forEach(function (set, i) {
        var found = validateStep(i);
        if (found && !bad) bad = found;
      });

      if (bad) {
        current = rules.filter(function (r) {
          return document.getElementById(r.id) === bad;
        })[0].step;
        paintStep();
        bad.focus();
        return;
      }

      if (readyToSend) {
        status.hidden = false;
        status.focus();
        return;
      }

      var siswa = valueOf("siswa");
      var asal = valueOf("asal");
      var wali = valueOf("wali");
      var wa = valueOf("wa");
      var catatan = valueOf("catatan");

      message =
        "Pendaftaran siswa baru SMP PGRI 4 Waru\n" +
        "Nama calon siswa: " + siswa + "\n" +
        "Asal SD: " + asal + "\n" +
        "Nama orang tua/wali: " + wali + "\n" +
        "WhatsApp: " + wa +
        (catatan ? "\nCatatan: " + catatan : "");

      summary.textContent = message;
      mailLink.href =
        "mailto:smp.pgri4waru@gmail.com?subject=" +
        encodeURIComponent("Pendaftaran siswa baru: " + siswa) +
        "&body=" +
        encodeURIComponent(message);

      if (copyNote) copyNote.textContent = "";
      readyToSend = true;

      if (window.fetch && form.action) {
        try {
          window.fetch(form.action, {
            method: "POST",
            headers: { Accept: "application/json" },
            body: new FormData(form)
          }).catch(function () {});
        } catch (err) {}
      }

      fieldsets.forEach(function (set) {
        set.hidden = true;
      });
      bars.forEach(function (bar) {
        bar.classList.add("is-on");
      });
      if (stepLabel) stepLabel.textContent = "Pesan tersusun";

      status.hidden = false;
      status.focus();
      status.scrollIntoView({ block: "nearest" });
    });

    var editBtn = form.querySelector("[data-edit]");

    if (editBtn) {
      editBtn.addEventListener("click", function () {
        readyToSend = false;
        status.hidden = true;
        current = 0;
        paintStep();
        var first = document.getElementById("siswa");
        if (first) first.focus();
      });
    }

    if (copyBtn) {
      copyBtn.addEventListener("click", function () {
        var done = function () {
          if (copyNote) copyNote.textContent = "Pesan disalin. Tempel di WhatsApp atau email.";
          copyBtn.textContent = "Tersalin";
          window.setTimeout(function () {
            copyBtn.textContent = "Salin pesan";
          }, 2400);
        };

        var fallback = function () {
          var box = document.createElement("textarea");
          box.value = message;
          box.setAttribute("readonly", "");
          box.style.position = "fixed";
          box.style.top = "-1000px";
          document.body.appendChild(box);
          box.select();
          try {
            document.execCommand("copy");
            done();
          } catch (err) {
            if (copyNote) {
              copyNote.textContent =
                "Peramban menolak menyalin. Pilih teks pesan lalu salin manual.";
            }
          }
          document.body.removeChild(box);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(message).then(done, fallback);
        } else {
          fallback();
        }
      });
    }

    paintStep();
  }

  /* ---------- Cetak ---------- */

  [].forEach.call(document.querySelectorAll("[data-print]"), function (btn) {
    btn.addEventListener("click", function () {
      window.print();
    });
  });
})();
