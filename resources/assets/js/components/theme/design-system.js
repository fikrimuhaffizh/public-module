/** Shared design recipes. Modes remain independently editable after generation. */
export const DESIGN_FAMILIES = [
    { key: 'studio', name: 'Studio', font: ['modern', 'jakarta'], radius: 'rounded', dark: false, modes: { pageheader: 1, navbar: 2, topbar: 1, hero: 2, product: 1, statistic: 1, feature: 1, testimonial: 1, client: 1, faq: 1, pengumuman: 1, cta: 1, price: 1, footer: 1 } },
    { key: 'editorial', name: 'Editorial', font: ['serif', 'condensed'], radius: 'square', dark: false, modes: { pageheader: 7, navbar: 7, topbar: 4, hero: 6, product: 8, statistic: 8, feature: 7, testimonial: 8, client: 8, faq: 1, pengumuman: 8, cta: 7, price: 6, footer: 6 } },
    { key: 'institutional', name: 'Institusi', font: ['inter', 'jakarta'], radius: 'default', dark: false, modes: { pageheader: 1, navbar: 2, topbar: 1, hero: 1, product: 2, statistic: 3, feature: 3, testimonial: 3, client: 1, faq: 2, pengumuman: 2, cta: 3, price: 4, footer: 3 } },
    { key: 'gallery', name: 'Galeri', font: ['figtree', 'outfit'], radius: 'rounded', dark: false, modes: { pageheader: 5, navbar: 5, topbar: 3, hero: 3, product: 7, statistic: 1, feature: 6, testimonial: 2, client: 6, faq: 6, pengumuman: 5, cta: 4, price: 1, footer: 5 } },
    { key: 'atelier', name: 'Atelier', font: ['serif', 'modern'], radius: 'default', dark: false, modes: { pageheader: 2, navbar: 3, topbar: 4, hero: 5, product: 5, statistic: 6, feature: 6, testimonial: 6, client: 8, faq: 5, pengumuman: 3, cta: 3, price: 3, footer: 6 } },
    { key: 'minimal', name: 'Minimal', font: ['inter', 'figtree'], radius: 'square', dark: false, modes: { pageheader: 4, navbar: 7, topbar: 4, hero: 6, product: 8, statistic: 8, feature: 7, testimonial: 8, client: 8, faq: 1, pengumuman: 8, cta: 7, price: 6, footer: 5 } },
    { key: 'bento', name: 'Bento', font: ['sora', 'jakarta'], radius: 'rounded', dark: false, modes: { pageheader: 7, navbar: 4, topbar: 6, hero: 7, product: 6, statistic: 4, feature: 5, testimonial: 6, client: 6, faq: 7, pengumuman: 5, cta: 6, price: 7, footer: 6 } },
    { key: 'contrast', name: 'Kontras', font: ['space', 'inter'], radius: 'default', dark: true, modes: { pageheader: 3, navbar: 8, topbar: 8, hero: 8, product: 3, statistic: 7, feature: 8, testimonial: 2, client: 8, faq: 6, pengumuman: 7, cta: 8, price: 8, footer: 8 } },
];
const SECTION_ORDER = ['pageheader', 'navbar', 'topbar', 'hero', 'product', 'statistic', 'feature', 'testimonial', 'client', 'faq', 'pengumuman', 'cta', 'price', 'footer'];
const COLOR_KEYS = ['primary', 'primaryDark', 'accent', 'background', 'card', 'foreground', 'muted', 'border', 'tint'];
const pick = (values, random) => values[Math.min(values.length - 1, Math.floor(random() * values.length))];

export function hexFromHsl(h, s, l) {
    s /= 100; l /= 100;
    const a = s * Math.min(l, 1 - l);
    const channel = n => {
        const k = (n + h / 30) % 12;
        return Math.round(255 * (l - a * Math.max(-1, Math.min(k - 3, 9 - k, 1)))).toString(16).padStart(2, '0');
    };
    return `#${channel(0)}${channel(8)}${channel(4)}`;
}
export function luminance(hex) {
    const expanded = hex.length === 4 ? '#' + [...hex.slice(1)].map(c => c + c).join('') : hex;
    const rgb = expanded.slice(1).match(/.{2}/g).map(c => parseInt(c, 16) / 255).map(c => c <= .04045 ? c / 12.92 : ((c + .055) / 1.055) ** 2.4);
    return rgb[0] * .2126 + rgb[1] * .7152 + rgb[2] * .0722;
}
export function contrastRatio(a, b) {
    const x = luminance(a), y = luminance(b);
    return (Math.max(x, y) + .05) / (Math.min(x, y) + .05);
}
export function readableText(background) {
    return contrastRatio(background, '#17212b') >= contrastRatio(background, '#ffffff') ? '#17212b' : '#ffffff';
}
export function colorHsl(hex) {
    const [r, g, b] = hex.slice(1).match(/.{2}/g).map(c => parseInt(c, 16) / 255);
    const max = Math.max(r, g, b), min = Math.min(r, g, b), delta = max - min;
    const l = (max + min) / 2;
    let h = 0, s = 0;
    if (delta) {
        s = delta / (1 - Math.abs(2 * l - 1));
        h = 60 * (max === r ? ((g - b) / delta + 6) % 6 : max === g ? (b - r) / delta + 2 : (r - g) / delta + 4);
    }
    return `${h.toFixed(1)} ${(s * 100).toFixed(1)}% ${(l * 100).toFixed(1)}%`;
}
export function validatedPalette(value) {
    if (!value || typeof value !== 'object' || !COLOR_KEYS.every(key => /^#[0-9a-f]{6}$/i.test(value[key]))) return null;
    return Object.fromEntries(COLOR_KEYS.map(key => [key, value[key]]));
}

/** Keep the generated brand hue when switching between light and dark surfaces. */
export function paletteForMode(palette, dark) {
    const hue = Number(colorHsl(palette.primary).split(' ')[0]);
    return {
        ...palette,
        accent: hexFromHsl((hue + 24) % 360, 48, dark ? 78 : 30),
        background: hexFromHsl(hue, dark ? 17 : 18, dark ? 9 : 99),
        card: hexFromHsl(hue, dark ? 15 : 10, dark ? 13 : 100),
        foreground: hexFromHsl(hue, dark ? 12 : 22, dark ? 95 : 12),
        muted: hexFromHsl(hue, 9, dark ? 73 : 38),
        border: hexFromHsl(hue, 12, dark ? 25 : 87),
        tint: hexFromHsl(hue, dark ? 16 : 22, dark ? 15 : 95),
    };
}

/** One hue anchors neutrals, surfaces and accents. Every output color is serializable hex. */
export function generateDesign({ sectionMeta = [], fontOptions = [], familyKey, random = Math.random } = {}) {
    const family = DESIGN_FAMILIES.find(item => item.key === familyKey) || pick(DESIGN_FAMILIES, random);
    const hue = Math.floor(random() * 360);
    const saturation = 38 + Math.floor(random() * 34);
    let primaryLightness = 44;
    while (contrastRatio(hexFromHsl(hue, saturation, primaryLightness), hexFromHsl(hue, 22, 95)) < 4.6 && primaryLightness > 12) primaryLightness--;
    const dark = family.dark;
    const palette = {
        primary: hexFromHsl(hue, saturation, primaryLightness),
        primaryDark: hexFromHsl(hue, saturation, Math.max(10, primaryLightness - 9)),
        accent: hexFromHsl((hue + 24) % 360, saturation, dark ? 78 : primaryLightness - 4),
        background: hexFromHsl(hue, dark ? 17 : 18, dark ? 9 : 99),
        card: hexFromHsl(hue, dark ? 15 : 10, dark ? 13 : 100),
        foreground: hexFromHsl(hue, dark ? 12 : 22, dark ? 95 : 12),
        muted: hexFromHsl(hue, 9, dark ? 73 : 38),
        border: hexFromHsl(hue, 12, dark ? 25 : 87),
        tint: hexFromHsl(hue, dark ? 16 : 22, dark ? 15 : 95),
    };
    const sectionVariants = {}, sectionColors = {};
    const rhythm = Math.floor(random() * 3);
    sectionMeta.forEach(({ key, variants = [], numModes }) => {
        const available = variants.length ? variants.map(v => typeof v === 'string' ? v : v.key) : Array.from({ length: numModes || 8 }, (_, i) => `${key}_${i + 1}`);
        const index = SECTION_ORDER.indexOf(key);
        const preferred = `${key}_${family.modes[key] || 1}`;
        // Keep the page's main composition coherent; vary supporting sections within a compatible set.
        const alternates = available.filter(value => [1, 2, 3, 6, 8].includes(Number(value.split('_').pop())));
        sectionVariants[key] = available.includes(preferred) && (['hero', 'navbar', 'footer'].includes(key) || random() < .72)
            ? preferred : pick(alternates.length ? alternates : available, random);
        const emphasized = key === 'cta' || (family.key === 'contrast' && key === 'hero');
        const tinted = ['hero', 'statistic', 'client'].includes(key) || (index + rhythm) % 3 === 0;
        const bg = emphasized ? palette.primaryDark : tinted ? palette.tint : palette.background;
        const text = emphasized ? '#ffffff' : palette.foreground;
        const accent = emphasized ? '#ffffff' : dark ? palette.accent : palette.primary;
        sectionColors[key] = { bg, text, heading: text, accent, pretext_color: accent, text_color: text, posttext_color: emphasized ? '#ffffff' : palette.muted };
    });
    const fonts = family.font.filter(key => !fontOptions.length || fontOptions.some(font => font.key === key));
    return {
        paletteKey: null, customPalette: palette, designFamily: family.key,
        font: pick(fonts.length ? fonts : fontOptions.length ? fontOptions.map(font => font.key) : ['modern'], random), radius: family.radius,
        density: pick(['standard', 'spacious'], random), elevation: pick(['flat', 'soft'], random),
        dark, heroFill: false, sectionVariants, sectionColors,
    };
}
