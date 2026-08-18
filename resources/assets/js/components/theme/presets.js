/**
 * Presets & helper data Theme Customizer — konstanta murni tanpa React.
 * Dipakai oleh ThemeCustomizerContext (state) dan ThemeSettingsDrawer (UI).
 */

// Struktur navbar & tombol tidak lagi di sini — navbar diatur per-section
// (variant Mode 1/2/3), tombol ikut knob Sudut (Radius).

// Radius global — diterapkan lewat class theme-custom-radius--<key> di root
// (blok CSS di css/themes/*.css memetakan tiap key ke nilai sudut).
export const RADIUS_OPTIONS = [
    { key: 'square', label: 'Kotak' },
    { key: 'default', label: 'Standar' },
    { key: 'rounded', label: 'Membulat' },
    { key: 'pill', label: 'Pill' },
];

// Kepadatan halaman — mengatur jarak vertikal antar section (padding
// section + heading). Diterapkan lewat class theme-custom-density--<key>.
export const DENSITY_OPTIONS = [
    { key: 'compact', label: 'Padat' },
    { key: 'standard', label: 'Standar' },
    { key: 'spacious', label: 'Lega' },
];

// Elevasi kartu (shadow) — seberapa "terangkat" kartu dari permukaan.
// Diterapkan lewat class theme-custom-elevation--<key> di root.
export const ELEVATION_OPTIONS = [
    { key: 'flat', label: 'Flat' },
    { key: 'soft', label: 'Lembut' },
    { key: 'medium', label: 'Sedang' },
    { key: 'strong', label: 'Tajam' },
];

// Mode gelap — class theme-custom-dark di root mengganti CSS vars permukaan.
export const DARK_VARS = {
    '--background': '#0b1220',
    '--card': '#131c2e',
    '--foreground': '#e6edf7',
    '--muted': '#93a7c2',
    '--border': '#22304a',
    '--tint': '#101a2c',
};

// Pola latar abstrak per-section — kunci memetakan ke class .sec-colored--p-<key>
// di css/sections/*.css; dipakai popover warna (tab Pola) + renderer.
export const SECTION_PATTERNS = [
    { key: 'dots', name: 'Titik' },
    { key: 'grid', name: 'Grid' },
    { key: 'diagonal', name: 'Garis miring' },
    { key: 'waves', name: 'Gelombang' },
    { key: 'beams', name: 'Sorotan' },
    { key: 'noise', name: 'Tekstur' },
];

// Preset latar per-section — field yang null berarti ikut tema (tidak di-override).
// Preset 'accent' diisi runtime: bg = primary tema, aksen = accent tema.
export const SECTION_COLOR_PRESETS = [
    { key: 'light', name: 'Terang', bg: '#f8fafc', text: '#102033' },
    { key: 'tint', name: 'Abu lembut', bg: '#eef2f7', text: '#102033' },
    { key: 'cream', name: 'Krem', bg: '#fbf3e4', text: '#42321a' },
    { key: 'mint', name: 'Pastel mint', bg: '#eef7f1', text: '#123024' },
    { key: 'dark', name: 'Gelap', bg: '#0f172a', text: '#e2e8f0', accent: '#fbbf24' },
    { key: 'ink', name: 'Hitam pekat', bg: '#0b1220', text: '#e2e8f0' },
    { key: 'accent', name: 'Aksen tema', text: '#ffffff' }, // bg diisi runtime = primary
];

// Fallback palet bila metadata tema belum punya nilai (institusi dll.)
export const FALLBACK_PALETTE = {
    primary: '#155eef', primaryDark: '#0b3b91', accent: '#f59e0b',
    background: '#ffffff', card: '#ffffff', foreground: '#102033',
    muted: '#64748b', border: '#e2e8f0', uiPrimary: '218 85% 51%',
};

/**
 * Kurasi pasangan font (heading/body) yang cocok untuk front-end.
 * SEMUA keluarga font di daftar ini harus termuat — lihat @import
 * Google Fonts di landing.css. Pasangan font dari metadata tema backend
 * (themeOptions[].font) ditambahkan di atas daftar ini oleh collectFonts().
 */
export const FONT_OPTIONS = [
    { key: 'modern', name: 'Modern', heading: "'Manrope', ui-sans-serif, system-ui, sans-serif", body: "'DM Sans', ui-sans-serif, system-ui, sans-serif" },
    { key: 'inter', name: 'Inter', heading: "'Inter', ui-sans-serif, system-ui, sans-serif", body: "'Inter', ui-sans-serif, system-ui, sans-serif" },
    { key: 'jakarta', name: 'Plus Jakarta', heading: "'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif", body: "'Inter', ui-sans-serif, system-ui, sans-serif" },
    { key: 'sora', name: 'Sora', heading: "'Sora', ui-sans-serif, system-ui, sans-serif", body: "'Inter', ui-sans-serif, system-ui, sans-serif" },
    { key: 'space', name: 'Space Grotesk', heading: "'Space Grotesk', ui-sans-serif, system-ui, sans-serif", body: "'Inter', ui-sans-serif, system-ui, sans-serif" },
    { key: 'figtree', name: 'Figtree', heading: "'Figtree', ui-sans-serif, system-ui, sans-serif", body: "'Figtree', ui-sans-serif, system-ui, sans-serif" },
    { key: 'outfit', name: 'Outfit', heading: "'Outfit', ui-sans-serif, system-ui, sans-serif", body: "'DM Sans', ui-sans-serif, system-ui, sans-serif" },
    { key: 'poppins', name: 'Poppins', heading: "'Poppins', ui-sans-serif, system-ui, sans-serif", body: "'Inter', ui-sans-serif, system-ui, sans-serif" },
    { key: 'serif', name: 'Serif', heading: "'Playfair Display', Georgia, serif", body: "'Source Serif 4', Georgia, serif" },
    { key: 'rounded', name: 'Rounded', heading: "'Baloo 2', ui-rounded, system-ui, sans-serif", body: "'DM Sans', ui-sans-serif, system-ui, sans-serif" },
    { key: 'condensed', name: 'Condensed', heading: "'Archivo', ui-sans-serif, system-ui, sans-serif", body: "'Manrope', ui-sans-serif, system-ui, sans-serif" },
];

export function paletteToVars(p) {
    const c = { ...FALLBACK_PALETTE, ...p };
    return {
        '--primary': c.primary,
        '--primary-dark': c.primaryDark,
        '--accent': c.accent,
        '--background': c.background,
        '--card': c.card,
        '--foreground': c.foreground,
        '--muted': c.muted,
        '--border': c.border,
        '--ui-primary': c.uiPrimary,
    };
}

/** Kumpulkan palet unik (dedupe per primary) dari metadata semua tema. */
export function collectPalettes(themeOptions) {
    const seen = new Map();
    Object.entries(themeOptions || {}).forEach(([key, meta]) => {
        const p = meta.palette || {};
        const primary = p.primary || FALLBACK_PALETTE.primary;
        if (seen.has(primary)) return;
        seen.set(primary, {
            key,
            name: meta.name || key,
            primary,
            primaryDark: p['primary-dark'] || FALLBACK_PALETTE.primaryDark,
            accent: p.accent || FALLBACK_PALETTE.accent,
            background: p.background || FALLBACK_PALETTE.background,
            card: p.card || FALLBACK_PALETTE.card,
            foreground: p.foreground || FALLBACK_PALETTE.foreground,
            muted: p.muted || FALLBACK_PALETTE.muted,
            border: p.border || FALLBACK_PALETTE.border,
            uiPrimary: p['ui-primary'] || FALLBACK_PALETTE.uiPrimary,
        });
    });
    return [...seen.values()];
}

/**
 * Kumpulkan pasangan font: kurasi FONT_OPTIONS + font dari metadata tema
 * (themeOptions[].font) — font tema menimpa yang sekunci, sisanya bertahan.
 */
export function collectFonts(themeOptions) {
    const fonts = new Map(FONT_OPTIONS.map(f => [f.key, f]));
    Object.entries(themeOptions || {}).forEach(([, meta]) => {
        const f = meta.font;
        if (!f?.name) return;
        const key = f.name.toLowerCase();
        fonts.set(key, { key, name: f.name, label: f.name, heading: f.heading, body: f.body });
    });
    return [...fonts.values()];
}

/**
 * Preset desain tema (config/themes.php) — variant section + warna section.
 * Key section memakai canonical (sectionKey): hero, product, statistic, dst.
 * Dipakai ThemeCustomizerContext saat tema berganti agar ganti tema benar-benar
 * mengubah tampilan (bukan hanya palet).
 */
export function presetSections(meta) {
    return {
        sectionVariants: meta?.preset?.sectionVariants || {},
        sectionColors: meta?.preset?.sectionColors || {},
    };
}

/**
 * Default state custom untuk tema tertentu (atau null bila tidak ada).
 * Nilai dari preset tema (config/themes.php) dipakai bila tidak ada override
 * user — jadi ganti tema otomatis menerapkan font/radius/nav/card/dark-nya.
 */
export function defaultsFor(custom, paletteOptions, fontOptions, meta) {
    const preset = meta?.preset || {};
    const paletteKey = custom?.paletteKey
        ?? paletteOptions.find(p => p.primary === meta?.palette?.primary)?.key
        ?? paletteOptions[0]?.key
        ?? null;
    const font = custom?.font
        ?? fontOptions.find(f => f.key === preset.font)?.key
        ?? fontOptions.find(f => f.name === meta?.font?.name)?.key
        ?? fontOptions[0]?.key
        ?? 'modern';
    return {
        paletteKey,
        font,
        radius: custom?.radius ?? preset.radius ?? 'default',
        density: custom?.density ?? preset.density ?? 'standard',
        elevation: custom?.elevation ?? preset.elevation ?? 'soft',
        dark: custom?.dark ?? preset.dark ?? false,
        heroFill: custom?.heroFill ?? preset.heroFill ?? true,
    };
}
