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
      { rootMargin: "0px 0px -2% 0px", threshold: 0.01 }
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
    var subjectSearch = document.querySelector("[data-subject-search]");
    var activeSubjectCat = "semua";

    var applyFilter = function (cat) {
      if (cat) activeSubjectCat = cat;
      var q = subjectSearch ? subjectSearch.value.trim().toLowerCase() : "";
      var shown = 0;

      items.forEach(function (item) {
        var catMatch =
          activeSubjectCat === "semua" ||
          item.getAttribute("data-cat") === activeSubjectCat;
        var textMatch =
          !q || (item.textContent || "").toLowerCase().indexOf(q) !== -1;
        var match = catMatch && textMatch;
        item.hidden = !match;
        if (match) shown += 1;
      });

      buttons.forEach(function (btn) {
        btn.setAttribute(
          "aria-pressed",
          btn.getAttribute("data-filter") === activeSubjectCat ? "true" : "false"
        );
      });

      if (status) {
        status.textContent =
          activeSubjectCat === "semua" && !q
            ? "Menampilkan " + shown + " mata pelajaran."
            : "Menampilkan " + shown + " mata pelajaran sesuai saringan.";
      }
    };

    filterBar.addEventListener("click", function (event) {
      var btn = event.target.closest("[data-filter]");
      if (!btn) return;
      applyFilter(btn.getAttribute("data-filter"));
    });

    if (subjectSearch) {
      subjectSearch.addEventListener("input", function () {
        applyFilter();
      });
    }

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

  /* ---------- Saringan Kategori Galeri Foto ---------- */

  [].forEach.call(
    document.querySelectorAll("[data-gallery-scope]"),
    function (toolbar) {
      var scope = toolbar.getAttribute("data-gallery-scope");
      var grid = document.querySelector('[data-gallery-grid="' + scope + '"]');
      if (!grid) return;

      var cards = [].slice.call(grid.querySelectorAll("[data-gallery-item]"));
      var pills = [].slice.call(toolbar.querySelectorAll("[data-gallery-filter]"));

      toolbar.addEventListener("click", function (event) {
        var btn = event.target.closest("[data-gallery-filter]");
        if (!btn) return;
        var cat = btn.getAttribute("data-gallery-filter");

        pills.forEach(function (p) {
          p.setAttribute("aria-pressed", p === btn ? "true" : "false");
        });

        cards.forEach(function (card) {
          var match = cat === "semua" || card.getAttribute("data-cat") === cat;
          card.hidden = !match;
        });
      });
    }
  );

  /* ---------- Lightbox Pratinjau Foto Interaktif ---------- */

  var lightbox = document.getElementById("photo-lightbox");
  if (lightbox) {
    var lbImg = document.getElementById("lightbox-img");
    var lbTitle = document.getElementById("lightbox-title");
    var lbDesc = document.getElementById("lightbox-desc");
    var lbTag = document.getElementById("lightbox-tag");
    var lbCounter = document.getElementById("lightbox-counter");
    var lbStage = document.getElementById("lightbox-stage");
    var lbPrev = lightbox.querySelector("[data-lightbox-prev]");
    var lbNext = lightbox.querySelector("[data-lightbox-next]");
    var lbCloses = [].slice.call(lightbox.querySelectorAll("[data-lightbox-close]"));

    var activeTriggers = [];
    var lbIndex = 0;
    var lastFocused = null;

    var collectVisibleTriggers = function (clickedTrigger) {
      var parentGrid = clickedTrigger.closest("[data-gallery-grid]");
      var scopeRoot = parentGrid || document;
      var all = [].slice.call(scopeRoot.querySelectorAll("[data-lightbox-trigger]"));
      return all.filter(function (el) {
        var card = el.closest("[data-gallery-item]");
        return !card || !card.hidden;
      });
    };

    var renderLightbox = function () {
      if (!activeTriggers.length) return;
      var item = activeTriggers[lbIndex];
      if (!item) return;

      var src = item.getAttribute("data-src") || "";
      var title = item.getAttribute("data-title") || "";
      var desc = item.getAttribute("data-desc") || "";
      var tag = item.getAttribute("data-tag") || "Dokumentasi";

      if (lbImg) {
        lbImg.src = src;
        lbImg.alt = title;
      }
      if (lbTitle) lbTitle.textContent = title;
      if (lbDesc) {
        lbDesc.textContent = desc;
        lbDesc.hidden = !desc;
      }
      if (lbTag) lbTag.textContent = tag;
      if (lbCounter) {
        lbCounter.textContent = lbIndex + 1 + " / " + activeTriggers.length;
      }
    };

    var openLightbox = function (trigger) {
      activeTriggers = collectVisibleTriggers(trigger);
      lbIndex = Math.max(0, activeTriggers.indexOf(trigger));
      lastFocused = trigger;
      if (lbPrev) lbPrev.hidden = activeTriggers.length <= 1;
      if (lbNext) lbNext.hidden = activeTriggers.length <= 1;
      if (lbCounter) lbCounter.hidden = activeTriggers.length <= 1;
      renderLightbox();
      lightbox.hidden = false;
      document.body.classList.add("has-lightbox");
      var closeBtn = lightbox.querySelector(".lightbox__close");
      if (closeBtn) closeBtn.focus();
    };

    var closeLightbox = function () {
      lightbox.hidden = true;
      document.body.classList.remove("has-lightbox");
      if (lastFocused && typeof lastFocused.focus === "function") {
        lastFocused.focus();
      }
    };

    var stepLightbox = function (delta) {
      if (!activeTriggers.length) return;
      lbIndex = (lbIndex + delta + activeTriggers.length) % activeTriggers.length;
      renderLightbox();
    };

    document.addEventListener("click", function (event) {
      var trigger = event.target.closest("[data-lightbox-trigger]");
      if (!trigger) return;
      event.preventDefault();
      openLightbox(trigger);
    });

    lbCloses.forEach(function (btn) {
      btn.addEventListener("click", closeLightbox);
    });

    if (lbPrev) {
      lbPrev.addEventListener("click", function () {
        stepLightbox(-1);
      });
    }

    if (lbNext) {
      lbNext.addEventListener("click", function () {
        stepLightbox(1);
      });
    }

    document.addEventListener("keydown", function (event) {
      if (lightbox.hidden) return;
      if (event.key === "Escape") {
        closeLightbox();
      } else if (event.key === "ArrowLeft") {
        stepLightbox(-1);
      } else if (event.key === "ArrowRight") {
        stepLightbox(1);
      }
    });

    if (lbStage) {
      var touchStartX = 0;
      lbStage.addEventListener(
        "touchstart",
        function (e) {
          if (e.changedTouches && e.changedTouches[0]) {
            touchStartX = e.changedTouches[0].clientX;
          }
        },
        { passive: true }
      );

      lbStage.addEventListener(
        "touchend",
        function (e) {
          if (!e.changedTouches || !e.changedTouches[0]) return;
          var dx = e.changedTouches[0].clientX - touchStartX;
          if (Math.abs(dx) > 42) {
            stepLightbox(dx < 0 ? 1 : -1);
          }
        },
        { passive: true }
      );
    }
  }

  /* ---------- Pencarian Cepat Berita ---------- */

  var newsSearchInput = document.querySelector("[data-news-search]");
  if (newsSearchInput) {
    var newsItems = [].slice.call(document.querySelectorAll("[data-news-item]"));
    var newsStatus = document.getElementById("news-search-status");
    var newsEmpty = document.getElementById("news-empty-state");

    newsSearchInput.addEventListener("input", function () {
      var q = newsSearchInput.value.trim().toLowerCase();
      var count = 0;

      newsItems.forEach(function (card) {
        var hay = (card.getAttribute("data-search") || "").toLowerCase();
        var match = !q || hay.indexOf(q) !== -1;
        card.hidden = !match;
        if (match) count += 1;
      });

      if (newsStatus) {
        newsStatus.textContent = "Menampilkan " + count + " berita";
      }
      if (newsEmpty) {
        newsEmpty.hidden = count > 0;
      }
    });
  }

  /* ---------- Saringan & Pratinjau Langsung Alumni ---------- */

  var alumniWrap = document.querySelector("[data-alumni-filter-wrap]");
  if (alumniWrap) {
    var alumniCards = [].slice.call(document.querySelectorAll("[data-alumni-item]"));
    var cohortBtns = [].slice.call(alumniWrap.querySelectorAll("[data-alumni-cohort]"));
    var alumniSearch = alumniWrap.querySelector("[data-alumni-search]");
    var alumniStatus = document.getElementById("alumni-filter-status");
    var alumniEmpty = document.getElementById("alumni-empty-state");
    var currentCohort = "semua";

    var filterAlumni = function () {
      var q = alumniSearch ? alumniSearch.value.trim().toLowerCase() : "";
      var shown = 0;

      alumniCards.forEach(function (card) {
        var cMatch =
          currentCohort === "semua" ||
          card.getAttribute("data-cohort") === currentCohort;
        var sMatch =
          !q ||
          (card.getAttribute("data-search") || "").toLowerCase().indexOf(q) !== -1;
        var ok = cMatch && sMatch;
        card.hidden = !ok;
        if (ok) shown += 1;
      });

      if (alumniStatus) {
        alumniStatus.textContent = "Menampilkan " + shown + " testimoni alumni.";
      }
      if (alumniEmpty) {
        alumniEmpty.hidden = shown > 0;
      }
    };

    alumniWrap.addEventListener("click", function (event) {
      var btn = event.target.closest("[data-alumni-cohort]");
      if (!btn) return;
      currentCohort = btn.getAttribute("data-alumni-cohort");
      cohortBtns.forEach(function (b) {
        b.setAttribute("aria-pressed", b === btn ? "true" : "false");
      });
      filterAlumni();
    });

    if (alumniSearch) {
      alumniSearch.addEventListener("input", filterAlumni);
    }
  }

  var alumniTextarea = document.querySelector("[data-alumni-textarea]");
  if (alumniTextarea) {
    var namaIn = document.getElementById("nama");
    var angkatanIn = document.getElementById("angkatan");
    var pekerjaanIn = document.getElementById("pekerjaan");
    var charCount = document.getElementById("ulasan-char-count");
    var prevQuote = document.getElementById("preview-alumni-quote");
    var prevName = document.getElementById("preview-alumni-name");
    var prevMeta = document.getElementById("preview-alumni-meta");
    var prevAvatar = document.getElementById("preview-alumni-avatar");

    var syncAlumniPreview = function () {
      var val = alumniTextarea.value.trim();
      var nama = namaIn && namaIn.value.trim() ? namaIn.value.trim() : "Nama Alumni";
      var angkatan =
        angkatanIn && angkatanIn.value.trim() ? angkatanIn.value.trim() : "Tahun Lulus";
      var pekerjaan =
        pekerjaanIn && pekerjaanIn.value.trim() ? pekerjaanIn.value.trim() : "";

      if (charCount) {
        charCount.textContent = alumniTextarea.value.length + " / 1200";
      }
      if (prevQuote) {
        prevQuote.textContent =
          "“" +
          (val ||
            "Tuliskan kesan dan cerita masa sekolah Anda di atas untuk melihat tampilannya di sini.") +
          "”";
      }
      if (prevName) prevName.textContent = nama;
      if (prevMeta) {
        prevMeta.textContent =
          "Angkatan " + angkatan + (pekerjaan ? " · " + pekerjaan : "");
      }
      if (prevAvatar) {
        prevAvatar.textContent = nama.charAt(0).toUpperCase() || "A";
      }
    };

    [alumniTextarea, namaIn, angkatanIn, pekerjaanIn].forEach(function (el) {
      if (el) el.addEventListener("input", syncAlumniPreview);
    });
    syncAlumniPreview();
  }

  /* ---------- Ceklis Kesiapan Dokumen PPDB ---------- */

  var docChecklist = document.getElementById("ppdb-doc-checklist");
  if (docChecklist) {
    var docInputs = [].slice.call(docChecklist.querySelectorAll(".doc-check-input"));
    var docLabel = document.getElementById("doc-check-label");
    var docFill = document.getElementById("doc-check-fill");
    var docReset = document.getElementById("doc-check-reset");
    var storageKey = "smp4-ppdb-docs";

    var loadSavedDocs = function () {
      try {
        var raw = localStorage.getItem(storageKey);
        return raw ? JSON.parse(raw) : {};
      } catch (e) {
        return {};
      }
    };

    var saveDocs = function (state) {
      try {
        localStorage.setItem(storageKey, JSON.stringify(state));
      } catch (e) {}
    };

    var savedState = loadSavedDocs();

    var paintDocs = function () {
      var total = docInputs.length;
      var ready = 0;
      var nextState = {};

      docInputs.forEach(function (input) {
        var item = input.closest(".doc-check-item");
        if (input.checked) {
          ready += 1;
          nextState[input.value] = true;
        }
        if (item) {
          item.classList.toggle("is-checked", input.checked);
        }
      });

      var pct = total > 0 ? Math.round((ready / total) * 100) : 0;
      if (docFill) docFill.style.width = pct + "%";
      if (docLabel) {
        docLabel.textContent =
          ready === total
            ? "Lengkap! 4 dari 4 berkas siap dibawa ke sekolah"
            : ready + " dari " + total + " berkas siap";
      }
      saveDocs(nextState);
    };

    docInputs.forEach(function (input) {
      if (savedState[input.value]) {
        input.checked = true;
      }
      input.addEventListener("change", paintDocs);
    });

    if (docReset) {
      docReset.addEventListener("click", function () {
        docInputs.forEach(function (input) {
          input.checked = false;
        });
        paintDocs();
      });
    }

    paintDocs();
  }

  /* ---------- Indikator Gulir & Tombol Kembali ke Atas ---------- */

  var scrollBar = document.getElementById("scroll-progress");
  var backTopBtn = document.getElementById("back-top");

  if (scrollBar || backTopBtn) {
    var updateScrollUI = function () {
      var scrollTop = window.scrollY || document.documentElement.scrollTop || 0;
      var docHeight =
        document.documentElement.scrollHeight -
        document.documentElement.clientHeight;
      var pct = docHeight > 0 ? Math.min(100, (scrollTop / docHeight) * 100) : 0;

      if (scrollBar) {
        scrollBar.style.transform = "scaleX(" + pct / 100 + ")";
      }
      if (backTopBtn) {
        backTopBtn.hidden = scrollTop < 380;
      }
    };

    window.addEventListener("scroll", updateScrollUI, { passive: true });
    updateScrollUI();

    if (backTopBtn) {
      backTopBtn.addEventListener("click", function () {
        window.scrollTo({
          top: 0,
          behavior: reduceMotion ? "auto" : "smooth"
        });
      });
    }
  }

  /* ---------- Tabel Responsif Otomatis (Label Kolom Mobile) ---------- */

  [].forEach.call(document.querySelectorAll(".adm-table"), function (table) {
    var headers = [].slice.call(table.querySelectorAll("thead th")).map(function (th) {
      return (th.textContent || "").trim();
    });
    if (!headers.length) return;

    [].forEach.call(table.querySelectorAll("tbody tr"), function (row) {
      var cells = [].slice.call(row.querySelectorAll("td"));
      if (cells.length === 1 && cells[0].hasAttribute("colspan")) return;
      cells.forEach(function (td, idx) {
        if (headers[idx] && !td.hasAttribute("data-label")) {
          td.setAttribute("data-label", headers[idx]);
        }
      });
    });
  });

  /* ---------- Sidebar Portal & Admin Mobile ---------- */

  var admToggle = document.querySelector("[data-adm-toggle]");
  var admSidebar = document.getElementById("adm-sidebar");
  var admBackdrop = document.querySelector("[data-adm-backdrop]");

  if (admToggle && admSidebar && !admToggle.hasAttribute("data-adm-bound")) {
    admToggle.setAttribute("data-adm-bound", "true");
    var setAdmOpen = function (open) {
      admSidebar.classList.toggle("is-open", open);
      if (admBackdrop) admBackdrop.classList.toggle("is-open", open);
      admToggle.setAttribute("aria-expanded", open ? "true" : "false");
    };
    admToggle.addEventListener("click", function () {
      setAdmOpen(!admSidebar.classList.contains("is-open"));
    });
    if (admBackdrop) {
      admBackdrop.addEventListener("click", function () {
        setAdmOpen(false);
      });
    }
  }

  /* ---------- Accordion Menu Pendaftaran (Portal Siswa) ---------- */

  [].forEach.call(document.querySelectorAll("[data-ppdb-acc]"), function (acc) {
    var btn = acc.querySelector("[data-ppdb-acc-toggle]");
    var subnav = acc.querySelector(".ppdb-subnav");
    if (!btn || !subnav) return;

    btn.addEventListener("click", function () {
      var open = acc.classList.toggle("is-open");
      subnav.hidden = !open;
      btn.setAttribute("aria-expanded", open ? "true" : "false");
    });
  });

  /* ---------- Modal Tata Cara Pendaftaran ---------- */

  var guideModal = document.getElementById("ppdb-guide-modal");
  if (guideModal) {
    try {
      sessionStorage.removeItem("smp4-ppdb-reg-draft");
    } catch (e) {}
    var guideOpens = [].slice.call(
      document.querySelectorAll("[data-ppdb-guide-open]")
    );
    var guideCloses = [].slice.call(
      guideModal.querySelectorAll("[data-ppdb-guide-close]")
    );
    var lastGuideTrigger = null;

    var openGuideModal = function (trigger) {
      lastGuideTrigger = trigger || document.activeElement;
      guideModal.hidden = false;
      var closeBtn = guideModal.querySelector("[data-ppdb-guide-close].icon-btn");
      if (closeBtn) closeBtn.focus();
    };

    var closeGuideModal = function () {
      guideModal.hidden = true;
      if (lastGuideTrigger && typeof lastGuideTrigger.focus === "function") {
        lastGuideTrigger.focus();
      }
    };

    guideOpens.forEach(function (btn) {
      btn.addEventListener("click", function () {
        openGuideModal(btn);
      });
    });

    guideCloses.forEach(function (btn) {
      btn.addEventListener("click", closeGuideModal);
    });

    document.addEventListener("keydown", function (event) {
      if (!guideModal.hidden && event.key === "Escape") {
        closeGuideModal();
      }
    });
  }

  /* ---------- Toggle Tampilkan / Sembunyikan Kata Sandi ---------- */

  [].forEach.call(
    document.querySelectorAll("[data-pw-toggle]"),
    function (btn) {
      btn.addEventListener("click", function () {
        var targetId = btn.getAttribute("data-pw-toggle");
        var input = document.getElementById(targetId);
        if (!input) return;

        var isPw = input.type === "password";
        input.type = isPw ? "text" : "password";

        var showIcon = btn.querySelector("[data-pw-icon-show]");
        var hideIcon = btn.querySelector("[data-pw-icon-hide]");
        if (showIcon) showIcon.hidden = isPw;
        if (hideIcon) hideIcon.hidden = !isPw;
        btn.setAttribute(
          "aria-label",
          isPw ? "Sembunyikan kata sandi" : "Tampilkan kata sandi"
        );
      });
    }
  );

  /* ---------- Registrasi Akun PPDB 2 Langkah & Aturan Sandi ---------- */

  var regForm = document.querySelector("[data-ppdb-register-form]");
  if (regForm) {
    var pane1 = regForm.querySelector('[data-reg-pane="1"]');
    var pane2 = regForm.querySelector('[data-reg-pane="2"]');
    var ind1 = document.querySelector('[data-reg-step-indicator="1"]');
    var ind2 = document.querySelector('[data-reg-step-indicator="2"]');
    var nextBtn = regForm.querySelector("[data-reg-next]");
    var prevBtns = regForm.querySelectorAll("[data-reg-prev]");
    var emailPreview = regForm.querySelector("[data-reg-email-preview]");
    var step1Url = regForm.getAttribute("data-step1-url");
    var csrfInput = regForm.querySelector('input[name="_token"]');

    var emailInput = document.getElementById("reg-email");
    var pwInput = document.getElementById("reg-password");
    var pwConfirmInput = document.getElementById("reg-password-confirm");
    var namaInput = document.getElementById("reg-nama");
    var tglLahirInput = document.getElementById("reg-tgl-lahir");
    var waInput = document.getElementById("reg-wa");
    var nikInput = document.getElementById("reg-nik");
    var DRAFT_KEY = "smp4-ppdb-reg-draft";

    var ruleLen = regForm.querySelector('[data-pw-rule="length"]');
    var ruleCase = regForm.querySelector('[data-pw-rule="case"]');
    var ruleDigit = regForm.querySelector('[data-pw-rule="digit"]');

    var setRegError = function (field, msg) {
      var errEl = regForm.querySelector('[data-field-error="' + field + '"]');
      if (errEl) errEl.textContent = msg || "";
    };

    var evalPwRules = function () {
      var val = pwInput ? pwInput.value : "";
      var okLen = val.length >= 8 && val.length <= 16;
      var okCase = /[a-z]/.test(val) && /[A-Z]/.test(val);
      var okDigit = /[0-9]/.test(val);

      if (ruleLen) ruleLen.classList.toggle("is-valid", okLen);
      if (ruleCase) ruleCase.classList.toggle("is-valid", okCase);
      if (ruleDigit) ruleDigit.classList.toggle("is-valid", okDigit);

      return okLen && okCase && okDigit;
    };

    var saveLocalDraft = function () {
      try {
        sessionStorage.setItem(
          DRAFT_KEY,
          JSON.stringify({
            email: emailInput ? emailInput.value.trim() : "",
            nama: namaInput ? namaInput.value : "",
            tglLahir: tglLahirInput ? tglLahirInput.value : "",
            wa: waInput ? waInput.value : "",
            nik: nikInput ? nikInput.value : "",
          })
        );
      } catch (e) {}
    };

    var restoreLocalDraft = function () {
      try {
        var raw = sessionStorage.getItem(DRAFT_KEY);
        if (!raw) return;
        var data = JSON.parse(raw);
        if (emailInput && !emailInput.value && data.email) emailInput.value = data.email;
        if (namaInput && !namaInput.value && data.nama) namaInput.value = data.nama;
        if (tglLahirInput && !tglLahirInput.value && data.tglLahir) tglLahirInput.value = data.tglLahir;
        if (waInput && !waInput.value && data.wa) waInput.value = data.wa;
        if (nikInput && !nikInput.value && data.nik) nikInput.value = data.nik;
      } catch (e) {}
    };

    restoreLocalDraft();

    [emailInput, namaInput, tglLahirInput, waInput, nikInput].forEach(function (el) {
      if (!el) return;
      el.addEventListener("input", function () {
        saveLocalDraft();
        if (el === emailInput) setRegError("email", "");
        if (el === namaInput) setRegError("nama_lengkap", "");
        if (el === tglLahirInput) setRegError("tanggal_lahir", "");
        if (el === waInput) setRegError("whatsapp", "");
        if (el === nikInput) {
          el.value = el.value.replace(/[^0-9]/g, "").slice(0, 16);
          setRegError("nik", "");
        }
      });
    });

    if (pwInput) {
      pwInput.addEventListener("input", function () {
        evalPwRules();
        setRegError("password", "");
      });
      evalPwRules();
    }

    if (pwConfirmInput) {
      pwConfirmInput.addEventListener("input", function () {
        setRegError("password_confirmation", "");
      });
    }

    var switchRegStep = function (step) {
      if (!pane1 || !pane2) return;
      var isStep1 = step === 1;
      pane1.hidden = !isStep1;
      pane2.hidden = isStep1;

      if (emailPreview && emailInput && emailInput.value.trim()) {
        emailPreview.textContent = emailInput.value.trim();
      }

      if (ind1) {
        ind1.classList.toggle("is-active", isStep1);
        ind1.classList.toggle("is-done", !isStep1);
      }
      if (ind2) {
        ind2.classList.toggle("is-active", !isStep1);
      }
    };

    var validateStep1Fields = function () {
      var emailVal = emailInput ? emailInput.value.trim() : "";
      var pwVal = pwInput ? pwInput.value : "";
      var confirmVal = pwConfirmInput ? pwConfirmInput.value : "";
      var valid = true;

      if (!emailVal || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
        setRegError("email", "Masukkan alamat email yang valid.");
        if (emailInput) emailInput.focus();
        valid = false;
      } else {
        setRegError("email", "");
      }

      if (!evalPwRules()) {
        setRegError(
          "password",
          "Kata sandi wajib 8 sampai 16 karakter, huruf besar & kecil, serta minimal 1 angka."
        );
        if (valid && pwInput) pwInput.focus();
        valid = false;
      } else {
        setRegError("password", "");
      }

      if (!confirmVal || confirmVal !== pwVal) {
        setRegError(
          "password_confirmation",
          "Konfirmasi kata sandi belum sama."
        );
        if (valid && pwConfirmInput) pwConfirmInput.focus();
        valid = false;
      } else {
        setRegError("password_confirmation", "");
      }

      return valid;
    };

    var advanceToStep2 = function () {
      if (!validateStep1Fields()) return;
      saveLocalDraft();

      if (!step1Url || !window.fetch) {
        switchRegStep(2);
        if (namaInput) namaInput.focus();
        return;
      }

      if (nextBtn) nextBtn.disabled = true;

      fetch(step1Url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": csrfInput ? csrfInput.value : "",
        },
        credentials: "same-origin",
        body: JSON.stringify({
          email: emailInput ? emailInput.value.trim() : "",
          password: pwInput ? pwInput.value : "",
          password_confirmation: pwConfirmInput ? pwConfirmInput.value : "",
        }),
      })
        .then(function (res) {
          return res.json().then(function (body) {
            return { status: res.status, body: body };
          });
        })
        .then(function (result) {
          if (nextBtn) nextBtn.disabled = false;
          if (result.status === 422 && result.body && result.body.errors) {
            var errs = result.body.errors;
            if (errs.email && errs.email[0]) {
              setRegError("email", errs.email[0]);
              if (emailInput) emailInput.focus();
            }
            if (errs.password && errs.password[0]) {
              setRegError("password", errs.password[0]);
              if (!errs.email && pwInput) pwInput.focus();
            }
            return;
          }
          switchRegStep(2);
          if (namaInput) namaInput.focus();
        })
        .catch(function () {
          if (nextBtn) nextBtn.disabled = false;
          switchRegStep(2);
          if (namaInput) namaInput.focus();
        });
    };

    if (nextBtn) {
      nextBtn.addEventListener("click", advanceToStep2);
    }

    [].forEach.call(prevBtns, function (btn) {
      btn.addEventListener("click", function () {
        switchRegStep(1);
        if (emailInput) emailInput.focus();
      });
    });

    regForm.addEventListener("keydown", function (e) {
      if (e.key === "Enter" && pane1 && !pane1.hidden) {
        e.preventDefault();
        advanceToStep2();
      }
    });

    regForm.addEventListener("submit", function (e) {
      if (pane1 && !pane1.hidden) {
        e.preventDefault();
        advanceToStep2();
        return;
      }

      var validStep2 = true;
      var namaVal = namaInput ? namaInput.value.trim() : "";
      var tglVal = tglLahirInput ? tglLahirInput.value.trim() : "";
      var waVal = waInput ? waInput.value.replace(/[^0-9]/g, "") : "";
      var nikVal = nikInput ? nikInput.value.replace(/[^0-9]/g, "") : "";

      if (!namaVal) {
        setRegError("nama_lengkap", "Masukkan nama lengkap calon siswa.");
        if (validStep2 && namaInput) namaInput.focus();
        validStep2 = false;
      } else {
        setRegError("nama_lengkap", "");
      }

      if (!tglVal) {
        setRegError("tanggal_lahir", "Pilih tanggal lahir calon siswa.");
        if (validStep2 && tglLahirInput) tglLahirInput.focus();
        validStep2 = false;
      } else {
        setRegError("tanggal_lahir", "");
      }

      if (!waVal || waVal.length < 8) {
        setRegError("whatsapp", "Masukkan nomor WhatsApp aktif yang valid.");
        if (validStep2 && waInput) waInput.focus();
        validStep2 = false;
      } else {
        setRegError("whatsapp", "");
      }

      if (nikVal.length !== 16) {
        setRegError("nik", "NIK calon siswa wajib tepat 16 angka.");
        if (validStep2 && nikInput) nikInput.focus();
        validStep2 = false;
      } else {
        setRegError("nik", "");
      }

      if (!validStep2) {
        e.preventDefault();
      }
    });
  }
  /* ---------- Unggah Berkas: tampilkan nama file terpilih ---------- */

  [].forEach.call(document.querySelectorAll(".ppdb-file"), function (wrap) {
    var input = wrap.querySelector('input[type="file"]');
    var label = wrap.querySelector("[data-file-name]");
    if (!input || !label) return;

    var defaultText = label.textContent;

    input.addEventListener("change", function () {
      var file = input.files && input.files[0];
      label.textContent = file ? file.name : defaultText;
      wrap.classList.toggle("has-file", Boolean(file));
    });
  });
})();
