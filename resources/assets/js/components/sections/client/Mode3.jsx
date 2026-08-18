import React from 'react';
import { Reveal } from '@public/components/motion/effects';
import { sectionHeading } from '../index';

/** Klien/Logo Mode 3 — marquee: strip logo berjalan tak terbatas. Prop: { section, data } */
export default function ClientMode3({ section, data }) {
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
                    {heading.eyebrow && <span className="eyebrow">{heading.eyebrow}</span>}
                    {heading.title && <h2>{heading.title}</h2>}
                    {heading.text && <p>{heading.text}</p>}
                </Reveal>
                <div className="client-logos--marquee">
                    <div className="client-logos-track" data-pause-offscreen>
                        {[...items, ...items].map((client, i) => (
                            <span key={`${client.id}-${i}`} className="client-logos-cell">
                                {client.logo
                                    ? <img src={client.logo} alt={client.name} />
                                    : <span className="client-logos-initial">{client.name.charAt(0)}</span>}
                            </span>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
