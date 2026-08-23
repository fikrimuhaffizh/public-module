import React from 'react';
import { Home } from 'lucide-react';

/**
 * Pageheader Mode 4 — Minimal center: title center, clean.
 */
export default function PageheaderMode4({ context, site }) {
    const { breadcrumb = [], title = '', subtitle = '', pretitle = '' } = context || {};

    return (
        <section className="pageheader pageheader--minimal-center">
            <div className="shell" style={{ textAlign: 'center' }}>
                <nav className="pageheader-breadcrumb">
                    <Home size={14} />
                    {breadcrumb.map((b, i) => (
                        <React.Fragment key={i}>
                            <span className="pageheader-breadcrumb-sep">/</span>
                            {b.url ? <a href={b.url}>{b.label}</a> : <span>{b.label}</span>}
                        </React.Fragment>
                    ))}
                </nav>
                {pretitle && <span className="eyebrow">{pretitle}</span>}
                <h1>{title}</h1>
                {subtitle && <p className="pageheader-excerpt">{subtitle}</p>}
            </div>
        </section>
    );
}
