import React from 'react';
import { Home } from 'lucide-react';

/**
 * Pageheader Mode 5 — Image bg: gambar background + overlay.
 */
export default function PageheaderMode5({ context, site }) {
    const { breadcrumb = [], title = '', subtitle = '', image = '' } = context || {};

    return (
        <section className="pageheader pageheader--image" style={image ? { backgroundImage: `url(${image})` } : undefined}>
            <div className="pageheader-image-scrim" />
            <div className="shell" style={{ position: 'relative', zIndex: 2, textAlign: 'center' }}>
                <nav className="pageheader-breadcrumb pageheader-breadcrumb--light">
                    <Home size={14} />
                    {breadcrumb.map((b, i) => (
                        <React.Fragment key={i}>
                            <span className="pageheader-breadcrumb-sep">/</span>
                            {b.url ? <a href={b.url}>{b.label}</a> : <span>{b.label}</span>}
                        </React.Fragment>
                    ))}
                </nav>
                <h1 style={{ color: '#fff' }}>{title}</h1>
                {subtitle && <p className="pageheader-excerpt" style={{ color: 'rgba(255,255,255,.85)' }}>{subtitle}</p>}
            </div>
        </section>
    );
}
