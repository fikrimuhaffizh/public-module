import React from 'react';
import { usePage } from '@inertiajs/react';
import { collectFonts, collectPalettes, DARK_VARS, defaultsFor, FALLBACK_PALETTE, FONT_OPTIONS, paletteToVars, presetSections } from './presets';

/**
 * Context Theme Customizer — state desain yang dibagikan ke PublicLayout
 * (CSS vars warna/font, kelas struktur) dan renderer section (variant +
 * warna per-section). UI drawer ada di ThemeSettingsDrawer.jsx; konstanta &
 * helper data murni ada di presets.js.
 *
 * TEMA = PRESET: setiap tema (config/themes.php) membawa preset desain —
 * variant section, palet, font, radius, nav, dark. Saat tema berganti,
 * seluruh state di-reset ke preset tema baru (kecuali ada simpanan untuk
 * tema itu), jadi ganti tema benar-benar mengubah tampilan.
 *
 * SUMBER BASIS desain:
 *   • Preview (/preview)  → localStorage PER-TEMA (draft). Tombol
 *     "Terapkan ke landing" mengirim state ini ke backend (PublicController
 *     saveDesign) — disimpan ke DB, landing asli ikut berubah.
 *   • Landing asli (/)    → design tersimpan di DB (props `design`, dari
 *     kolom JSON cms_landing_page_settings.design). Fallback ke preset tema.
 *
 * Persistensi localStorage hanya ditulis saat preview — landing asli tidak
 * pernah menimpa draft.
 */

const STORAGE_KEY = 'public.themeCustomizer.v1';

const ThemeCustomizerContext = React.createContext(null);

export function useThemeCustomizer() {
    return React.useContext(ThemeCustomizerContext);
}

/** Baca + parse localStorage dengan aman (korup/private mode → null). */
export function loadStored() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return null;
        const parsed = JSON.parse(raw);
        // Migrasi format lama (flat `{template, ...}`) → per-tema.
        if (parsed.template && !parsed.byTemplate) {
            return { byTemplate: { [parsed.template]: parsed } };
        }
        return parsed;
    } catch (e) {
        return null;
    }
}

/** Simpanan desain satu tema dari localStorage (atau null). */
export function storedForTemplate(stored, template) {
    return stored?.byTemplate?.[template] || null;
}

export function ThemeCustomizerProvider({ children }) {
    const { template, themeOptions = {}, preview, design = null } = usePage().props;
    const [isOpen, setOpen] = React.useState(false);

    const paletteOptions = React.useMemo(() => collectPalettes(themeOptions), [themeOptions]);
    const fontOptions = React.useMemo(() => collectFonts(themeOptions), [themeOptions]);

    // Basis desain: preview → localStorage draft per-tema (atau design DB bila
    // belum ada draft); landing asli → design DB (fallback preset tema).
    const [stored] = React.useState(() => loadStored());
    const dbDesign = design && design.template === template ? design : null;
    const basis = React.useMemo(
        () => (preview ? (storedForTemplate(stored, template) || dbDesign) : dbDesign),
        [preview, stored, template, dbDesign]
    );

    const [custom, setCustom] = React.useState(() =>
        defaultsFor(basis, paletteOptions, fontOptions, themeOptions[template]));

    const [sectionVariants, setSectionVariants] = React.useState(() =>
        basis?.sectionVariants || presetSections(themeOptions[template]).sectionVariants);

    const [sectionColors, setSectionColors] = React.useState(() =>
        basis?.sectionColors || presetSections(themeOptions[template]).sectionColors);

    // Pengaturan per-section (mis. navbar → showTopbar) — draft preview,
    // dikirim saat "Terapkan ke landing" lalu disimpan ke settings section DB.
    const [sectionSettings, setSectionSettings] = React.useState(() =>
        basis?.sectionSettings || {});

    // Template berganti (dropdown tema / URL ?template=) → muat basis untuk
    // tema itu (draft localStorage di preview, design DB di landing).
    const prevKey = React.useRef(`${preview}|${template}`);
    React.useEffect(() => {
        const key = `${preview}|${template}`;
        if (prevKey.current === key) return;
        prevKey.current = key;
        const nextBasis = preview
            ? (storedForTemplate(stored, template) || dbDesign)
            : dbDesign;
        setCustom(defaultsFor(nextBasis, paletteOptions, fontOptions, themeOptions[template]));
        setSectionVariants(nextBasis?.sectionVariants || presetSections(themeOptions[template]).sectionVariants);
        setSectionColors(nextBasis?.sectionColors || presetSections(themeOptions[template]).sectionColors);
        setSectionSettings(nextBasis?.sectionSettings || {});
    }, [preview, template]); // eslint-disable-line react-hooks/exhaustive-deps

    const setSectionVariant = (sectionKeyName, variantKey) =>
        setSectionVariants(prev => ({ ...prev, [sectionKeyName]: variantKey }));

    const resetSectionVariants = () => setSectionVariants({});

    const setSectionColor = (sectionKeyName, patch) =>
        setSectionColors(prev => ({ ...prev, [sectionKeyName]: { ...(prev[sectionKeyName] || {}), ...patch } }));

    const resetSectionColor = (sectionKeyName) =>
        setSectionColors(prev => {
            const next = { ...prev };
            delete next[sectionKeyName];
            return next;
        });

    const resetSectionColors = () => setSectionColors({});

    const setSectionSetting = (sectionKeyName, patch) =>
        setSectionSettings(prev => ({ ...prev, [sectionKeyName]: { ...(prev[sectionKeyName] || {}), ...patch } }));

    React.useEffect(() => {
        // Draft localStorage hanya untuk halaman preview — landing asli tidak
        // pernah menulis agar design DB tidak tertimpa draft.
        if (!preview) return;
        try {
            const current = loadStored() || { byTemplate: {} };
            const draft = { ...custom, sectionVariants, sectionColors, sectionSettings };
            current.byTemplate = { ...(current.byTemplate || {}), [template]: draft };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(current));
        } catch (e) { /* private mode */ }
    }, [preview, template, custom, sectionVariants, sectionColors, sectionSettings]);

    const update = (patch) => setCustom(prev => ({ ...prev, ...patch }));
    const reset = () => {
        const meta = themeOptions[template];
        setCustom(defaultsFor(null, paletteOptions, fontOptions, meta));
        setSectionVariants(presetSections(meta).sectionVariants);
        setSectionColors(presetSections(meta).sectionColors);
        setSectionSettings({});
    };

    const palette = paletteOptions.find(p => p.key === custom.paletteKey) || paletteOptions[0] || FALLBACK_PALETTE;
    const font = fontOptions.find(f => f.key === custom.font) || fontOptions[0] || FONT_OPTIONS[0];

    const appliedVars = React.useMemo(() => ({
        ...paletteToVars(palette),
        '--font-heading': font.heading,
        '--font-body': font.body,
    }), [palette, font]);

    const appliedClasses = [
        `theme-custom-radius--${custom.radius}`,
        `theme-custom-density--${custom.density}`,
        `theme-custom-elevation--${custom.elevation}`,
        custom.dark ? 'theme-custom-dark' : '',
        custom.heroFill ? 'theme-custom-hero-fill' : '',
    ].filter(Boolean).join(' ');

    const value = {
        preview,
        isOpen, setOpen,
        custom, update, reset,
        sectionVariants, setSectionVariant, resetSectionVariants,
        sectionColors, setSectionColor, resetSectionColor, resetSectionColors,
        sectionSettings, setSectionSetting,
        paletteOptions, fontOptions,
        palette, font,
        appliedVars, appliedClasses,
        darkVars: custom.dark ? DARK_VARS : null,
    };

    return <ThemeCustomizerContext.Provider value={value}>{children}</ThemeCustomizerContext.Provider>;
}
