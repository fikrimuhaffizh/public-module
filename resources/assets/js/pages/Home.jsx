import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import { PublicPageLayout } from '@public/layouts/PublicLayout';
import { resolveTemplate } from '@public/templates';

export default function Home() {
    const data = usePage().props;
    const Template = resolveTemplate(data.template);
    return <>
        <Head title={`${data.site.name}`}>
            <meta head-key="description" name="description" content={data.seo?.description || data.site.tagline || ''} />
            {data.seo?.title && <meta head-key="og:title" property="og:title" content={data.seo.title} />}
            {data.seo?.description && <meta head-key="og:description" property="og:description" content={data.seo.description} />}
        </Head>
        <Template data={data} />
    </>;
}

Home.layout = PublicPageLayout;
