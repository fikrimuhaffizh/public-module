import React from 'react';

/**
 * Registri variant section - satu sumber kebenaran untuk daftar "Sections"
 * di Theme Settings (offcanvas /preview) DAN renderer section.
 *
 * AUTO-DISCOVERY: daftar mode dibaca langsung dari file komponen di folder
 * section-nya. Developer cukup menambah file dengan pola `Mode{n}.jsx`:
 *
 *     components/sections/faq/Mode5.jsx  =>  otomatis menjadi "Mode 5"
 *
 * File yang bukan variant (helper: FaqReveal, HeroActions, CountUp, hours.js)
 * otomatis diabaikan karena namanya tidak memuat `Mode{digit}`.
 *
 * Komponen menerima prop { section, data } (data = props Inertia lengkap),
 * atau { site, menus, open, onToggle } untuk navbar, { site, footerMenus }
 * untuk footer.
 *
 * ─── KONTRAK ELEMEN ROOT (penting!) ─────────────────────────────────
 *   Elemen root komponen variant HARUS berupa <section> (konten), <header>
 *   (navbar), atau <footer> (footer). Alasan:
 * Override warna per section (.sec-colored) memakai CSS vars pada
 *     wrapper, dan CSS menembus lewat selektor `section/header/footer`;
 * Root <div> membuat override warna dan styling template tidak bekerja
 *     (guard dev-mode di renderer akan memperingatkan, lihat konsol).
 *   Jangan bungkus root dengan <div> lain. ───────────────────────────
 */

/** Metadata per section (bukan per mode): nama, heading, align. */
const SECTION_META = [
    { key: 'pageheader', dir: 'pageheader', name: 'Page Header' },
    { key: 'navbar', dir: 'navbar', name: 'Navbar' },
    { key: 'topbar', dir: 'topbar', name: 'Top Bar' },
    { key: 'hero', dir: 'hero', name: 'Hero', heading: true, align: false },
    { key: 'product', dir: 'product', name: 'Produk', heading: true, limit: true },
    { key: 'statistic', dir: 'statistic', name: 'Statistik', heading: true, limit: true },
    { key: 'feature', dir: 'feature', name: 'Fitur', heading: true, limit: true },
    { key: 'testimonial', dir: 'testimonial', name: 'Testimoni', heading: true, limit: true },
    { key: 'client', dir: 'client', name: 'Klien / Logo', heading: true, limit: true },
    { key: 'faq', dir: 'faq', name: 'FAQ', heading: true, limit: true },
    { key: 'pengumuman', dir: 'announcement', name: 'Pengumuman', heading: true, limit: true },
    { key: 'cta', dir: 'cta', name: 'Call to Action', heading: true, align: false },
    { key: 'price', dir: 'price', name: 'Harga / Paket', heading: true },
    { key: 'footer', dir: 'footer', name: 'Footer' },
];

// Import semua komponen section secara sinkron (eager) - setara dengan
// daftar import manual lama, tapi otomatis mengikuti isi folder.
const SECTION_MODULES = import.meta.glob('./*/Mode[0-9]*.jsx');

/** Ambil daftar variant dari file `Mode{n}.jsx` dalam satu folder section. */
function variantsFromFolder(dir, sectionKey) {
    return Object.entries(SECTION_MODULES)
        .map(([path, load]) => {
            const match = path.match(new RegExp(`^\\./${dir}/Mode(\\d+)\\.jsx$`));
            if (!match) return null;
            const num = Number(match[1]);
            const Component = React.lazy(load);
            return {
                key: `${sectionKey}_${num}`,
                name: `Mode ${num}`,
                component: props => React.createElement(React.Suspense, {
                    fallback: React.createElement('section', { 'aria-busy': true, 'aria-label': 'Memuat bagian halaman', style: { minHeight: 80 } }),
                }, React.createElement(Component, props)),
            };
        })
        .filter(Boolean)
        .sort((a, b) => Number(a.key.split('_').pop()) - Number(b.key.split('_').pop()));
}

/**
 * Key section mengikuti `sectionKey()` (alias dibukukan): clients => client,
 * products => product, stats => statistic, dsb.
 */
export const SECTION_VARIANTS = Object.fromEntries(
    SECTION_META.map(({ key, dir, ...meta }) => [
        key,
        { ...meta, variants: variantsFromFolder(dir, key) },
    ])
);

/** Ambil daftar variant untuk satu section key. */
export function sectionVariants(sectionKey) {
    return SECTION_VARIANTS[sectionKey]?.variants || [];
}

/** Ambil entry variant (atau yang pertama sebagai fallback). */
export function resolveVariant(sectionKey, variantKey) {
    const list = sectionVariants(sectionKey);
    return list.find(v => v.key === variantKey) || list[0] || null;
}
