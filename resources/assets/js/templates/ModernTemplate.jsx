import { SectionVariantRenderer } from '@public/components/sections/renderer';
import React from 'react';

/**
 * Modern Template — template ringan.
 *
 * Semua section dirender lewat SectionVariantRenderer (registry.js):
 * tiap section memakai komponen variant-nya sendiri, dan variant bisa
 * diganti live dari Theme Settings (offcanvas /preview).
 * Menambah variant section = tambah file komponen + 1 entri di registry.
 */
export default function ModernTemplate({ data }) {
    const sections = data.sections || [];

        const renderSection = (section) => {
        if (!section.is_active) return null;
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
