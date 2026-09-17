/**
 * Barrel Theme Customizer - semua export lama tetap tersedia lewat satu
 * import `@public/components/theme/ThemeCustomizer` agar consumer
 * (PublicLayout, renderer, template) tidak berubah.
 *
 *   ThemeCustomizerContext.jsx ke context + provider + state (loadStored)
 *   ThemeSettingsDrawer.jsx    ke UI tombol + offcanvas
 *   presets.js                 ke konstanta & helper data murni
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
