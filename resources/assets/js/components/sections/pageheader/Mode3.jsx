import React from 'react';
import { Link } from '@inertiajs/react';
import { BookOpen, ChevronRight } from 'lucide-react';

/**
 * Page Header Mode 3 — dramatis: breadcrumb + badge kategori + judul di atas
 * latar gradien primary→gelap dengan aksen ornamen. Cocok untuk detail berita
 * dan halaman statis utama. Prop: { context, site }
 */
export default function PageHeaderMode3({ context, site }) {
    const { title, excerpt, eyebrow, crumb, pretitleColor, titleColor, subtitleColor } = context || {};
    if (!title) return null;

    return (
        <section className="pageheader pageheader--bold">
            <div className="shell">
                <nav className="pageheader-crumb pageheader-crumb--light" aria-label="Breadcrumb">
                    <Link href={site?.homeUrl || '/'}>Beranda</Link>
                    <ChevronRight size={13} />
                    <span style={pretitleColor ? { color: pretitleColor } : undefined}>{crumb || title}</span>
                </nav>
                {eyebrow && <span className="pageheader-badge" style={pretitleColor ? { color: pretitleColor, borderColor: pretitleColor + '44' } : undefined}>{eyebrow}</span>}
                <h1 className="pageheader-title" style={titleColor ? { color: titleColor } : undefined}>{title}</h1>
                {excerpt && <p className="pageheader-excerpt" style={subtitleColor ? { color: subtitleColor } : undefined}>{excerpt}</p>}
            </div>
            <BookOpen className="pageheader-ornament" aria-hidden="true" />
        </section>
    );
}
