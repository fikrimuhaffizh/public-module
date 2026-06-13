import React from 'react';
import { usePage } from '@inertiajs/react';
import { SiteLayout } from '@public/layouts/PublicLayout';
import { NewsGrid } from '@public/components/sections/LandingSections';
import { Reveal } from '@public/components/motion/effects';

export default function NewsIndex() {
    const { site, menus, template, sections, announcements } = usePage().props;
    return <SiteLayout title="Berita dan Pengumuman" site={site} menus={menus} template={template} sections={sections}>
                <main className="inner-page">
                    <div className="shell">
                        <Reveal className="inner-hero">
                            <span className="eyebrow">Kabar kampus</span>
                            <h1>Berita dan pengumuman</h1>
                            <p>Ikuti perkembangan, agenda, dan informasi terbaru dari institusi.</p>
                        </Reveal>
                        <NewsGrid announcements={announcements} editorial={template === 'editorial'} />
                    </div>
                </main>
        </SiteLayout>;
}
