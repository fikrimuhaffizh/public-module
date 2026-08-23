import React from 'react';

/**
 * Client Mode 7 — Marquee: logo berjalan otomatis.
 * Animasi: CSS infinite scroll.
 */
export default function ClientMode7({ section, data }) {
    const clients = data.landing?.clients || [];
    if (!clients.length) return null;
    const limit = section?.limit_data || 8;
    const items = clients.slice(0, limit);

    return (
        <section className="client-logos client-logos--marquee" id="klien">
            <div className="shell" style={{ marginBottom: 24 }}>
                {section.pre_title && <span className="eyebrow" style={{ display: 'block', textAlign: 'center' }}>{section.pre_title}</span>}
                <h2 className="section-heading" style={{ textAlign: 'center', color: 'var(--sec-title, inherit)' }}>
                    {section.title || 'Mitra Kami'}
                </h2>
            </div>
            <div className="client-marquee">
                <div className="client-marquee-track">
                    {[...items, ...items].map((c, i) => (
                        <div key={i} className="client-marquee-item">
                            {c.logo
                                ? <img src={c.logo} alt={c.name || ''} />
                                : <span className="client-marquee-initial">{(c.name || '').slice(0, 2)}</span>
                            }
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
