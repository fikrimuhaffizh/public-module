import React from 'react';
import { Reveal } from '@public/components/motion/effects';
import { sectionHeading } from '../index';

/** Klien/Logo Mode 1 — grid kartu teratur (logo + nama). Prop: { section, data } */
export default function ClientMode1({ section, data }) {
    const clients = data.landing?.clients || [];
    if (!clients.length) return null;
    const limit = section?.limit_data;
    const items = limit ? clients.slice(0, limit) : clients;
    const heading = sectionHeading(section, {
        eyebrow: 'Dipercaya oleh', title: 'Institusi Mitra',
        text: 'Ribuan institusi telah mempercayakan manajemennya kepada kami.',
    });

    return (
        <section className="client-logos">
            <div className="shell">
                <Reveal className={`section-heading section-heading--${heading.align}`}>
                    {heading.eyebrow && <span className="eyebrow" style={{ color: 'var(--sec-pretext, inherit)' }}>{heading.eyebrow}</span>}
                    {heading.title && <h2 style={{ color: 'var(--sec-title, inherit)' }}>{heading.title}</h2>}
                    {heading.text && <p style={{ color: 'var(--sec-posttext, inherit)' }}>{heading.text}</p>}
                </Reveal>
                <div className="client-logos--grid">
                    {items.map(client => {
                        const body = client.logo
                            ? <img src={client.logo} alt={client.name} loading="lazy" />
                            : <span className="client-logos-initial">{client.name.charAt(0)}</span>;
                        return client.website
                            ? <a key={client.id} href={client.website} target="_blank" rel="noreferrer" title={client.name}>{body}<span className="client-logos-name">{client.name}</span></a>
                            : <div key={client.id} title={client.name}>{body}<span className="client-logos-name">{client.name}</span></div>;
                    })}
                </div>
            </div>
        </section>
    );
}
