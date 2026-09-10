import test from 'node:test';
import assert from 'node:assert/strict';
import { DESIGN_FAMILIES, generateDesign, contrastRatio, validatedPalette, paletteForMode } from '../../resources/assets/js/components/theme/design-system.js';

const keys = ['hero', 'feature', 'product', 'statistic', 'client', 'testimonial', 'faq', 'pengumuman', 'cta', 'price', 'navbar', 'footer'];
const sectionMeta = keys.map(key => ({ key, variants: Array.from({ length: 8 }, (_, i) => ({ key: `${key}_${i + 1}` })) }));
function seeded(seed) {
    return () => ((seed = Math.imul(1664525, seed) + 1013904223 >>> 0) / 4294967296);
}

test('1000 random designs produce varied palettes with readable section text', () => {
    const random = seeded(20260910);
    const palettes = new Set();
    for (let i = 0; i < 1000; i++) {
        const design = generateDesign({ sectionMeta, random });
        assert.ok(validatedPalette(design.customPalette));
        palettes.add(JSON.stringify(design.customPalette));
        assert.deepEqual(Object.keys(design.sectionVariants), keys);
        for (const color of Object.values(design.sectionColors)) {
            for (const field of ['text', 'text_color', 'posttext_color', 'pretext_color']) {
                assert.ok(contrastRatio(color.bg, color[field]) >= 4.5, `${field}: ${color.bg}/${color[field]}`);
            }
        }
    }
    assert.ok(palettes.size > 900);
});

test('every design family respects a sparse registry and available fonts', () => {
    for (const family of DESIGN_FAMILIES) {
        const design = generateDesign({ familyKey: family.key, random: seeded(42), fontOptions: [{ key: 'inter' }],
            sectionMeta: [{ key: 'hero', variants: ['hero_2', 'hero_8'] }] });
        assert.ok(['hero_2', 'hero_8'].includes(design.sectionVariants.hero));
        assert.equal(design.designFamily, family.key);
        assert.equal(design.font, 'inter');
    }
});

test('light/dark conversion retains the brand and readable surfaces', () => {
    const original = generateDesign({ random: seeded(7) }).customPalette;
    for (const dark of [true, false]) {
        const palette = paletteForMode(original, dark);
        assert.equal(palette.primary, original.primary);
        for (const surface of ['background', 'card', 'tint']) {
            assert.ok(contrastRatio(palette[surface], palette.foreground) >= 4.5);
            assert.ok(contrastRatio(palette[surface], palette.muted) >= 4.5);
        }
    }
});

test('palette validation rejects partial and CSS-injection values', () => {
    assert.equal(validatedPalette({ primary: '#ffffff' }), null);
    const palette = generateDesign({ random: seeded(4) }).customPalette;
    assert.equal(validatedPalette({ ...palette, card: 'url(https://example.test)' }), null);
    assert.deepEqual(validatedPalette(JSON.parse(JSON.stringify(palette))), palette);
});
