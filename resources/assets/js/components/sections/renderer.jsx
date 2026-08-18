import React from 'react';
import { useThemeCustomizer } from '@public/components/theme/ThemeCustomizer';
import { sectionKey } from './index';
import { resolveVariant } from './registry';

/**
 * Section yang dirender layout-level (PublicLayout), bukan dari alur konten
 * template — variant-nya dipakai di navbar/footer, jadi dilewati di sini.
 */
const LAYOUT_SECTIONS = ['navbar', 'footer', 'topbar', 'pageheader'];

/** Elemen root yang sah untuk komponen variant — lihat kontrak di registry.js. */
const ROOT_TAGS = ['SECTION', 'HEADER', 'FOOTER'];

/**
 * Guard dev-mode: peringatkan saat komponen variant memakai elemen root
 * selain <section>/<header>/<footer> (mis. <div>), karena override warna
 * (.sec-colored) dan styling template tidak akan bekerja. Tanpa efek di
 * produksi — wrapper memakai display:contents (tidak membentuk kotak).
 */
function SectionRootGuard({ name, children }) {
    const ref = React.useRef(null);
    React.useEffect(() => {
        const root = ref.current?.firstElementChild;
        if (root && !ROOT_TAGS.includes(root.tagName)) {
            console.warn(
                `[sections] "${name}" memakai elemen root <${root.tagName.toLowerCase()}>, ` +
                `seharusnya <section>/<header>/<footer> — override warna & styling template bisa gagal. ` +
                `Lihat kontrak di components/sections/registry.js`
            );
        }
    }, []);
    return (
        <span ref={ref} style={{ display: 'contents' }}>
            {children}
        </span>
    );
}

/**
 * Style object (CSS vars) untuk override warna per-section dari Theme
 * Settings. Return null bila tidak ada override — dipakai di SectionVariantRenderer
 * dan di layout (navbar/footer) supaya konsisten.
 */
export function sectionColorStyle(colors) {
    if (!colors || typeof colors !== 'object') return null;
    const style = {};
    if (colors.bg) style['--sec-bg'] = colors.bg;
    if (colors.text) style['--sec-text'] = colors.text;
    if (colors.heading || colors.text) style['--sec-heading'] = colors.heading || colors.text;
    if (colors.accent) style['--sec-accent'] = colors.accent;
    if (colors.image) style['--sec-image'] = `url("${colors.image}")`;
    return Object.keys(style).length ? style : null;
}

/**
 * Render satu section memakai komponen variant-nya.
 *
 * Urutan variant: override dari Theme Settings (offcanvas /preview, live)
 * → variant tersimpan di DB (section.variant) → variant pertama registry.
 * Override warna per-section diterapkan lewat wrapper .sec-colored (CSS vars).
 *
 * Komponen variant menerima { section, data } dan bertanggung jawab penuh
 * atas markup + heading-nya sendiri (bebas pakai Section/SectionHeader).
 */
export function SectionVariantRenderer({ section, data }) {
    const customizer = useThemeCustomizer();
    const key = sectionKey(section);
    if (LAYOUT_SECTIONS.includes(key)) return null;

    const override = customizer?.sectionVariants?.[key];
    const variant = resolveVariant(key, override || section?.variant);
    if (!variant) return null;

    const Component = variant.component;
    // Override teks (pretitle/title/subtitle) dari Theme Settings offcanvas.
    const textOverride = customizer?.sectionSettings?.[key] || {};
    const mergedSection = {
        ...section,
        ...(textOverride.pre_title != null ? { pre_title: textOverride.pre_title } : {}),
        ...(textOverride.title != null ? { title: textOverride.title } : {}),
        ...(textOverride.subtitle != null ? { subtitle: textOverride.subtitle } : {}),
        ...(textOverride.limit_data != null ? { limit_data: textOverride.limit_data } : {}),
        settings: {
            ...(section.settings || {}),
            ...(textOverride.text_align != null ? { text_align: textOverride.text_align } : {}),
        },
    };
    const content = import.meta.env.DEV
        ? <SectionRootGuard name={variant.name}><Component section={mergedSection} data={data} /></SectionRootGuard>
        : <Component section={mergedSection} data={data} />;

    const colors = customizer?.sectionColors?.[key];
    const hasBg = Boolean(colors?.bg || colors?.pattern || colors?.image);
    if (!hasBg) return content;

    const style = sectionColorStyle(colors);
    const patternClass = colors?.pattern ? ` sec-colored--p-${colors.pattern}` : '';
    const imgClass = colors?.image ? ' sec-colored--img' : '';
    return (
        <div className={`sec-colored${hasBg ? ' sec-colored--bg' : ''}${patternClass}${imgClass}`} style={style}>
            {content}
        </div>
    );
}

/** Variant aktif untuk section key tertentu (untuk layout-level seperti navbar/footer). */
export function useSectionVariant(sectionKeyName, fallback) {
    const customizer = useThemeCustomizer();
    return customizer?.sectionVariants?.[sectionKeyName] || fallback;
}
