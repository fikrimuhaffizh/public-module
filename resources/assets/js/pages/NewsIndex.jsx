import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import { PublicPageLayout } from '@public/layouts/PublicLayout';
import { NewsGrid } from '@public/components/sections/LandingSections';
import { Reveal } from '@public/components/motion/effects';

export default function NewsIndex() {
    const { site, template, announcements } = usePage().props;
    return <>
        <Head title={`Berita dan Pengumuman - ${site.name}`}>
            <meta head-key="description" name="description" content="Ikuti perkembangan, agenda, dan informasi terbaru dari institusi." />
            <meta head-key="og:title" property="og:title" content={`Berita dan Pengumuman - ${site.name}`} />
        </Head>
        <main className="inner-page">
            <div className="shell">
                <NewsGrid announcements={announcements} editorial={template === 'editorial'} />
            </div>
        </main>
    </>;
}

NewsIndex.layout = PublicPageLayout;
