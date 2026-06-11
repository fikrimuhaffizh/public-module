import React from 'react';
import { usePage } from '@inertiajs/react';
import { SiteLayout } from '@public/layouts/PublicLayout';
import { Reveal } from '@public/components/motion/effects';

export default function ContentPage() {
    const { site, menus, template, page } = usePage().props;
    return <SiteLayout title={page.title} site={site} menus={menus} template={template}><main className="inner-page"><article className="shell shell--article"><Reveal className="inner-hero"><span className="eyebrow">Informasi kampus</span><h1>{page.title}</h1><p>{page.excerpt}</p></Reveal><div className="prose-content" dangerouslySetInnerHTML={{ __html: page.content }} /></article></main></SiteLayout>;
}
