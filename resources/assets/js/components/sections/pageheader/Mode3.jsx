import React from 'react';
import { Link } from '@inertiajs/react';
import { ArrowRight, ChevronRight } from 'lucide-react';

/**
 * Page Header Mode 3 — dramatis: breadcrumb + badge kategori + judul di atas
 * latar gradien primary→gelap dengan aksen ornamen. Cocok untuk detail berita
 * dan halaman statis utama. Prop: { context, site }
 */
export default function PageHeaderMode3({ context, site }) {
    const { title, excerpt, eyebrow, crumb } = context || {};
    if (!title) return null;

    return (
        <section className="pageheader pageheader--bold">
            <div className="shell">
                <nav className="pageheader-crumb pageheader-crumb--light" aria-label="Breadcrumb">
                    <Link href={site?.homeUrl || '/'}>Beranda</Link>
                    <ChevronRight size={13} />
                    <span>{crumb || title}</span>
                </nav>
                {eyebrow && <span className="pageheader-badge">{eyebrow}</span>}
                <h1 className="pageheader-title">{title}</h1>
                {excerpt && <p className="pageheader-excerpt">{excerpt}</p>}
            </div>
            <ArrowRight className="pageheader-ornament" aria-hidden="true" />
        </section>
    );
}
