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
// FontAwesome: GrapesJS core UI (RTE, asset manager, modal, canvas badges) memakai class fa-*.
import '@fortawesome/fontawesome-free/css/all.min.css';
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

/* ── GrapesJS Editor Init ───────────────────────────────────── */
const editor = grapesjs.init({
    container: '#gjs',
    fromElement: false,
    height: '100%',
    width: 'auto',
    storageManager: false,


    styleManager: {
        sectors: [
            {
                name: 'Dimension',
                open: false,
                properties: [
                    {
                        name: 'Width',
                        property: 'width',
                        type: 'integer',
                        units: ['px', '%', 'rem'],
                        defaults: 'auto',
                        min: 0,
                    },
                    {
                        name: 'Height',
                        property: 'height',
                        type: 'integer',
                        units: ['px', '%', 'rem'],
                        defaults: 'auto',
                        min: 0,
                    },
                    {
                        name: 'Padding',
                        property: 'padding',
                        type: 'composite',
                        properties: [
                            { name: 'Top', property: 'padding-top' },
                            { name: 'Right', property: 'padding-right' },
                            { name: 'Bottom', property: 'padding-bottom' },
                            { name: 'Left', property: 'padding-left' },
                        ],
                    },
                    {
                        name: 'Margin',
                        property: 'margin',
                        type: 'composite',
                        properties: [
                            { name: 'Top', property: 'margin-top' },
                            { name: 'Right', property: 'margin-right' },
                            { name: 'Bottom', property: 'margin-bottom' },
                            { name: 'Left', property: 'margin-left' },
                        ],
                    },
                ],
            },
            {
                name: 'Typography',
                open: false,
                properties: [
                    {
                        name: 'Font Family',
                        property: 'font-family',
                        type: 'select',
                        defaults: 'inherit',
                        options: [
                            { value: 'inherit', name: 'Default' },
                            { value: 'system-ui, sans-serif', name: 'System UI' },
                            { value: 'Arial, sans-serif', name: 'Arial' },
                            { value: 'Georgia, serif', name: 'Georgia' },
                            { value: 'Times New Roman, serif', name: 'Times New Roman' },
                            { value: 'Courier New, monospace', name: 'Courier New' },
                        ],
                    },
                    {
                        name: 'Font Size',
                        property: 'font-size',
                        type: 'integer',
                        units: ['px', 'rem', 'em'],
                        defaults: '16px',
                        min: 8,
                        max: 72,
                    },
                    {
                        name: 'Font Weight',
                        property: 'font-weight',
                        type: 'select',
                        defaults: 'normal',
                        options: [
                            { value: '100', name: 'Thin' },
                            { value: '300', name: 'Light' },
                            { value: '400', name: 'Normal' },
                            { value: '500', name: 'Medium' },
                            { value: '600', name: 'Semi Bold' },
                            { value: '700', name: 'Bold' },
                            { value: '900', name: 'Black' },
                        ],
                    },
                    {
                        name: 'Color',
                        property: 'color',
                        type: 'color',
                        defaults: '#000000',
                    },
                    {
                        name: 'Text Align',
                        property: 'text-align',
                        type: 'radio',
                        defaults: 'left',
                        options: [
                            { value: 'left', name: 'Left', className: 'icon-align-left' },
                            { value: 'center', name: 'Center', className: 'icon-align-center' },
                            { value: 'right', name: 'Right', className: 'icon-align-right' },
                        ],
                    },
                    {
                        name: 'Line Height',
                        property: 'line-height',
                        type: 'integer',
                        units: ['px', 'em', 'rem'],
                        defaults: '1.5',
                        min: 1,
                        max: 3,
                        step: 0.1,
                    },
                ],
            },
            {
                name: 'Background',
                open: false,
                properties: [
                    {
                        name: 'Background Color',
                        property: 'background-color',
                        type: 'color',
                        defaults: 'transparent',
                    },
                    {
                        name: 'Background Image',
                        property: 'background-image',
                        type: 'file',
                        defaults: 'none',
                    },
                    {
                        name: 'Background Position',
                        property: 'background-position',
                        type: 'select',
                        defaults: 'left top',
                        options: [
                            { value: 'left top', name: 'Left Top' },
                            { value: 'center top', name: 'Center Top' },
                            { value: 'right top', name: 'Right Top' },
                            { value: 'left center', name: 'Left Center' },
                            { value: 'center center', name: 'Center' },
                            { value: 'right center', name: 'Right Center' },
                        ],
                    },
                    {
                        name: 'Background Size',
                        property: 'background-size',
                        type: 'select',
                        defaults: 'auto',
                        options: [
                            { value: 'auto', name: 'Auto' },
                            { value: 'cover', name: 'Cover' },
                            { value: 'contain', name: 'Contain' },
                        ],
                    },
                    {
                        name: 'Background Repeat',
                        property: 'background-repeat',
                        type: 'select',
                        defaults: 'repeat',
                        options: [
                            { value: 'repeat', name: 'Repeat' },
                            { value: 'no-repeat', name: 'No Repeat' },
                            { value: 'repeat-x', name: 'Repeat X' },
                            { value: 'repeat-y', name: 'Repeat Y' },
                        ],
                    },
                ],
            },
            {
                name: 'Border',
                open: false,
                properties: [
                    {
                        name: 'Border Width',
                        property: 'border-width',
                        type: 'integer',
                        units: ['px'],
                        defaults: '0',
                        min: 0,
                    },
                    {
                        name: 'Border Style',
                        property: 'border-style',
                        type: 'select',
                        defaults: 'solid',
                        options: [
                            { value: 'none', name: 'None' },
                            { value: 'solid', name: 'Solid' },
                            { value: 'dashed', name: 'Dashed' },
                            { value: 'dotted', name: 'Dotted' },
                        ],
                    },
                    {
                        name: 'Border Color',
                        property: 'border-color',
                        type: 'color',
                        defaults: '#000000',
                    },
                    {
                        name: 'Border Radius',
                        property: 'border-radius',
                        type: 'integer',
                        units: ['px', '%', 'rem'],
                        defaults: '0',
                        min: 0,
                    },
                ],
            },
            {
                name: 'Extra',
                open: false,
                properties: [
                    {
                        name: 'Opacity',
                        property: 'opacity',
                        type: 'slider',
                        defaults: '1',
                        min: 0,
                        max: 1,
                        step: 0.01,
                    },
                    {
                        name: 'Box Shadow',
                        property: 'box-shadow',
                        type: 'composite',
                        properties: [
                            { name: 'X', property: 'box-shadow-h' },
                            { name: 'Y', property: 'box-shadow-v' },
                            { name: 'Blur', property: 'box-shadow-blur' },
                            { name: 'Spread', property: 'box-shadow-spread' },
                            { name: 'Color', property: 'box-shadow-color', type: 'color' },
                        ],
                    },
                ],
            },
        ],
    },

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
            // Desktop harus widthMedia kosong ('') → style dibuat sebagai aturan
            // dasar (tanpa @media). Jika diberi widthMedia (mis. '992px'), semua
            // ubahan Desktop malah dibungkus `@media (max-width: 992px)` dan
            // tidak tampil di layar lebih lebar dari 992px.
            // Tablet pakai 768px (bukan 992) agar nilai 992px tetap identik
            // dengan "Desktop legacy" → aman utk migrasi @media 992 ke dasar.
            { id: 'Desktop', name: 'Desktop', width: '' },
            { id: 'Tablet', name: 'Tablet', width: '768px', widthMedia: '768px' },
            { id: 'Mobile portrait', name: 'Mobile', width: '375px', height: '667px', widthMedia: '480px' },
        ],
    },

    selectorManager: {
        componentFirst: true,
    },

    // Override panel utama GrapesJS agar memakai ikon Tabler (ti-*) — konsisten dengan
    // lingkungan Tabler app. FontAwesome tetap dimuat utk ikon internal GrapesJS lain.
    panels: {
        defaults: [
            {
                id: 'commands',
                buttons: [{}],
            },
            {
                id: 'options',
                buttons: [
                    {
                        id: 'sw-visibility',
                        active: true,
                        className: 'ti ti-view-360',
                        command: 'core:component-outline',
                        context: 'sw-visibility',
                        attributes: { title: 'Lihat komponen' },
                    },
                    {
                        id: 'preview',
                        className: 'ti ti-eye',
                        command: 'preview',
                        context: 'preview',
                        attributes: { title: 'Preview' },
                    },
                    {
                        id: 'fullscreen',
                        className: 'ti ti-maximize',
                        command: 'fullscreen',
                        context: 'fullscreen',
                        attributes: { title: 'Fullscreen' },
                    },
                    {
                        id: 'export-template',
                        className: 'ti ti-code',
                        command: 'export-template',
                        attributes: { title: 'Lihat kode' },
                    },
                ],
            },
            {
                id: 'views',
                buttons: [
                    {
                        id: 'open-sm',
                        className: 'ti ti-palette',
                        command: 'open-sm',
                        active: true,
                        togglable: false,
                        attributes: { title: 'Style Manager' },
                    },
                    {
                        id: 'open-tm',
                        className: 'ti ti-settings',
                        command: 'open-tm',
                        togglable: false,
                        attributes: { title: 'Settings' },
                    },
                    {
                        id: 'open-layers',
                        className: 'ti ti-layers',
                        command: 'open-layers',
                        togglable: false,
                        attributes: { title: 'Layer Manager' },
                    },
                    {
                        id: 'open-blocks',
                        className: 'ti ti-layout-grid',
                        command: 'open-blocks',
                        togglable: false,
                        attributes: { title: 'Blocks' },
                    },
                ],
            },
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
        migrateLegacyDesktopMedia(project);
        editor.loadProjectData(project);
        return;
    }
    if (cfg.html) {
        editor.setComponents(cfg.html);
        const css = cfg.css ? unwrapLegacyDesktopMediaCss(cfg.css) : '';
        if (css) editor.setStyle(css);
        return;
    }
    editor.setComponents(starterMarkup());
}

/**
 * Migrasi data lama: sebelum perbaikan device config, style "Desktop" tertulis
 * sebagai `@media (max-width: 992px)` (akibat widthMedia '992px' pada device
 * Desktop). Pindahkan kembali aturan itu ke level dasar (tanpa media query)
 * agar perubahan desktop benar-benar terlihat. Hanya aturan `max-width: 992px`
 * yang ditarget (device lama: Desktop→992, Tablet→768, Mobile→480).
 */
function migrateLegacyDesktopMedia(project) {
    if (!project || !Array.isArray(project.styles)) return;
    let migrated = 0;
    project.styles.forEach((rule) => {
        if (rule && typeof rule === 'object' && Array.isArray(rule.selectors) &&
            typeof rule.mediaText === 'string' && rule.mediaText.includes('max-width: 992px')) {
            rule.mediaText = '';
            migrated++;
        }
    });
    if (migrated) console.info(`[builder] Memigrasi ${migrated} aturan @media max-width:992px ke level dasar.`);
}

function unwrapLegacyDesktopMediaCss(css) {
    let out = String(css || '');
    let iterations = 0;
    while (iterations++ < 50) {
        const re = /@media\s*\(\s*max-width\s*:\s*992px\s*\)\s*\{([\s\S]*?)\n?\}/;
        const m = out.match(re);
        if (!m) break;
        out = out.replace(re, '') + '\n' + m[1];
    }
    return out;
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

/* ── Open Block Manager on load ─────────────────────────────── */
editor.on('load', () => {
    injectThemeSafely();
    setTimeout(() => {
        try { editor.Commands.run('open-blocks'); } catch (_) { /* no-op */ }
    }, 400);
    setTimeout(() => { injectThemeSafely(); }, 600);
});

/* ── Panel Exclusivity ────────────────────────────────────── */
/* Pastikan ketika user klik tab (SM/Blocks/Layers/TM),
   panel lain tertutup. SM content (kanan) dan Blocks panel (kiri)
   bisa tumpuk. Kita pastikan view buttons saling exclusive. */
editor.on('load', () => {
    const panelViews = editor.Panels.getPanel('views');
    if (!panelViews) return;
    panelViews.get('buttons').on('change:active', (btn) => {
        if (!btn.get('active')) return;
        panelViews.get('buttons').forEach(b => {
            if (b !== btn && b.get('active')) {
                b.set('active', false);
            }
        });
    });
});

/* ── Aksi toolbar ────────────────────────────────────────────── */

/* Satu snapshot BERSAING (latest-wins): autosave yang sedang berjalan
   (dengan data LAMA) harus tuntas dulu sebelum save manual mengirim data
   TERBARU, sehingga tidak ada tulisan balik dengan snapshot basi yang
   membuat perubahan terakhir tampak "balik ke semula". */
let saveInFlight = false;

/**
 * Line GrapesJS menyimpan override teks/trait sebagai rule CSS ber-selector id
 * (mis. `#ir2vk { font-style:normal }`) tetapi TIDAK menulis `id` ke atribut
 * komponen di project/HTML. Akibatnya rule hanya berfungsi di dalam kanvas
 * (GrapesJS menyuntik id saat render) dan menjadi "yatim" di halaman publik/
 * preview → perubahan tampak balik lagi. Fungsi ini menstempel `id` selector
 * ke atribut komponen sebelum snapshot diambil, sehingga `getHtml()` &
 * `getProjectData()` turut membawa id tersebut.
 */
function stampSelectorIds() {
    const wrapper = editor.getWrapper();
    if (!wrapper) return;
    editor.CssComposer.getAll().forEach((rule) => {
        rule.getSelectors().forEach((sel) => {
            if (!sel.isId()) return;
            const name = sel.get('name');
            if (!name) return;
            const found = wrapper.find(`#${name}`);
            if (!found.length) return;
            const comp = found[0];
            const attrs = comp.get('attributes') || {};
            if (attrs.id !== name) {
                comp.set('attributes', { ...attrs, id: name });
            }
        });
    });
}

function currentSnapshot() {
    stampSelectorIds();
    return {
        gjs_project: editor.getProjectData(),
        html: editor.getHtml(),
        css: editor.getCss(),
    };
}

/** Tunggu hingga autosave yang sedang berjalan selesai. */
async function flushAuto() {
    while (saveInFlight) {
        await new Promise((r) => setTimeout(r, 50));
    }
}

async function postSnapshot(payload) {
    const { data } = await axios.post(cfg.saveUrl, payload, {
        headers: { 'X-CSRF-TOKEN': cfg.csrf },
    });
    dirty = false;
    return data;
}

async function save(showToast = true) {
    clearTimeout(autosaveTimer);
    setStatus('Menyimpan…', 'busy');
    try {
        // Tunggu autosave lama selesai DULU, baru kirim snapshot terbaru —
        // urutan POST = manual diposting PERTAMA (latest-wins).
        await flushAuto();
        saveInFlight = true;
        const data = await postSnapshot(currentSnapshot());
        dirty = false;
        setStatus(data.message || 'Tersimpan', 'ok');
        if (showToast) toast('success', data.message || 'Project tersimpan.');
    } catch (err) {
        setStatus('Gagal menyimpan', 'error');
        if (showToast) toast('error', err.response?.data?.message || 'Terjadi kesalahan saat menyimpan.');
    } finally {
        saveInFlight = false;
    }
}

async function togglePublish() {
    const btn = $('#be-publish');
    const action = btn?.dataset.action;
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
    wireDeviceToggle();

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

/* ── Device Toggle ──────────────────────────────────────────── */
function wireDeviceToggle() {
    const deviceBtns = document.querySelectorAll('.be-device-btn');

    deviceBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const device = btn.dataset.device;
            deviceBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            editor.setDevice(device);
        });
    });

    editor.on('change:device', () => {
        const currentDevice = editor.getDevice();
        deviceBtns.forEach(btn => {
            if (btn.dataset.device === currentDevice) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    });
}

/* ── Autosave (debounce) ─────────────────────────────────────── */
let autosaveTimer = null;
let autosaveEnabledAt = Date.now();

async function autosave() {
    if (saveInFlight) {
        autosaveTimer = setTimeout(autosave, 800);
        return;
    }
    saveInFlight = true;
    try {
        await postSnapshot(currentSnapshot());
        setStatus('Disimpan otomatis ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }), 'ok');
    } catch (err) {
        setStatus('Autosave gagal — cek koneksi', 'error');
    } finally {
        saveInFlight = false;
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
    // Kirim snapshot TERBARU via keepalive agar perubahan menit terakhir
    // (belum sempat autosave) tidak hilang saat tab ditutup/direload.
    try {
        fetch(cfg.saveUrl, {
            method: 'POST',
            keepalive: true,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': cfg.csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(currentSnapshot()),
        });
    } catch (_) { /* abaikan — browser mungkin menolak keepalive di unload */ }
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

setTimeout(() => {
    try { editor.refresh(); } catch (e) { /* abaikan */ }
}, 50);
