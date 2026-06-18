import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, FileText } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { Reveal } from '@public/components/motion/effects';
import { PublicPageLayout } from '@public/layouts/PublicLayout';

export default function ContentPage() {
    const { site, page } = usePage().props;

    return (
        <>
            <Head title={`${page.title} - ${site.name}`}>
                <meta head-key="description" name="description" content={page.excerpt || ''} />
                <meta head-key="og:title" property="og:title" content={`${page.title} - ${site.name}`} />
                {page.excerpt && <meta head-key="og:description" property="og:description" content={page.excerpt} />}
            </Head>
            <main className="dynamic-page">
                <div className="dynamic-page-hero">
                    <Reveal className="shell shell--article">
                        <div className="dynamic-page-breadcrumb">
                            <Link href={site.homeUrl}>Beranda</Link>
                            <ArrowRight size={14} />
                            <span>{page.title}</span>
                        </div>
                        <span className="dynamic-page-icon"><FileText /></span>
                        <span className="eyebrow">Informasi institusi</span>
                        <h1>{page.title}</h1>
                        {page.excerpt && <p>{page.excerpt}</p>}
                    </Reveal>
                </div>

                <article className="shell shell--article dynamic-page-content">
                    <div
                        className="prose-content"
                        dangerouslySetInnerHTML={{ __html: page.content }}
                    />
                    <div className="dynamic-page-footer">
                        <Button variant="outline" asChild>
                            <Link href={site.homeUrl}><ArrowLeft size={17} /> Kembali ke beranda</Link>
                        </Button>
                        <Button asChild>
                            <Link href={site.contactUrl}>Hubungi kami <ArrowRight size={17} /></Link>
                        </Button>
                    </div>
                </article>
            </main>
        </>
    );
}

ContentPage.layout = PublicPageLayout;
