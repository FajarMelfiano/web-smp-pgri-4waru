@extends('layouts.admin')

@section('title', 'Berita & Pengumuman — Admin SMP PGRI 4 Waru')
@section('description', 'Tulis dan kelola artikel berita, informasi kegiatan, serta pengumuman resmi SMP PGRI 4 Waru.')
@section('crumb', 'Berita & Pengumuman')

@php
  $initialCover = '';
  if ($editPost && $editPost->cover_path) {
      $initialCover = str_starts_with($editPost->cover_path, 'http') || str_starts_with($editPost->cover_path, '/')
          ? $editPost->cover_path
          : asset('storage/'.$editPost->cover_path);
  }
  $presetCovers = [
      ['src' => '/images/sekolah-upacara.jpg', 'label' => 'Upacara Senin'],
      ['src' => '/images/sekolah-kelas-pagi.jpg', 'label' => 'Kelas Pagi'],
      ['src' => '/images/sekolah-multimedia.jpg', 'label' => 'Lab Informatika'],
      ['src' => '/images/sekolah-perpustakaan.jpg', 'label' => 'Perpustakaan'],
      ['src' => '/images/sekolah-praktikum-ipa.jpg', 'label' => 'Praktikum IPA'],
      ['src' => '/images/sekolah-pramuka.jpg', 'label' => 'Pramuka'],
      ['src' => '/images/sekolah-diskusi-kelas.jpg', 'label' => 'Diskusi Siswa'],
      ['src' => '/images/sekolah-gedung.jpg', 'label' => 'Gedung Sekolah'],
  ];
@endphp

@section('content')
  <div class="adm-page-head">
    <div>
      <h1>Berita &amp; Pengumuman</h1>
      <p>Tulis artikel kegiatan siswa, pengumuman akademik, dan berita sekolah. Isi artikel tampil di Beranda dan halaman <code>/berita</code>.</p>
    </div>
    <div class="adm-actions">
      <a class="btn btn--ghost btn--sm" href="#daftar-artikel">
        <span>Arsip Artikel ({{ $stats['berita_total'] ?? 0 }})</span>
      </a>
      <a class="btn btn--ghost btn--sm" href="{{ route('berita.index') }}">
        <span>Lihat Halaman Berita Publik</span>
      </a>
      @if($editPost)
        <a class="btn btn--primary btn--sm" href="{{ route('admin.berita') }}">Entri Baru</a>
      @endif
    </div>
  </div>

  @if(session('status'))
    <div class="adm-alert" role="status">
      <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  {{-- STUDIO KOMPOSER BERGAYA BLOGGER --}}
  <section class="blogger-studio" aria-labelledby="editor-berita-heading">
    <form id="blogger-form" method="POST" action="{{ route('admin.berita.store') }}" enctype="multipart/form-data">
      @csrf
      @if($editPost)
        <input type="hidden" name="post_id" value="{{ $editPost->id }}">
      @endif

      {{-- Bilah Atas (Judul Besar + Mode Tampilan + Aksi Simpan/Publikasikan) --}}
      <div class="blogger-topbar">
        <div class="blogger-topbar__left">
          <div class="blogger-mark" aria-hidden="true" title="Editor Berita SMP PGRI 4 Waru">
            <svg aria-hidden="true"><use href="#i-pencil-mark"/></svg>
          </div>
          <div class="blogger-title-wrap">
            <h2 id="editor-berita-heading" class="sr-only">Editor isi berita</h2>
            <label for="berita-judul" class="sr-only">Judul berita atau pengumuman</label>
            <input
              id="berita-judul"
              class="blogger-title-input"
              name="judul"
              type="text"
              value="{{ old('judul', $editPost?->judul ?? '') }}"
              placeholder="Judul berita atau pengumuman resmi..."
              required
            >
            @error('judul')
              <span class="error" style="display:block;margin-top:0.25rem;color:var(--red);font-size:0.78rem;font-weight:700">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="blogger-topbar__actions">
          @if($editPost)
            <span class="adm-status adm-status--{{ $editPost->status }}">{{ ucfirst($editPost->status) }}</span>
          @endif

          <div class="blogger-mode-switch" role="tablist" aria-label="Mode tampilan editor">
            <button type="button" class="blogger-mode-btn is-active" data-editor-mode="visual" role="tab" aria-selected="true">
              <svg aria-hidden="true"><use href="#i-ed-visual"/></svg>
              <span>Visual</span>
            </button>
            <button type="button" class="blogger-mode-btn" data-editor-mode="html" role="tab" aria-selected="false">
              <svg aria-hidden="true"><use href="#i-ed-code"/></svg>
              <span>HTML</span>
            </button>
            <button type="button" class="blogger-mode-btn" data-editor-mode="preview" role="tab" aria-selected="false">
              <svg aria-hidden="true"><use href="#i-ed-eye"/></svg>
              <span>Pratinjau</span>
            </button>
          </div>

          <button type="button" id="btn-save-draft" class="btn btn--ghost btn--sm">
            Simpan Draf
          </button>

          <button type="submit" id="btn-publish-main" class="btn btn--primary btn--sm">
            <svg class="i" aria-hidden="true"><use href="#i-send"/></svg>
            <span>{{ $editPost ? 'Perbarui Artikel' : 'Publikasikan' }}</span>
          </button>

          @if($editPost)
            <a href="{{ route('admin.berita') }}" class="btn btn--quiet btn--sm">Batal</a>
          @endif
        </div>
      </div>

      {{-- Bilah Alat Format Kaya (Blogger Formatting Toolbar) --}}
      <div class="blogger-toolbar" id="blogger-toolbar" role="toolbar" aria-label="Alat pemformatan teks berita">
        <div class="blogger-tool-group">
          <button type="button" class="blogger-tool-btn" data-cmd="undo" title="Urungkan (Ctrl+Z)" aria-label="Urungkan">
            <svg aria-hidden="true"><use href="#i-ed-undo"/></svg>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="redo" title="Ulangi (Ctrl+Y)" aria-label="Ulangi">
            <svg aria-hidden="true"><use href="#i-ed-redo"/></svg>
          </button>
        </div>

        <span class="blogger-sep" aria-hidden="true"></span>

        <div class="blogger-tool-group">
          <label for="blogger-block-format" class="sr-only">Format baris atau judul</label>
          <select id="blogger-block-format" class="blogger-format-select" title="Format Paragraf & Judul">
            <option value="P">Paragraf Normal</option>
            <option value="H2">Judul Utama (H2)</option>
            <option value="H3">Subjudul Bagian (H3)</option>
            <option value="BLOCKQUOTE">Kutipan Sorotan</option>
          </select>
        </div>

        <span class="blogger-sep" aria-hidden="true"></span>

        <div class="blogger-tool-group">
          <button type="button" class="blogger-tool-btn" data-cmd="bold" title="Tebal (Ctrl+B)" aria-label="Tebal">
            <b>B</b>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="italic" title="Miring (Ctrl+I)" aria-label="Miring">
            <i style="font-family:Georgia,serif">I</i>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="underline" title="Garis Bawah (Ctrl+U)" aria-label="Garis Bawah">
            <u>U</u>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="strikeThrough" title="Coret" aria-label="Coret">
            <s>S</s>
          </button>
        </div>

        <span class="blogger-sep" aria-hidden="true"></span>

        <div class="blogger-tool-group">
          <button type="button" class="blogger-tool-btn" data-cmd="justifyLeft" title="Rata Kiri" aria-label="Rata Kiri">
            <svg aria-hidden="true"><use href="#i-ed-align-left"/></svg>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="justifyCenter" title="Rata Tengah" aria-label="Rata Tengah">
            <svg aria-hidden="true"><use href="#i-ed-align-center"/></svg>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="insertUnorderedList" title="Daftar Poin" aria-label="Daftar Poin">
            <svg aria-hidden="true"><use href="#i-ed-list-ul"/></svg>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="insertOrderedList" title="Daftar Bernomor" aria-label="Daftar Bernomor">
            <svg aria-hidden="true"><use href="#i-ed-list-ol"/></svg>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="formatBlock" data-val="BLOCKQUOTE" title="Kutipan" aria-label="Kutipan">
            <svg aria-hidden="true"><use href="#i-ed-quote"/></svg>
          </button>
        </div>

        <span class="blogger-sep" aria-hidden="true"></span>

        <div class="blogger-tool-group">
          <button type="button" class="blogger-tool-btn" id="btn-toggle-link-drawer" title="Sisipkan Tautan" aria-label="Sisipkan Tautan">
            <svg aria-hidden="true"><use href="#i-ed-link"/></svg>
            <span style="margin-left:0.28rem;font-size:0.76rem">Tautan</span>
          </button>
          <button type="button" class="blogger-tool-btn" id="btn-toggle-image-drawer" title="Sisipkan Foto ke Dalam Artikel" aria-label="Sisipkan Foto">
            <svg aria-hidden="true"><use href="#i-ed-image"/></svg>
            <span style="margin-left:0.28rem;font-size:0.76rem">Sisipkan Foto</span>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="insertHorizontalRule" title="Garis Pemisah Bagian" aria-label="Garis Pemisah">
            <svg aria-hidden="true"><use href="#i-ed-hr"/></svg>
          </button>
          <button type="button" class="blogger-tool-btn" data-cmd="removeFormat" title="Bersihkan Format" aria-label="Bersihkan Format">
            <span style="font-size:0.74rem">Tx</span>
          </button>
        </div>

        <div class="blogger-toolbar__stats" id="blogger-stats" aria-live="polite">
          <span id="blogger-word-count">0 kata</span>
          <span>·</span>
          <span id="blogger-char-count">0 karakter</span>
        </div>
      </div>

      {{-- Laci Sisipan Tautan --}}
      <div class="blogger-insert-drawer" id="drawer-insert-link">
        <div class="blogger-insert-drawer__row">
          <strong style="font-size:0.8rem;color:var(--ink)">Sisipkan Tautan:</strong>
          <input type="text" id="insert-link-text" class="adm-input" placeholder="Teks tampilan (opsional)" style="min-width:180px">
          <input type="url" id="insert-link-url" class="adm-input" placeholder="https://smppgri4waru.sch.id/ppdb" style="min-width:240px">
          <button type="button" id="btn-apply-link" class="btn btn--primary btn--sm">Tambahkan Tautan</button>
          <button type="button" id="btn-close-link-drawer" class="btn btn--quiet btn--sm">Tutup</button>
        </div>
      </div>

      {{-- Laci Sisipan Gambar ke Isi Berita --}}
      <div class="blogger-insert-drawer" id="drawer-insert-image">
        <div class="blogger-insert-drawer__row">
          <strong style="font-size:0.8rem;color:var(--ink)">Sisipkan Gambar ke Isi Artikel:</strong>
          <input type="text" id="insert-img-url" class="adm-input" placeholder="URL gambar atau pilih foto dokumentasi di bawah..." style="min-width:250px">
          <input type="text" id="insert-img-alt" class="adm-input" placeholder="Keterangan gambar (caption)" style="min-width:190px">
          <button type="button" id="btn-apply-image" class="btn btn--primary btn--sm">Masukkan ke Artikel</button>
          <button type="button" id="btn-close-image-drawer" class="btn btn--quiet btn--sm">Tutup</button>
        </div>
        <div class="blogger-insert-drawer__thumbs">
          @foreach($presetCovers as $preset)
            <button type="button" class="blogger-thumb-pick" data-insert-src="{{ $preset['src'] }}" data-insert-cap="{{ $preset['label'] }} — SMP PGRI 4 Waru">
              <img src="{{ $preset['src'] }}" alt="{{ $preset['label'] }}">
              <span>+ {{ $preset['label'] }}</span>
            </button>
          @endforeach
        </div>
      </div>

      {{-- RUANG KERJA 2 KOLOM: LEMBAR KERTAS DOKUMEN (KIRI) & SETELAN ENTRI (KANAN) --}}
      <div class="blogger-workspace">
        <div class="blogger-desk">
          <div class="blogger-paper">
            @error('isi')
              <div class="adm-alert" style="margin-bottom:1rem">
                <span>{{ $message }}</span>
              </div>
            @enderror

            {{-- Kanvas Visual WYSIWYG --}}
            <div
              id="blogger-visual"
              class="blogger-visual-editor"
              contenteditable="true"
              role="textbox"
              aria-multiline="true"
              aria-label="Lembar penulisan isi berita"
              data-placeholder="Mulai tulis isi berita di sini..."
            ></div>

            {{-- Editor Kode HTML / Fallback Textarea --}}
            <label for="berita-isi" class="sr-only">Isi lengkap pengumuman (HTML / Teks)</label>
            <textarea
              id="berita-isi"
              name="isi"
              class="blogger-html-editor"
              rows="14"
              placeholder="Tulis isi lengkap berita atau pengumuman resmi di sini..."
              style="display:none"
              required
            >{{ old('isi', $editPost?->isi ?? '') }}</textarea>

            {{-- Panel Pratinjau Langsung --}}
            <div id="blogger-preview-pane" style="display:none">
              <div style="padding-bottom:1.15rem;margin-bottom:1.25rem;border-bottom:1px solid var(--line)">
                <span class="news-card__badge">Pratinjau Halaman Publik</span>
                <h2 id="preview-title" style="margin-top:0.65rem;font-size:1.65rem;font-weight:800;letter-spacing:-0.025em;color:var(--ink)"></h2>
                <p id="preview-summary" style="margin-top:0.45rem;color:var(--ink-3);font-size:0.95rem"></p>
              </div>
              <figure id="preview-cover-wrap" class="article-cover" style="display:none;margin-bottom:1.5rem">
                <img id="preview-cover-img" alt="Pratinjau sampul">
              </figure>
              <div id="preview-body" class="prose prose--article"></div>
            </div>
          </div>
        </div>

        {{-- Panel Setelan Entri (Kanan) --}}
        <aside class="blogger-sidebar" aria-label="Setelan Entri Berita">
          <div class="blogger-sidebar__head">
            <h3>Setelan Entri</h3>
          </div>

          {{-- 1. Status Publikasi --}}
          <details class="blogger-setting-group" open>
            <summary>Status &amp; Visibilitas</summary>
            <div class="blogger-setting-body">
              <div class="field" style="margin-bottom:0.65rem">
                <label for="berita-status">Status penayangan</label>
                <select id="berita-status" name="status">
                  <option value="terbit" @selected(old('status', $editPost?->status ?? 'terbit') === 'terbit')>Terbit langsung di situs publik</option>
                  <option value="draf" @selected(old('status', $editPost?->status ?? '') === 'draf')>Simpan sebagai draf</option>
                </select>
              </div>
              <span class="hint" style="display:block;color:var(--ink-3);font-size:0.76rem;line-height:1.5">
                @if($editPost && $editPost->published_at)
                  Diterbitkan pada {{ $editPost->published_at->translatedFormat('d M Y, H:i') }} WIB.
                @else
                  Artikel berstatus <b>Terbit</b> langsung tampil di Beranda dan halaman <code>/berita</code>.
                @endif
              </span>
            </div>
          </details>

          {{-- 2. Gambar Sampul Utama --}}
          <details class="blogger-setting-group" open>
            <summary>Gambar Sampul Utama</summary>
            <div class="blogger-setting-body">
              <div class="blogger-cover-preview" id="blogger-cover-preview">
                @if($initialCover)
                  <img id="blogger-cover-img" src="{{ $initialCover }}" alt="Pratinjau sampul berita">
                @else
                  <div class="blogger-cover-empty" id="blogger-cover-empty">
                    <strong>Belum ada gambar sampul</strong>
                    <span style="display:block;margin-top:0.2rem">Unggah berkas foto atau pilih foto dokumentasi sekolah di bawah.</span>
                  </div>
                  <img id="blogger-cover-img" alt="Pratinjau sampul berita" style="display:none">
                @endif
              </div>

              <div class="field" style="margin-bottom:0.65rem">
                <label for="berita-cover">Unggah berkas sampul (JPG/PNG/WebP)</label>
                <input id="berita-cover" name="cover" type="file" accept=".jpg,.jpeg,.png,.webp">
              </div>

              <input type="hidden" id="berita-cover-preset" name="cover_preset" value="">

              <span style="display:block;font-size:0.74rem;font-weight:700;color:var(--ink-2)">Atau pilih cepat foto sekolah:</span>
              <div class="blogger-preset-grid">
                @foreach($presetCovers as $preset)
                  <button
                    type="button"
                    class="blogger-preset-btn {{ ($editPost && $editPost->cover_path === $preset['src']) ? 'is-selected' : '' }}"
                    data-cover-preset="{{ $preset['src'] }}"
                    title="Gunakan sampul: {{ $preset['label'] }}"
                  >
                    <img src="{{ $preset['src'] }}" alt="{{ $preset['label'] }}" loading="lazy">
                  </button>
                @endforeach
              </div>
            </div>
          </details>

          {{-- 3. Tautan Permanen (Permalink / Slug) --}}
          <details class="blogger-setting-group" open>
            <summary>Permalink (Tautan Berita)</summary>
            <div class="blogger-setting-body">
              <div class="field @error('slug') has-error @enderror" style="margin-bottom:0.5rem">
                <label for="berita-slug">Slug URL khusus (opsional)</label>
                <input
                  id="berita-slug"
                  name="slug"
                  type="text"
                  value="{{ old('slug', $editPost?->slug ?? '') }}"
                  placeholder="Otomatis dari judul berita"
                >
                @error('slug')
                  <span class="error">{{ $message }}</span>
                @enderror
              </div>
              <span class="hint" style="display:block;color:var(--ink-3);font-size:0.75rem;overflow-wrap:anywhere">
                Pratinjau URL: <b id="blogger-slug-preview">/berita/{{ $editPost?->slug ?: 'judul-berita-otomatis' }}</b>
              </span>
            </div>
          </details>

          {{-- 4. Deskripsi Penelusuran / Ringkasan --}}
          <details class="blogger-setting-group" open>
            <summary>Deskripsi Penelusuran</summary>
            <div class="blogger-setting-body">
              <div class="field @error('ringkasan') has-error @enderror" style="margin-bottom:0.35rem">
                <label for="berita-ringkasan">Ringkasan kartu &amp; SEO (maks 300 karakter)</label>
                <textarea
                  id="berita-ringkasan"
                  name="ringkasan"
                  rows="3"
                  maxlength="300"
                  placeholder="Ringkasan singkat satu atau dua kalimat untuk kartu berita di Beranda..."
                >{{ old('ringkasan', $editPost?->ringkasan ?? '') }}</textarea>
                @error('ringkasan')
                  <span class="error">{{ $message }}</span>
                @enderror
              </div>
              <div style="display:flex;justify-content:space-between;font-size:0.74rem;color:var(--ink-3)">
                <span>Tampil di kartu daftar berita</span>
                <span id="blogger-summary-count">0 / 300</span>
              </div>
            </div>
          </details>

          {{-- 5. Pintasan模板 Kerangka Berita Cepat --}}
          <details class="blogger-setting-group">
            <summary>Templat Kerangka Berita</summary>
            <div class="blogger-setting-body">
              <p style="margin:0 0 0.6rem;font-size:0.77rem;color:var(--ink-3);line-height:1.5">
                Isi cepat struktur paragraf standar untuk mempercepat penulisan:
              </p>
              <div style="display:grid;gap:0.4rem">
                <button type="button" class="btn btn--ghost btn--sm" data-template="pengumuman" style="justify-content:flex-start">
                  + Templat Pengumuman Resmi
                </button>
                <button type="button" class="btn btn--ghost btn--sm" data-template="liputan" style="justify-content:flex-start">
                  + Templat Liputan Kegiatan Siswa
                </button>
              </div>
            </div>
          </details>
        </aside>
      </div>
    </form>
  </section>

  {{-- ARSIP & DAFTAR ARTIKEL BERITA --}}
  <div id="daftar-artikel">
    <nav class="adm-tabs" aria-label="Saringan status berita">
      <a class="adm-tab {{ !request()->filled('status') ? 'is-active' : '' }}" href="{{ route('admin.berita', request()->only('q')) }}">
        <span>Semua Artikel</span>
        <span class="adm-tab__count">{{ $stats['berita_total'] ?? 0 }}</span>
      </a>
      <a class="adm-tab {{ request('status') === 'terbit' ? 'is-active' : '' }}" href="{{ route('admin.berita', array_filter(['status' => 'terbit', 'q' => request('q')])) }}">
        <span>Tayang (Terbit)</span>
        <span class="adm-tab__count">{{ $stats['berita_terbit'] ?? 0 }}</span>
      </a>
      <a class="adm-tab {{ request('status') === 'draf' ? 'is-active' : '' }}" href="{{ route('admin.berita', array_filter(['status' => 'draf', 'q' => request('q')])) }}">
        <span>Tersimpan (Draf)</span>
        <span class="adm-tab__count">{{ $stats['berita_draf'] ?? 0 }}</span>
      </a>
    </nav>

    <section class="adm-panel" aria-labelledby="daftar-berita-heading">
      <div class="adm-panel__head">
        <div class="adm-panel__title">
          <h2 id="daftar-berita-heading">Daftar Artikel &amp; Pengumuman</h2>
          <span class="adm-panel__badge">{{ $posts->count() }} artikel</span>
        </div>
      </div>

      <div class="adm-toolbar">
        <form method="GET" action="{{ route('admin.berita') }}" class="adm-filter-form">
          <label for="cari-berita" class="sr-only">Cari judul atau isi berita</label>
          <input id="cari-berita" class="adm-input" name="q" type="search" value="{{ request('q') }}" placeholder="Cari judul, ringkasan, atau slug...">

          <label for="status-berita" class="sr-only">Filter status berita</label>
          <select id="status-berita" class="adm-select" name="status">
            <option value="">Semua status</option>
            <option value="terbit" @selected(request('status') === 'terbit')>Status: Terbit</option>
            <option value="draf" @selected(request('status') === 'draf')>Status: Draf</option>
          </select>

          <button type="submit" class="btn btn--primary btn--sm">Cari</button>
          @if(request()->filled('q') || request()->filled('status'))
            <a href="{{ route('admin.berita') }}" class="btn btn--quiet btn--sm">Reset</a>
          @endif
        </form>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th scope="col">Sampul &amp; Judul Artikel</th>
              <th scope="col">Ringkasan</th>
              <th scope="col">Status</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($posts as $post)
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:0.85rem">
                    @if($post->cover_path)
                      <img
                        src="{{ str_starts_with($post->cover_path, 'http') || str_starts_with($post->cover_path, '/') ? $post->cover_path : asset('storage/'.$post->cover_path) }}"
                        alt=""
                        style="width:56px;height:38px;border-radius:8px;object-fit:cover;border:1px solid var(--line);flex:0 0 auto"
                      >
                    @endif
                    <div>
                      <span class="adm-cell-primary">{{ $post->judul }}</span>
                      <span class="adm-cell-sub">
                        {{ $post->created_at?->format('d M Y') }} · /berita/{{ $post->slug }}
                      </span>
                    </div>
                  </div>
                </td>
                <td style="max-width:300px">
                  <span class="adm-cell-sub" style="margin:0;line-height:1.45">{{ $post->excerpt(95) }}</span>
                </td>
                <td>
                  <span class="adm-status adm-status--{{ $post->status }}">{{ ucfirst($post->status) }}</span>
                </td>
                <td>
                  <div class="adm-actions">
                    <a href="{{ route('admin.berita', ['edit' => $post->id]) }}" class="btn btn--ghost btn--sm">Buka di Studio</a>

                    <form method="POST" action="{{ route('admin.berita.status', $post) }}" class="adm-inline-form">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn--quiet btn--sm">
                        {{ $post->status === 'terbit' ? 'Jadikan Draf' : 'Terbitkan' }}
                      </button>
                    </form>

                    @if($post->status === 'terbit')
                      <a href="{{ route('berita.show', $post->slug) }}" class="btn btn--ghost btn--sm">Baca</a>
                    @endif

                    @if(auth()->user()?->role === 'admin')
                      <form method="POST" action="{{ route('admin.berita.destroy', $post) }}" class="adm-inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--quiet btn--sm adm-btn-danger">Hapus</button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" style="text-align:center;padding:2.25rem 1rem;color:var(--ink-3)">
                  Belum ada artikel berita yang sesuai.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <script>
    (function () {
      var form = document.getElementById('blogger-form');
      var visual = document.getElementById('blogger-visual');
      var textarea = document.getElementById('berita-isi');
      var previewPane = document.getElementById('blogger-preview-pane');
      var toolbar = document.getElementById('blogger-toolbar');
      var blockSelect = document.getElementById('blogger-block-format');
      var titleInput = document.getElementById('berita-judul');
      var slugInput = document.getElementById('berita-slug');
      var slugPreview = document.getElementById('blogger-slug-preview');
      var summaryInput = document.getElementById('berita-ringkasan');
      var summaryCount = document.getElementById('blogger-summary-count');
      var statusSelect = document.getElementById('berita-status');
      var btnSaveDraft = document.getElementById('btn-save-draft');
      var wordCountEl = document.getElementById('blogger-word-count');
      var charCountEl = document.getElementById('blogger-char-count');
      var coverFileInput = document.getElementById('berita-cover');
      var coverPresetInput = document.getElementById('berita-cover-preset');
      var coverImg = document.getElementById('blogger-cover-img');
      var coverEmpty = document.getElementById('blogger-cover-empty');

      if (!visual || !textarea) return;

      function normalizeInitialContent(raw) {
        var trimmed = (raw || '').trim();
        if (!trimmed) return '';
        if (/<(p|h2|h3|h4|ul|ol|li|blockquote|figure|img|hr|div)\b/i.test(trimmed)) {
          return trimmed;
        }
        return trimmed
          .split(/\n\s*\n/)
          .map(function (p) {
            var line = p.trim();
            if (!line) return '';
            var escaped = line
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/\n/g, '<br>');
            return '<p>' + escaped + '</p>';
          })
          .filter(Boolean)
          .join('\n');
      }

      visual.innerHTML = normalizeInitialContent(textarea.value);

      function updateStats() {
        var plain = (visual.innerText || textarea.value || '').replace(/\s+/g, ' ').trim();
        var words = plain ? plain.split(' ').length : 0;
        var chars = plain.length;
        if (wordCountEl) wordCountEl.textContent = words + ' kata';
        if (charCountEl) charCountEl.textContent = chars + ' karakter';
      }

      function syncVisualToTextarea() {
        var html = visual.innerHTML.trim();
        if (html === '<br>' || html === '<p><br></p>') {
          textarea.value = '';
        } else {
          textarea.value = html;
        }
        updateStats();
      }

      function syncTextareaToVisual() {
        visual.innerHTML = normalizeInitialContent(textarea.value);
        updateStats();
      }

      visual.addEventListener('input', syncVisualToTextarea);
      visual.addEventListener('blur', syncVisualToTextarea);
      textarea.addEventListener('input', updateStats);
      updateStats();

      // Mode Switcher (Visual / HTML / Pratinjau)
      var currentMode = 'visual';
      var modeBtns = document.querySelectorAll('[data-editor-mode]');
      modeBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var targetMode = btn.getAttribute('data-editor-mode');
          if (targetMode === currentMode) return;

          if (currentMode === 'visual') {
            syncVisualToTextarea();
          } else if (currentMode === 'html') {
            syncTextareaToVisual();
          }

          currentMode = targetMode;
          modeBtns.forEach(function (b) {
            var active = b.getAttribute('data-editor-mode') === targetMode;
            b.classList.toggle('is-active', active);
            b.setAttribute('aria-selected', active ? 'true' : 'false');
          });

          visual.style.display = targetMode === 'visual' ? 'block' : 'none';
          textarea.style.display = targetMode === 'html' ? 'block' : 'none';
          previewPane.style.display = targetMode === 'preview' ? 'block' : 'none';
          if (toolbar) {
            toolbar.style.opacity = targetMode === 'visual' ? '1' : '0.55';
          }

          if (targetMode === 'preview') {
            document.getElementById('preview-title').textContent = titleInput.value || 'Judul Artikel Tanpa Nama';
            document.getElementById('preview-summary').textContent = summaryInput ? summaryInput.value : '';
            document.getElementById('preview-body').innerHTML = visual.innerHTML || '<p>Belum ada isi artikel.</p>';
            var pCoverWrap = document.getElementById('preview-cover-wrap');
            var pCoverImg = document.getElementById('preview-cover-img');
            if (coverImg && coverImg.getAttribute('src')) {
              pCoverImg.src = coverImg.getAttribute('src');
              pCoverWrap.style.display = 'block';
            } else {
              pCoverWrap.style.display = 'none';
            }
          }
        });
      });

      // Formatting Toolbar Commands
      document.querySelectorAll('[data-cmd]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (currentMode !== 'visual') return;
          var cmd = btn.getAttribute('data-cmd');
          var val = btn.getAttribute('data-val') || null;
          visual.focus();
          document.execCommand(cmd, false, val);
          syncVisualToTextarea();
        });
      });

      if (blockSelect) {
        blockSelect.addEventListener('change', function () {
          if (currentMode !== 'visual') return;
          visual.focus();
          document.execCommand('formatBlock', false, blockSelect.value);
          syncVisualToTextarea();
        });
      }

      // Laci Tautan
      var linkDrawer = document.getElementById('drawer-insert-link');
      var imgDrawer = document.getElementById('drawer-insert-image');
      var btnToggleLink = document.getElementById('btn-toggle-link-drawer');
      var btnToggleImg = document.getElementById('btn-toggle-image-drawer');

      if (btnToggleLink && linkDrawer) {
        btnToggleLink.addEventListener('click', function () {
          if (imgDrawer) imgDrawer.classList.remove('is-open');
          linkDrawer.classList.toggle('is-open');
        });
      }
      var btnCloseLink = document.getElementById('btn-close-link-drawer');
      if (btnCloseLink && linkDrawer) {
        btnCloseLink.addEventListener('click', function () {
          linkDrawer.classList.remove('is-open');
        });
      }
      var btnApplyLink = document.getElementById('btn-apply-link');
      if (btnApplyLink) {
        btnApplyLink.addEventListener('click', function () {
          var url = (document.getElementById('insert-link-url').value || '').trim();
          var text = (document.getElementById('insert-link-text').value || '').trim() || url;
          if (!url) return;
          visual.focus();
          document.execCommand('insertHTML', false, '<a href="' + url.replace(/"/g, '&quot;') + '">' + text + '</a>');
          syncVisualToTextarea();
          linkDrawer.classList.remove('is-open');
        });
      }

      // Laci Sisipkan Gambar
      if (btnToggleImg && imgDrawer) {
        btnToggleImg.addEventListener('click', function () {
          if (linkDrawer) linkDrawer.classList.remove('is-open');
          imgDrawer.classList.toggle('is-open');
        });
      }
      var btnCloseImg = document.getElementById('btn-close-image-drawer');
      if (btnCloseImg && imgDrawer) {
        btnCloseImg.addEventListener('click', function () {
          imgDrawer.classList.remove('is-open');
        });
      }
      function insertFigureIntoEditor(src, caption) {
        if (!src) return;
        var safeSrc = src.replace(/"/g, '&quot;');
        var safeCap = (caption || 'Dokumentasi kegiatan SMP PGRI 4 Waru').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        var figureHtml = '<figure><img src="' + safeSrc + '" alt="' + safeCap + '"><figcaption>' + safeCap + '</figcaption></figure><p></p>';
        visual.focus();
        document.execCommand('insertHTML', false, figureHtml);
        syncVisualToTextarea();
        if (imgDrawer) imgDrawer.classList.remove('is-open');
      }
      var btnApplyImg = document.getElementById('btn-apply-image');
      if (btnApplyImg) {
        btnApplyImg.addEventListener('click', function () {
          var src = (document.getElementById('insert-img-url').value || '').trim();
          var cap = (document.getElementById('insert-img-alt').value || '').trim();
          insertFigureIntoEditor(src, cap);
        });
      }
      document.querySelectorAll('[data-insert-src]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          insertFigureIntoEditor(btn.getAttribute('data-insert-src'), btn.getAttribute('data-insert-cap'));
        });
      });

      // Simpan Draf Cepat
      if (btnSaveDraft && statusSelect && form) {
        btnSaveDraft.addEventListener('click', function () {
          if (currentMode === 'visual') syncVisualToTextarea();
          statusSelect.value = 'draf';
          if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
          } else {
            form.submit();
          }
        });
      }

      if (form) {
        form.addEventListener('submit', function () {
          if (currentMode === 'visual') {
            syncVisualToTextarea();
          }
        });
      }

      // Live Permalink Preview
      function updateSlugPreview() {
        var raw = (slugInput && slugInput.value.trim()) ? slugInput.value.trim() : (titleInput ? titleInput.value.trim() : '');
        var slugified = raw
          .toLowerCase()
          .replace(/[^a-z0-9\s-]/g, '')
          .trim()
          .replace(/\s+/g, '-')
          .replace(/-+/g, '-');
        if (slugPreview) {
          slugPreview.textContent = '/berita/' + (slugified || 'judul-berita-otomatis');
        }
      }
      if (titleInput) titleInput.addEventListener('input', updateSlugPreview);
      if (slugInput) slugInput.addEventListener('input', updateSlugPreview);

      // Ringkasan Counter
      function updateSummaryCount() {
        if (!summaryInput || !summaryCount) return;
        summaryCount.textContent = summaryInput.value.length + ' / 300';
      }
      if (summaryInput) {
        summaryInput.addEventListener('input', updateSummaryCount);
        updateSummaryCount();
      }

      // Pratinjau Sampul & Preset Sampul
      function showCoverPreview(src) {
        if (!coverImg) return;
        coverImg.src = src;
        coverImg.style.display = 'block';
        if (coverEmpty) coverEmpty.style.display = 'none';
      }

      if (coverFileInput) {
        coverFileInput.addEventListener('change', function () {
          var file = coverFileInput.files && coverFileInput.files[0];
          if (!file) return;
          var reader = new FileReader();
          reader.onload = function (e) {
            showCoverPreview(e.target.result);
          };
          reader.readAsDataURL(file);
        });
      }

      var presetBtns = document.querySelectorAll('[data-cover-preset]');
      presetBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var src = btn.getAttribute('data-cover-preset');
          if (coverPresetInput) coverPresetInput.value = src;
          presetBtns.forEach(function (b) { b.classList.remove('is-selected'); });
          btn.classList.add('is-selected');
          showCoverPreview(src);
        });
      });

      // Templat Kerangka Berita Cepat
      document.querySelectorAll('[data-template]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var kind = btn.getAttribute('data-template');
          var tpl = '';
          if (kind === 'pengumuman') {
            tpl = '<h2>Informasi Pelaksanaan Kegiatan</h2><p>Diberitahukan kepada seluruh siswa dan orang tua/wali murid SMP PGRI 4 Waru bahwa sekolah akan menyelenggarakan kegiatan akademik sesuai jadwal berikut:</p><ul><li><b>Hari &amp; Tanggal:</b> Senin, pukul 07.00 WIB</li><li><b>Tempat:</b> Lingkungan Kampus SMP PGRI 4 Waru, Jl. Anggrek No. 109 Kureksari</li><li><b>Peserta:</b> Seluruh siswa kelas VII, VIII, dan IX</li></ul><blockquote>Dimohon kehadiran tepat waktu dengan mengenakan seragam sekolah lengkap sesuai ketentuan hari kerja.</blockquote>';
          } else {
            tpl = '<h2>Antusiasme Siswa dalam Kegiatan Sekolah</h2><p>Siswa-siswi SMP PGRI 4 Waru mengikuti rangkaian kegiatan pembinaan prestasi dan karakter di lingkungan sekolah dengan penuh semangat.</p><h3>Tujuan Pembinaan</h3><p>Kegiatan ini dirancang untuk memperkuat kedisiplinan, kerja sama tim, serta keterampilan praktis siswa baik di dalam maupun di luar kelas.</p>';
          }
          visual.innerHTML = (visual.innerHTML ? visual.innerHTML + '\n' : '') + tpl;
          syncVisualToTextarea();
        });
      });
    })();
  </script>
@endsection
