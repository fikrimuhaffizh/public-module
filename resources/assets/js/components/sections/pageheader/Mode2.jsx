import React from 'react';
import { Link } from '@inertiajs/react';
import { ChevronRight, Home } from 'lucide-react';

/**
 * Page Header Mode 2 — terpusat: breadcrumb + eyebrow + judul di tengah,
 * latar bersih dengan garis bawah halus. Cocok untuk halaman berita & kontak.
 * Fitur: home icon di breadcrumb, eyebrow default jika tidak ada.
 * Prop: { context, site }
 */
export default function PageHeaderMode2({ context, site }) {
    const { title, excerpt, eyebrow, crumb, pretitleColor, titleColor, subtitleColor } = context || {};
    if (!title) return null;

    // Eyebrow fallback: gunakan site name jika tidak ada eyebrow spesifik
    const displayEyebrow = eyebrow || site?.name || '';

    return (
        <section className="pageheader pageheader--center">
            <div className="shell">
                <nav className="pageheader-crumb pageheader-crumb--center" aria-label="Breadcrumb">
                    <Link href={site?.homeUrl || '/'}>
                        <Home size={13} style={{ verticalAlign: '-2px', marginRight: 2 }} />
                        Beranda
                    </Link>
                    <ChevronRight size={13} />
                    <span style={pretitleColor ? { color: pretitleColor } : undefined}>{crumb || title}</span>
                </nav>
                {displayEyebrow && <span className="eyebrow" style={pretitleColor ? { color: pretitleColor } : undefined}>{displayEyebrow}</span>}
                <h1 className="pageheader-title" style={titleColor ? { color: titleColor } : undefined}>{title}</h1>
                {excerpt && <p className="pageheader-excerpt" style={subtitleColor ? { color: subtitleColor } : undefined}>{excerpt}</p>}
            </div>
        </section>
    );
}
