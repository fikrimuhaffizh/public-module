import React from 'react';
import { SectionVariantRenderer } from '@public/components/sections/renderer';

/**
 * Tema UMKM GENERIK (data-driven) — dipakai oleh semua tema hasil CLI
 * `php artisan public:generate-themes`. Template ini adalah shell tipis:
 * seluruh section (hero, produk, statistik, dsb.) dirender oleh
 * SectionVariantRenderer, jadi variasi layout & warna datang dari registry
 * section (Theme Settings /preview) — bukan dari komponen per-tema.
 *
 * Warna & font datang dari blok CSS `.theme-<key>` (css/themes/<key>.css) lewat
 * CSS variables — dan bisa dikustomisasi live dari Theme Settings (/preview).
 */
export default function UmkmGenericTemplate({ data }) {
    return (
        <>
            {(data.sections || []).map(section =>
                section.is_active
                    ? <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />
                    : null
            )}
        </>
    );
}
