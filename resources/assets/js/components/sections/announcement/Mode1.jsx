import React from 'react';
import { Section, NewsGrid, combinedText } from '../index';

/** Pengumuman Mode 1 — grid kartu berita. Prop: { section, data } */
export default function AnnouncementMode1({ section, data }) {
    return (
        <Section
            section={section}
            id="berita"
            tint
            eyebrow={section.pre_title || 'Tetap terhubung'}
            title={section.title || 'Kabar terbaru kampus'}
            text={combinedText(section)}
        >
            <NewsGrid announcements={data.announcements} section={section} />
        </Section>
    );
}
