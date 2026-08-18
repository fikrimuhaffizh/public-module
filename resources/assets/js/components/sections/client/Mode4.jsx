import React from 'react';
import { Reveal } from '@public/components/motion/effects';
import { sectionHeading } from '../index';

/** Klien/Logo Mode 4 — awan logo: logo melayang naik-turun lembut dengan jeda acak, pause saat hover. Prop: { section, data } */
export default function ClientMode4({ section, data }) {
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
                <div className="client-logos--float">
                    {items.map((client, index) => {
                        const body = client.logo
                            ? <img src={client.logo} alt={client.name} loading="lazy" />
                            : <span className="client-logos-initial">{client.name.charAt(0)}</span>;
                        const style = {
                            animationDelay: `${(index % 5) * 0.6}s`,
                            animationDuration: `${3.6 + (index % 4) * 0.8}s`,
                        };

                        return client.website
                            ? <a key={client.id} href={client.website} target="_blank" rel="noreferrer" title={client.name} className="client-logos-float" data-pause-offscreen style={style}>{body}<span className="client-logos-name">{client.name}</span></a>
                            : <div key={client.id} title={client.name} className="client-logos-float" data-pause-offscreen style={style}>{body}<span className="client-logos-name">{client.name}</span></div>;
                    })}
                </div>
            </div>
        </section>
    );
}
