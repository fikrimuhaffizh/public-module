import React from 'react';
import { Section, PagesGrid, combinedText } from '../index';

/** Produk Mode 1 — card grid (dari halaman/PagesGrid). Prop: { section, data } */
export default function ProductMode1({ section, data }) {
    return (
        <Section
            section={section}
            id="informasi"
            eyebrow={section.pre_title || 'Satu ekosistem'}
            title={section.title || 'Semua yang dibutuhkan sivitas akademika'}
            text={combinedText(section, 'Pengalaman digital yang sederhana di depan, dengan pengelolaan konten yang terstruktur di belakang.')}
        >
            <PagesGrid pages={data.pages} section={section} />
        </Section>
    );
}
