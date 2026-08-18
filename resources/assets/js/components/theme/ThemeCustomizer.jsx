/**
 * Barrel Theme Customizer — semua export lama tetap tersedia lewat satu
 * import `@public/components/theme/ThemeCustomizer` agar consumer
 * (PublicLayout, renderer, template) tidak berubah.
 *
 *   ThemeCustomizerContext.jsx → context + provider + state (loadStored)
 *   ThemeSettingsDrawer.jsx    → UI tombol + offcanvas
 *   presets.js                 → konstanta & helper data murni
 */
export { ThemeCustomizerProvider, useThemeCustomizer, loadStored, storedForTemplate } from './ThemeCustomizerContext';
export { ThemeSettingsDrawer } from './ThemeSettingsDrawer';
export {
    SECTION_COLOR_PRESETS,
    RADIUS_OPTIONS,
    DARK_VARS,
    FALLBACK_PALETTE,
    FONT_OPTIONS,
    paletteToVars,
    collectPalettes,
    collectFonts,
    defaultsFor,
} from './presets';
