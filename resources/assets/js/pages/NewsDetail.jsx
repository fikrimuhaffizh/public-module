import React from 'react';
import { usePage } from '@inertiajs/react';
import { SiteLayout } from '@public/layouts/PublicLayout';
import { NewsGrid } from '@public/components/sections/LandingSections';

export default function NewsDetail() {
    const { site, menus, template, sections, announcement, related } = usePage().props;
    return <SiteLayout title={announcement.title} site={site} menus={menus} template={template} sections={sections}><main className="inner-page"><article className="shell shell--article"><div className="article-meta"><span>{announcement.type}</span><span>{announcement.date}</span></div><h1 className="article-title">{announcement.title}</h1><img className="article-cover" src={announcement.image} alt={announcement.title} /><div className="prose-content" dangerouslySetInnerHTML={{ __html: announcement.content }} /></article>{related.length > 0 && <section className="shell related-news"><h2>Informasi lainnya</h2><NewsGrid announcements={related} /></section>}</main></SiteLayout>;
}
