import React from 'react';
import { Home } from 'lucide-react';

/**
 * Pageheader Mode 7 — Split: breadcrumb kiri, title kanan.
 */
export default function PageheaderMode7({ context, site }) {
    const { breadcrumb = [], title = '', subtitle = '' } = context || {};

    return (
        <section className="pageheader pageheader--split">
            <div className="shell pageheader-split-grid">
                <div className="pageheader-split-breadcrumb">
                    <nav className="pageheader-breadcrumb">
                        <Home size={14} />
                        {breadcrumb.map((b, i) => (
                            <React.Fragment key={i}>
                                <span className="pageheader-breadcrumb-sep">/</span>
                                {b.url ? <a href={b.url}>{b.label}</a> : <span>{b.label}</span>}
                            </React.Fragment>
                        ))}
                    </nav>
                </div>
                <div className="pageheader-split-title">
                    <h1>{title}</h1>
                    {subtitle && <p className="pageheader-excerpt">{subtitle}</p>}
                </div>
            </div>
        </section>
    );
}
