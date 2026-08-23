import React from 'react';
import { Home } from 'lucide-react';

/**
 * Pageheader Mode 8 — Large: title sangat besar, minimal.
 */
export default function PageheaderMode8({ context, site }) {
    const { breadcrumb = [], title = '', subtitle = '' } = context || {};

    return (
        <section className="pageheader pageheader--large">
            <div className="shell">
                <nav className="pageheader-breadcrumb">
                    <Home size={14} />
                    {breadcrumb.map((b, i) => (
                        <React.Fragment key={i}>
                            <span className="pageheader-breadcrumb-sep">/</span>
                            {b.url ? <a href={b.url}>{b.label}</a> : <span>{b.label}</span>}
                        </React.Fragment>
                    ))}
                </nav>
                <h1 className="pageheader-large-title">{title}</h1>
                {subtitle && <p className="pageheader-excerpt">{subtitle}</p>}
            </div>
        </section>
    );
}
