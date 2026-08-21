import React from 'react';
import { Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';

/**
 * Page Header Mode 1 — klasik: breadcrumb + judul rata kiri di latar tint.
 * Dipakai layout-level (PublicLayout) di semua halaman dalam; konten
 * (judul/ekserp) berasal dari halaman yang sedang dibuka, bukan dari CMS.
 * 
 * Color props: context.pretitleColor, context.titleColor, context.subtitleColor
 * → applied sebagai inline style, bisa diatur per-halaman dari CMS.
 * 
 * Prop: { context, site }
 */
export default function PageHeaderMode1({ context, site }) {
    const { title, excerpt, crumb, pretitleColor, titleColor, subtitleColor } = context || {};
    if (!title) return null;

    return (
        <section className="pageheader pageheader--classic section--tint">
            <div className="shell">
                <nav className="pageheader-crumb" aria-label="Breadcrumb">
                    <Link href={site?.homeUrl || '/'}>Beranda</Link>
                    <ChevronRight size={13} />
                    <span style={pretitleColor ? { color: pretitleColor } : undefined}>{crumb || title}</span>
                </nav>
                <h1 className="pageheader-title" style={titleColor ? { color: titleColor } : undefined}>{title}</h1>
                {excerpt && <p className="pageheader-excerpt" style={subtitleColor ? { color: subtitleColor } : undefined}>{excerpt}</p>}
            </div>
        </section>
    );
}
