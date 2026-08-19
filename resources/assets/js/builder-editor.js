/**
 * Website Builder — GrapesJS Editor bootstrap.
 *
 * Memuat project (getProjectData) dari server, menyediakan blok section
 * (rendered server-side dari config builder_sections), asset upload ke
 * endpoint builder, penyimpanan gjs_project + html + css, publish/unpublish,
 * dan toolbar aksi kustom.
 *
 * Konfigurasi diterima lewat `window.__BUILDER_CONFIG` (diisi dari Blade).
 */
import grapesjs from 'grapesjs';
import 'grapesjs/dist/css/grapes.min.css';
import axios from 'axios';
import Swal from 'sweetalert2';

const cfg = window.__BUILDER_CONFIG || {};
const $ = (selector) => document.querySelector(selector);

let dirty = false;
const statusEl = $('#be-status');

function setStatus(text, kind = 'idle') {
    if (!statusEl) return;
    statusEl.textContent = text;
    statusEl.className = `be-status be-status-${kind}`;
}

function toast(icon, title) {
    return Swal.fire({
        icon,
        title,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
    });
}

// Bootstrap editor GrapesJS
const editor = grapesjs.init({
    container: '#gjs',
    fromElement: false,
    height: '100%',
    width: 'auto',
    storageManager: false,

    assetManager: {
        assets: [],
        upload: cfg.uploadUrl || false,
        uploadName: 'files',
        headers: { 'X-CSRF-TOKEN': cfg.csrf },
        params: { _token: cfg.csrf },
        credentials: 'same-origin',
        autoAdd: 1,
        multiUpload: true,
        dropzone: true,
        openAssetsOnDrop: true,
        modalTitle: 'Pilih / Unggah Gambar',
        uploadText: 'Letakkan file di sini atau klik untuk unggah',
        addBtnText: 'Tambah URL gambar',
        noAssets: 'Belum ada aset. Unggah gambar untuk mulai.',
    },

    deviceManager: {
        devices: [
            { id: 'Desktop', name: 'Desktop', width: '', widthMedia: '992px' },
            { id: 'Tablet', name: 'Tablet', width: '768px', widthMedia: '768px' },
            { id: 'Mobile portrait', name: 'Mobile', width: '375px', height: '667px', widthMedia: '480px' },
        ],
    },

    canvas: {
        styles: Array.isArray(cfg.canvasStyles) ? cfg.canvasStyles : [],
    },
});

window.__BUILDER_EDITOR = editor;

/* ── Muat konten awal ───────────────────────────────────────── */
function starterMarkup() {
    return [
        '<section class="wbp-section wbp-bg-white">',
        '  <div class="wbp-container wbp-text-center">',
        '    <h2 class="wbp-title wbp-title-lg" style="margin-bottom:0.5rem">Mulai dari sini ✨</h2>',
        '    <p class="wbp-subtitle" style="margin:0 auto;max-width:40rem">',
        '      Seret blok dari panel kiri (Dasar / Pemasaran / Konten / Layout), lalu simpan.',
        '    </p>',
        '  </div>',
        '</section>',
    ].join('');
}

function loadInitial() {
    const project = cfg.gjsProject;
    if (project && Array.isArray(project.components) && project.components.length) {
        editor.loadProjectData(project);
        return;
    }
    if (cfg.html) {
        editor.setComponents(cfg.html);
        if (cfg.css) editor.setStyle(cfg.css);
        return;
    }
    editor.setComponents(starterMarkup());
}

/* ── Blok kustom dari section builder ───────────────────────── */
function blockMedia(icon) {
    const safe = String(icon || '').replace(/[^a-z0-9-]/gi, '');
    if (safe) {
        return `<i class="ti ti-${safe}" style="font-size:26px;line-height:1"></i>`;
    }
    return '▦';
}

function addSectionBlocks() {
    const blocks = cfg.blocks || {};
    Object.entries(blocks).forEach(([key, def]) => {
        editor.BlockManager.add(key, {
            label: def.name,
            category: def.category,
            select: true,
            activate: true,
            media: blockMedia(def.icon),
            content: def.html,
        });
    });
}

function addBaseBlocks() {
    const base = [
        ['wbp-row', 'Baris 2 Kolom', 'Dasar',
            '<div style="display:flex;gap:1rem;flex-wrap:wrap">' +
            '<div style="flex:1 1 40%;min-width:220px;padding:1rem;border:1px dashed #cbd5e1;border-radius:12px;min-height:90px"></div>' +
            '<div style="flex:1 1 40%;min-width:220px;padding:1rem;border:1px dashed #cbd5e1;border-radius:12px;min-height:90px"></div>' +
            '</div>'],
        ['wbp-row-3', 'Baris 3 Kolom', 'Dasar',
            '<div style="display:flex;gap:1rem;flex-wrap:wrap">' +
            '<div style="flex:1 1 30%;min-width:200px;padding:1rem;border:1px dashed #cbd5e1;border-radius:12px;min-height:80px"></div>' +
            '<div style="flex:1 1 30%;min-width:200px;padding:1rem;border:1px dashed #cbd5e1;border-radius:12px;min-height:80px"></div>' +
            '<div style="flex:1 1 30%;min-width:200px;padding:1rem;border:1px dashed #cbd5e1;border-radius:12px;min-height:80px"></div>' +
            '</div>'],
        ['wbp-youtube', 'Video (YouTube)', 'Dasar',
            '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:12px">' +
            '<iframe style="position:absolute;left:0;top:0;width:100%;height:100%;border:0" ' +
            'src="https://www.youtube.com/embed/jNQXAC9IVRw" title="YouTube video" ' +
            'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" ' +
            'allowfullscreen loading="lazy"></iframe></div>'],
        ['wbp-spacer', 'Spacer', 'Dasar',
            '<div style="height:60px;min-height:20px" data-gjs="spacer"></div>'],
    ];

    base.forEach(([id, label, cat, content]) => {
        editor.BlockManager.add(id, { label, category: cat, media: blockMedia(idToIcon(id)), content });
    });
}

function idToIcon(id) {
    const map = {
        'wbp-row': 'columns-2',
        'wbp-row-3': 'columns-3',
        'wbp-youtube': 'brand-youtube',
        'wbp-spacer': 'arrows-vertical',
    };
    return map[id] || 'box';
}

/* ── Theme & canvas helper ───────────────────────────────────── */
function applyThemeToFrame() {
    const doc = editor.Canvas.getDocument();
    if (!doc) return;

    if (doc.body) {
        doc.body.style.margin = '0';
        doc.body.style.background = '#ffffff';
    }

    if (cfg.themeCss && doc.head && !doc.getElementById('wbp-theme-vars')) {
        const st = doc.createElement('style');
        st.id = 'wbp-theme-vars';
        st.textContent = cfg.themeCss;
        doc.head.appendChild(st);
    }
}

editor.on('canvas:frame:load', applyThemeToFrame);

function injectThemeSafely() {
    try { applyThemeToFrame(); } catch (_) { /* frame mungkin belum siap */ }
}

// Buka panel blok (Block Manager) sejak awal agar library section langsung
// terlihat, dan pastikan CSS variabel tema masuk ke frame kanvas.
editor.on('load', () => {
    injectThemeSafely();
    // Block Manager baru tersedia setelah modul selesai dirender — jalankan
    // sesudah event loop agar panel tampil dengan daftar blok lengkap.
    setTimeout(() => {
        const blocksBtn = document.querySelector(
            '.gjs-pn-views-buttons .gjs-pn-btn[title="Open Blocks"], .gjs-pn-views .gjs-pn-btn[title="Open Blocks"]'
        );
        if (blocksBtn) {
            blocksBtn.click();
        } else {
            try { editor.Commands.run('core:open-blocks'); } catch (_) { /* no-op */ }
        }
    }, 400);
    setTimeout(() => { injectThemeSafely(); }, 600);
});

/* ── Aksi toolbar ────────────────────────────────────────────── */
async function save(showToast = true) {
    setStatus('Menyimpan…', 'busy');
    try {
        const payload = {
            gjs_project: editor.getProjectData(),
            html: editor.getHtml(),
            css: editor.getCss(),
        };
        const { data } = await axios.post(cfg.saveUrl, payload, {
            headers: { 'X-CSRF-TOKEN': cfg.csrf },
        });
        dirty = false;
        setStatus(data.message || 'Tersimpan', 'ok');
        if (showToast) toast('success', data.message || 'Project tersimpan.');
    } catch (err) {
        setStatus('Gagal menyimpan', 'error');
        toast('error', err.response?.data?.message || 'Terjadi kesalahan saat menyimpan.');
    }
}

async function togglePublish() {
    const btn = $('#be-publish');
    const action = btn?.dataset.action; // publish | unpublish
    const publish = action === 'publish';

    const { isConfirmed } = await Swal.fire({
        title: publish ? 'Publikasikan halaman?' : 'Hentikan publikasi?',
        text: publish
            ? 'Halaman akan tampil untuk umum di URL-nya.'
            : 'Halaman tidak lagi tampil untuk umum.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: publish ? 'Ya, publikasikan' : 'Ya, hentikan',
        cancelButtonText: 'Batal',
    });
    if (!isConfirmed) return;

    try {
        await axios.post(
            publish ? cfg.publishUrl : cfg.unpublishUrl,
            {},
            { headers: { 'X-CSRF-TOKEN': cfg.csrf } }
        );
        toast('success', publish ? 'Halaman dipublikasikan.' : 'Publikasi dihentikan.');
        window.location.reload();
    } catch (err) {
        toast('error', err.response?.data?.message || 'Gagal mengubah status publikasi.');
    }
}

/* ── Pemilihan band blok ─────────────────────────────────────── */
function blockRoot(comp) {
    const wrapper = editor.getWrapper();
    if (!wrapper || !comp || comp === wrapper) return null;
    let p = comp;
    while (p.parent() && p.parent() !== wrapper) p = p.parent();
    return p.parent() === wrapper ? p : null;
}

function currentSelection() {
    return editor.getSelected() || (editor.getSelectedAll?.()?.[0] ?? null) || null;
}

function wireSectionPicker() {
    const btn = $('#be-pick-section');
    if (!btn) return;

    const refresh = () => {
        const sel = currentSelection();
        if (!sel) { btn.classList.add('d-none'); return; }
        const root = blockRoot(sel);
        if (!root) { btn.classList.add('d-none'); return; }
        btn.classList.remove('d-none');
        btn.innerHTML = root === sel
            ? '<i class="ti ti-frame"></i><span>Band terpilih</span>'
            : '<i class="ti ti-frame"></i><span>Pilih Band</span>';
    };

    let debounce = null;
    const schedule = () => {
        clearTimeout(debounce);
        debounce = setTimeout(refresh, 50);
    };
    editor.on('component:selected', schedule);
    editor.on('component:deselected', schedule);
    editor.on('load', () => setTimeout(refresh, 400));
    refresh();

    btn.addEventListener('click', () => {
        const sel = currentSelection();
        let root = sel ? blockRoot(sel) : null;
        if (!root) {
            const first = editor.getWrapper().components().first();
            root = first ? blockRoot(first) : null;
        }
        if (!root) { toast('info', 'Halaman masih kosong — taruh blok dulu.'); return; }
        editor.select(root);
        setTimeout(() => {
            const el = root.getEl();
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setStatus('Band blok dipilih — ganti Background di panel kanan (Dekorasi).', 'ok');
        }, 60);
    });
}

function wireToolbar() {
    $('#be-save')?.addEventListener('click', () => save(true));
    $('#be-publish')?.addEventListener('click', togglePublish);

    $('#be-undo')?.addEventListener('click', () => editor.UndoManager.undo());
    $('#be-redo')?.addEventListener('click', () => editor.UndoManager.redo());

    wireSectionPicker();

    $('#be-clear')?.addEventListener('click', async () => {
        const { isConfirmed } = await Swal.fire({
            title: 'Hapus semua komponen?',
            text: 'Seluruh isi halaman akan dikosongkan (style ikut dibersihkan).',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, kosongkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626',
        });
        if (!isConfirmed) return;
        editor.DomComponents.clear();
        editor.CssComposer.clear();
        editor.setStyle('');
        dirty = true;
        setStatus('Belum disimpan', 'dirty');
        toast('success', 'Kanvas dikosongkan.');
    });
}

/* ── Autosave (debounce) ─────────────────────────────────────── */
let autosaveTimer = null;
let autosaveBusy = false;
let autosaveEnabledAt = Date.now(); // abaikan update awal saat editor baru dibuka

async function autosave() {
    if (autosaveBusy) {
        autosaveTimer = setTimeout(autosave, 800);
        return;
    }
    autosaveBusy = true;
    try {
        const payload = {
            gjs_project: editor.getProjectData(),
            html: editor.getHtml(),
            css: editor.getCss(),
        };
        await axios.post(cfg.saveUrl, payload, {
            headers: { 'X-CSRF-TOKEN': cfg.csrf },
        });
        dirty = false;
        setStatus('Disimpan otomatis ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }), 'ok');
    } catch (err) {
        setStatus('Autosave gagal — cek koneksi', 'error');
    } finally {
        autosaveBusy = false;
    }
}

function queueAutosave() {
    if (Date.now() - autosaveEnabledAt < 2000) return;
    clearTimeout(autosaveTimer);
    autosaveTimer = setTimeout(autosave, 1500);
}

/* ── Status / shortcut ───────────────────────────────────────── */
editor.on('update', () => {
    dirty = true;
    setStatus('Belum disimpan', 'dirty');
    queueAutosave();
});

// Simpan sekali tak lama setelah editor terbuka supaya preview/publikasi
// langsung mencerminkan kanvas walau pengguna belum menekan "Simpan".
setTimeout(() => { autosave(); }, 2600);

document.addEventListener('keydown', (e) => {
    const mod = e.ctrlKey || e.metaKey;
    if (mod && (e.key === 's' || e.key === 'S')) {
        e.preventDefault();
        save(true);
    }
});

window.addEventListener('beforeunload', (e) => {
    if (!dirty) return undefined;
    e.preventDefault();
    e.returnValue = '';
    return '';
});

window.BuilderEditorApi = { save, togglePublish };

document.body.classList.add('builder-editor');
loadInitial();
addSectionBlocks();
addBaseBlocks();
wireToolbar();
setStatus(dirty ? 'Belum disimpan' : 'Siap', dirty ? 'dirty' : 'idle');

// Fokus ulang layouter setelah editor terbentuk (pastikan ukuran kanvas benar).
setTimeout(() => {
    try { editor.refresh(); } catch (e) { /* abaikan */ }
}, 50);