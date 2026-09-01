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
import grapesjs from '../vendor/grapesjs/js/grapes.mjs';
import '../vendor/grapesjs/css/grapes.min.css';
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

/* ── CMS-backed Blocks ────────────────────────────────────── */
/* Section blocks (FAQ, Testimonials, Statistics) otomatis mengambil data
   dari database saat publish. Admin tetap edit wrapper/title/styling.
   data-cms-source di Blade component menandai bagian yang dari DB.
   Lihat: Modules/Public/resources/views/components/builder/*.blade.php */

/* OLD dynamic blocks code removed — CMS-backed blocks handled server-side */
const STATIC_TEMPLATES_REMOVE_ME = {
    faq: [
        '<section class="wbp-section wbp-bg-white wbp-py-lg">',
        '  <div class="wbp-container wbp-container-narrow">',
        '    <h2 class="wbp-title wbp-title-lg wbp-text-center" style="margin-bottom:2rem">Frequently Asked Questions</h2>',
        '    <div class="wbp-faq">',
        '      <details>',
        '        <summary>Bagaimana cara mendaftar sebagai mahasiswa baru?</summary>',
        '        <div class="wbp-faq-body">Pendaftaran dapat dilakukan secara online melalui portal PMB.</div>',
        '      </details>',
        '      <details>',
        '        <summary>Program studi apa saja yang tersedia?</summary>',
        '        <div class="wbp-faq-body">Kami memiliki berbagai program studi di bidang Teknologi, Bisnis, dan Ilmu Sosial.</div>',
        '      </details>',
        '      <details>',
        '        <summary>Apakah tersedia beasiswa?</summary>',
        '        <div class="wbp-faq-body">Ya, tersedia berbagai jenis beasiswa untuk mahasiswa berprestasi.</div>',
        '      </details>',
        '    </div>',
        '  </div>',
        '</section>',
    ].join('\n'),

    pengumuman: [
        '<section class="wbp-section wbp-bg-gray wbp-py-lg">',
        '  <div class="wbp-container">',
        '    <h2 class="wbp-title wbp-title-lg wbp-text-center" style="margin-bottom:2rem">Berita & Pengumuman</h2>',
        '    <div class="wbp-grid wbp-grid-3">',
        '      <div class="wbp-card">',
        '        <div class="wbp-card-body">',
        '          <span class="wbp-card-date">20 Agustus 2026</span>',
        '          <h3 class="wbp-card-title">Pengumuman Kampus</h3>',
        '          <p class="wbp-card-text">Isi pengumuman dapat diedit sesuai kebutuhan.</p>',
        '        </div>',
        '      </div>',
        '      <div class="wbp-card">',
        '        <div class="wbp-card-body">',
        '          <span class="wbp-card-date">19 Agustus 2026</span>',
        '          <h3 class="wbp-card-title">Berita Terbaru</h3>',
        '          <p class="wbp-card-text">Tambahkan berita dan informasi terkini di sini.</p>',
        '        </div>',
        '      </div>',
        '      <div class="wbp-card">',
        '        <div class="wbp-card-body">',
        '          <span class="wbp-card-date">18 Agustus 2026</span>',
        '          <h3 class="wbp-card-title">Acara Mendatang</h3>',
        '          <p class="wbp-card-text">Jadwalkan acara dan kegiatan kampus di sini.</p>',
        '        </div>',
        '      </div>',
        '    </div>',
        '  </div>',
        '</section>',
    ].join('\n'),

    testimonial: [
        '<section class="wbp-section wbp-bg-white wbp-py-lg">',
        '  <div class="wbp-container">',
        '    <h2 class="wbp-title wbp-title-lg wbp-text-center" style="margin-bottom:2rem">Apa Kata Mereka</h2>',
        '    <div class="wbp-grid wbp-grid-3">',
        '      <div class="wbp-card wbp-card-testimonial">',
        '        <div class="wbp-card-body">',
        '          <div class="wbp-testimonial-stars">⭐⭐⭐⭐⭐</div>',
        '          <p class="wbp-testimonial-quote">"Platform kampus membantu saya menemukan layanan dan informasi akademik dengan cepat."</p>',
        '          <div class="wbp-testimonial-author">',
        '            <img src="https://ui-avatars.com/api/?name=Ahmad" alt="Ahmad" class="wbp-testimonial-avatar">',
        '            <div><strong>Ahmad Fauzi</strong><span>Mahasiswa Teknik Informatika</span></div>',
        '          </div>',
        '        </div>',
        '      </div>',
        '      <div class="wbp-card wbp-card-testimonial">',
        '        <div class="wbp-card-body">',
        '          <div class="wbp-testimonial-stars">⭐⭐⭐⭐⭐</div>',
        '          <p class="wbp-testimonial-quote">"Sangat membantu untuk pencarian informasi perkuliahan sehari-hari."</p>',
        '          <div class="wbp-testimonial-author">',
        '            <img src="https://ui-avatars.com/api/?name=Siti" alt="Siti" class="wbp-testimonial-avatar">',
        '            <div><strong>Siti Nurhaliza</strong><span>Mahasiswa Manajemen</span></div>',
        '          </div>',
        '        </div>',
        '      </div>',
        '      <div class="wbp-card wbp-card-testimonial">',
        '        <div class="wbp-card-body">',
        '          <div class="wbp-testimonial-stars">⭐⭐⭐⭐⭐</div>',
        '          <p class="wbp-testimonial-quote">"Fitur akademiknya lengkap dan mudah digunakan."</p>',
        '          <div class="wbp-testimonial-author">',
        '            <img src="https://ui-avatars.com/api/?name=Budi" alt="Budi" class="wbp-testimonial-avatar">',
        '            <div><strong>Budi Santoso</strong><span>Mahasiswa Akuntansi</span></div>',
        '          </div>',
        '        </div>',
        '      </div>',
        '    </div>',
        '  </div>',
        '</section>',
    ].join('\n'),

    statistik: [
        '<section class="wbp-section wbp-bg-brand wbp-py-lg">',
        '  <div class="wbp-container">',
        '    <div class="wbp-statistics">',
        '      <div class="wbp-statistic"><div class="wbp-statistic-value">10,000+</div><div class="wbp-statistic-label">Mahasiswa Aktif</div></div>',
        '      <div class="wbp-statistic"><div class="wbp-statistic-value">50+</div><div class="wbp-statistic-label">Program Studi</div></div>',
        '      <div class="wbp-statistic"><div class="wbp-statistic-value">200+</div><div class="wbp-statistic-label">Dosen</div></div>',
        '      <div class="wbp-statistic"><div class="wbp-statistic-value">95%</div><div class="wbp-statistic-label">Tingkat Kelulusan</div></div>',
        '    </div>',
        '  </div>',
        '</section>',
    ].join('\n'),
};

/**
 * Build placeholder string from trait values.
 */
function buildPlaceholder(type, traits) {
    const params = [];
    Object.entries(traits).forEach(([key, value]) => {
        if (value !== undefined && value !== '' && value !== null) {
            params.push(`${key}=${value}`);
        }
    });
    return `{{${type}:${params.join('&')}}}`;
}

/**
 * Build dynamic indicator badge HTML.
 */
function dynamicBadge(type) {
    return `<div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;padding:6px 12px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border-radius:8px;font-size:12px;font-weight:600;">` +
        `<i class="ti ti-database" style="font-size:14px"></i> ` +
        `<span>🔗 Data CMS: ${type.toUpperCase()}</span>` +
        `<span style="margin-left:auto;font-size:10px;opacity:0.8">auto-update</span>` +
        `</div>`;
}

function addDynamicBlocks() {
    const dynamicBlocks = [
        {
            id: 'dynamic-faq',
            label: 'FAQ',
            category: 'Konten Dinamis',
            icon: 'help-circle',
            dynamicType: 'faq',
            traitDefaults: { category: '', limit: 5, is_active: 'true' },
            traitDefs: [
                {
                    type: 'checkbox',
                    name: 'use_dynamic',
                    label: '🗄️ Gunakan Data CMS',
                    value: false,
                },
                {
                    type: 'select',
                    name: 'category',
                    label: 'Kategori',
                    options: [
                        { value: '', name: 'Semua' },
                        { value: 'akademik', name: 'Akademik' },
                        { value: 'administrasi', name: 'Administrasi' },
                        { value: 'umum', name: 'Umum' },
                    ],
                },
                {
                    type: 'number',
                    name: 'limit',
                    label: 'Jumlah Item',
                    min: 1,
                    max: 20,
                    value: 5,
                },
            ],
        },
        {
            id: 'dynamic-pengumuman',
            label: 'Pengumuman',
            category: 'Konten Dinamis',
            icon: 'megaphone',
            dynamicType: 'pengumuman',
            traitDefaults: { type: '', limit: 3, is_published: 'true' },
            traitDefs: [
                {
                    type: 'checkbox',
                    name: 'use_dynamic',
                    label: '🗄️ Gunakan Data CMS',
                    value: false,
                },
                {
                    type: 'select',
                    name: 'type',
                    label: 'Jenis',
                    options: [
                        { value: '', name: 'Semua' },
                        { value: 'berita', name: 'Berita' },
                        { value: 'pengumuman', name: 'Pengumuman' },
                        { value: 'agenda', name: 'Agenda' },
                    ],
                },
                {
                    type: 'number',
                    name: 'limit',
                    label: 'Jumlah Item',
                    min: 1,
                    max: 12,
                    value: 3,
                },
            ],
        },
        {
            id: 'dynamic-testimonial',
            label: 'Testimonial',
            category: 'Konten Dinamis',
            icon: 'quote',
            dynamicType: 'testimonial',
            traitDefaults: { limit: 3, is_active: 'true' },
            traitDefs: [
                {
                    type: 'checkbox',
                    name: 'use_dynamic',
                    label: '🗄️ Gunakan Data CMS',
                    value: false,
                },
                {
                    type: 'number',
                    name: 'limit',
                    label: 'Jumlah Item',
                    min: 1,
                    max: 12,
                    value: 3,
                },
            ],
        },
        {
            id: 'dynamic-statistik',
            label: 'Statistik',
            category: 'Konten Dinamis',
            icon: 'chart-bar',
            dynamicType: 'statistik',
            traitDefaults: { limit: 4, is_active: 'true' },
            traitDefs: [
                {
                    type: 'checkbox',
                    name: 'use_dynamic',
                    label: '🗄️ Gunakan Data CMS',
                    value: false,
                },
                {
                    type: 'number',
                    name: 'limit',
                    label: 'Jumlah Item',
                    min: 1,
                    max: 8,
                    value: 4,
                },
            ],
        },
    ];

    dynamicBlocks.forEach((block) => {
        // --- Block definition ---
        editor.BlockManager.add(block.id, {
            label: block.label,
            category: block.category,
            media: blockMedia(block.icon),
            // Default: static mode — admin bisa langsung edit
            content: {
                type: 'text',
                content: STATIC_TEMPLATES[block.dynamicType],
                attributes: {
                    'data-dynamic-type': block.dynamicType,
                    'data-dynamic-enabled': 'false',
                },
                classes: ['wbp-dynamic-block', 'wbp-dynamic-static'],
            },
            activate: true,
            select: true,
        });

        // --- Component type with traits ---
        editor.DomComponents.addType(block.id, {
            model: {
                defaults: {
                    traits: [
                        // Use CMS Data toggle
                        {
                            type: 'checkbox',
                            name: 'use_dynamic',
                            label: '🗄️ Gunakan Data CMS',
                            value: false,
                        },
                        // Dynamic config traits (shown only when toggle ON)
                        ...block.traitDefs.filter(t => t.name !== 'use_dynamic'),
                    ],
                    // Store static content so we can restore it when toggling OFF
                    'data-static-content': STATIC_TEMPLATES[block.dynamicType],
                    'data-dynamic-enabled': 'false',
                },
            },

            view: {
                events: {
                    change: 'onTraitChange',
                },

                init() {
                    // Listen for trait changes (the toggle)
                    this.model.get('traits').on('change', this.onTraitChange.bind(this));
                },

                onTraitChange() {
                    const model = this.model;
                    const useDynamic = model.getTrait('use_dynamic')?.getValue();
                    const dynType = model.get('data-dynamic-type') || block.dynamicType;

                    if (useDynamic) {
                        // ── Toggle ON: Switch to dynamic placeholder ──
                        // Save current content as static before switching
                        const currentContent = model.get('content') || '';
                        if (currentContent && !currentContent.startsWith('{{')) {
                            model.set('data-static-content', currentContent);
                        }

                        // Build trait values for placeholder
                        const traitValues = {};
                        block.traitDefs.filter(t => t.name !== 'use_dynamic').forEach((t) => {
                            const val = model.getTrait(t.name)?.getValue() ?? t.value;
                            if (val !== '' && val !== undefined && val !== null) {
                                traitValues[t.name] = val;
                            }
                        });

                        // Set placeholder content with dynamic badge
                        const placeholder = buildPlaceholder(dynType, traitValues);
                        model.set('content', dynamicBadge(dynType) + `<div style="display:block;padding:2rem;background:linear-gradient(135deg,#eff6ff,#dbeafe);border:2px dashed #3b82f6;border-radius:12px;text-align:center;color:#1e40af;font-size:14px;">${placeholder}</div>`);
                        model.set('data-dynamic-enabled', 'true');

                        // Update CSS class
                        const classes = model.get('classes');
                        classes.remove('wbp-dynamic-static');
                        classes.add('wbp-dynamic-active');

                    } else {
                        // ── Toggle OFF: Restore static content ──
                        const staticContent = model.get('data-static-content') || STATIC_TEMPLATES[block.dynamicType];
                        model.set('content', staticContent);
                        model.set('data-dynamic-enabled', 'false');

                        // Update CSS class
                        const classes = model.get('classes');
                        classes.remove('wbp-dynamic-active');
                        classes.add('wbp-dynamic-static');
                    }
                },
            },
        });
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
/* CMS-backed blocks handled server-side — no client-side init needed */
wireToolbar();
setStatus(dirty ? 'Belum disimpan' : 'Siap', dirty ? 'dirty' : 'idle');

setTimeout(() => {
    try { editor.refresh(); } catch (e) { /* abaikan */ }
}, 50);
