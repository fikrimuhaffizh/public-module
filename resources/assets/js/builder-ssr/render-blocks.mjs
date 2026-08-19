// Render semua variant section React (Mode{n}.jsx) menjadi HTML statis untuk
// block library GrapesJS di Website Builder.
//
// Cara pakai:
//   node Modules/Public/resources/assets/js/builder-ssr/render-blocks.mjs
//
// Keluaran: storage/app/builder/builder_blocks.json
// (nama, kategori, ikon, HTML per variant per section).

import { createServer } from 'vite';
import React from 'react';
import { renderToString } from 'react-dom/server';
import path from 'node:path';
import fs from 'node:fs';
import { fileURLToPath } from 'node:url';
import { sampleProps } from './sample-content.js';

const ROOT = path.resolve(
    path.dirname(path.dirname(path.dirname(path.dirname(path.dirname(
        path.dirname(path.dirname(fileURLToPath(import.meta.url))))))))
);
const OUT = path.join(ROOT, 'storage/app/builder/builder_blocks.json');
const REGISTRY = '/Modules/Public/resources/assets/js/components/sections/registry.js';

// Ikons per section (Tabler). Saat komponen mengekspor `icon`, dipakai itu.
const ICON_FALLBACK = {
    pageheader: 'heading', navbar: 'layout-navbar', topbar: 'topbar',
    hero: 'rocket', product: 'shopping-bag', statistic: 'chart-bar',
    feature: 'layout-grid', testimonial: 'message-circle', client: 'building',
    faq: 'help', price: 'tags', cta: 'target', gallery: 'photo',
    pengumuman: 'bell', marquee: 'align-center', footer: 'layout',
};

/** Lambang awal animasi React (opacity:0/translateY) disembunyikan supaya blok
 * statis di builder terlihat utuh tanpa perlu JS. */
function neutralizeMotionStyles(html) {
    return html.replace(/style="([^"]*)"/g, (match, raw) => {
        const parts = raw.split(';')
            .map((s) => s.trim())
            .filter((s) => s && !/^opacity:\s*0$/i.test(s) && !/^transform:/i.test(s) && !/^filter:/i.test(s));
        return `style="${parts.join(';')}"`;
    });
}

/** Props yang dipakai komponen layout (navbar/footer) beda dari section. */
const specialProps = {
    navbar: { site: sampleProps.site, menus: sampleProps.menus },
    footer: { site: sampleProps.site, footerMenus: sampleProps.footerMenus },
};

function sectionKeyName(section) {
    return String(section.section_key).replace(/[-_]+/g, ' ');
}

const vite = await createServer({
    configFile: path.join(ROOT, 'vite.config.js'),
    server: { middlewareMode: true, hmr: false },
    appType: 'custom',
});

let SECTION_META = [];
let SECTION_VARIANTS = {};
try {
    const mod = await vite.ssrLoadModule(REGISTRY);
    SECTION_META = mod.SECTION_META || [];
    SECTION_VARIANTS = mod.SECTION_VARIANTS || {};
} catch (e) {
    console.error('Gagal load registry:', e.message);
    process.exit(1);
}

const metaMap = Object.fromEntries(
    (SECTION_META || []).map((m) => [m.key, m])
);

const blocks = [];
const failures = [];

for (const [key, variant] of Object.entries(SECTION_VARIANTS ?? {})) {
    const meta = metaMap[key] || variant.meta || {};
    const sectionName = meta.name || key;
    const icon = variant.icon || ICON_FALLBACK[key];
    const variants = variant.variants || [];

    for (const v of variants) {
        const name = v.name || String(v.key || '').replace(/[-_]+/g, ' ');
        try {
            const props = specialProps[key]
                ? specialProps[key]
                : (() => {
                      const section = {
                          landing_section_id: 1,
                          section_key: key,
                          section_name: sectionName,
                          area: 'middle',
                          title: '',
                          pre_title: '',
                          post_title: '',
                          subtitle: '',
                          is_active: true,
                          limit_data: 3,
                          variant: v.key || null,
                          settings: { text_align: 'center' },
                      };
                      return { section, data: { ...sampleProps, sections: [section] } };
                  })();

            const html = renderToString(React.createElement(v.component, props));
            const cleaned = neutralizeMotionStyles(html).trim();
            blocks.push({
                type: key,
                mode: v.key,
                name,
                label: sectionName + ' · ' + name,
                category: sectionName,
                icon: icon || 'box',
                html: cleaned,
            });
        } catch (e) {
            failures.push({ type: key, mode: v.key, name, error: (e.message || '').split('\n')[0] });
        }
    }
}

fs.mkdirSync(path.dirname(OUT), { recursive: true });
fs.writeFileSync(OUT, JSON.stringify({
    generator: 'builder-ssr/render-blocks.mjs',
    generated_at: new Date().toISOString(),
    source: 'resources/assets/js/components/sections',
    count: blocks.length,
    failures,
    blocks,
}, null, 2));

console.log(`OK: ${blocks.length} blok, ${failures.length} gagal → ${OUT}`);
for (const f of failures) console.log(`  ✗ ${f.type}/${f.mode} ${f.name}: ${f.error}`);
process.exit(0);