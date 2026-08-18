import React from 'react';
import { Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';

/**
 * Page Header Mode 2 — terpusat: breadcrumb + eyebrow + judul di tengah,
 * latar bersih dengan garis bawah halus. Cocok untuk halaman berita & kontak.
 * Prop: { context, site }
 */
export default function PageHeaderMode2({ context, site }) {
    const { title, excerpt, eyebrow, crumb } = context || {};
    if (!title) return null;

    return (
        <section className="pageheader pageheader--center">
            <div className="shell">
                <nav className="pageheader-crumb pageheader-crumb--center" aria-label="Breadcrumb">
                    <Link href={site?.homeUrl || '/'}>Beranda</Link>
                    <ChevronRight size={13} />
                    <span>{crumb || title}</span>
                </nav>
                {eyebrow && <span className="eyebrow">{eyebrow}</span>}
                <h1 className="pageheader-title">{title}</h1>
                {excerpt && <p className="pageheader-excerpt">{excerpt}</p>}
            </div>
        </section>
    );
}
