import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import { PublicPageLayout } from '@public/layouts/PublicLayout';
import { NewsGrid } from '@public/components/sections/LandingSections';

export default function NewsDetail() {
    const { site, announcement, related } = usePage().props;
    return <>
        <Head title={`${announcement.title} - ${site.name}`}>
            <meta head-key="description" name="description" content={announcement.excerpt || ''} />
            <meta head-key="og:title" property="og:title" content={`${announcement.title} - ${site.name}`} />
            {announcement.image && <meta head-key="og:image" property="og:image" content={announcement.image} />}
        </Head>
        <main className="inner-page">
            <article className="shell shell--article">
                <div className="article-meta"><span>{announcement.type}</span><span>{announcement.date}</span></div>
                <h1 className="article-title">{announcement.title}</h1>
                <img className="article-cover" src={announcement.image} alt={announcement.title} />
                <div className="prose-content" dangerouslySetInnerHTML={{ __html: announcement.content }} />
            </article>
            {related.length > 0 && <section className="shell related-news"><h2>Informasi lainnya</h2><NewsGrid announcements={related} /></section>}
        </main>
    </>;
}

NewsDetail.layout = PublicPageLayout;
