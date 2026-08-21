import React, { useMemo } from 'react';
import { GraduationCap } from 'lucide-react';
import { Reveal } from '@public/components/motion/effects';
import { sectionHeading } from '../index';

// Posisi item pada lingkaran radius r, indeks ke-i dari n.
const pos = (r, i, n) => {
    const a = (i / n) * Math.PI * 2;
    return { tx: Math.round(Math.cos(a) * r), ty: Math.round(Math.sin(a) * r) };
};

/**
 * Klien/Logo Mode 5 — orbiting circles: logo partner mengorbit pelan mengelilingi
 * logo utama tenant (2 ring, arah berlawanan). Prop: { section, data }
 */
export default function ClientMode5({ section, data }) {
    const clients = data.landing?.clients || [];
    if (!clients.length) return null;
    const limit = section?.limit_data;
    const items = limit ? clients.slice(0, limit) : clients;
    const site = data.site || {};
    const heading = sectionHeading(section, {
        eyebrow: 'Dipercaya oleh', title: 'Institusi Mitra',
        text: 'Ribuan institusi telah mempercayakan manajemennya kepada kami.',
    });

    // ≤4 item → satu ring besar; lebih → ring dalam 3 + ring luar sampai 6.
    const rings = useMemo(() => {
        const ringItems = (arr, r) =>
            arr.map((c, i) => ({ ...c, ...pos(r, i, arr.length) }));
        if (items.length <= 4) {
            return [{ dur: '45s', reverse: false, items: ringItems(items, 175) }];
        }
        return [
            { dur: '34s', reverse: false, items: ringItems(items.slice(0, 3), 115) },
            { dur: '52s', reverse: true, items: ringItems(items.slice(3, 9), 190) },
        ];
    }, [items]);

    return (
        <section className="client-logos">
            <div className="shell">
                <Reveal className={`section-heading section-heading--${heading.align}`}>
                    {heading.eyebrow && <span className="eyebrow" style={{ color: 'var(--sec-pretext, inherit)' }}>{heading.eyebrow}</span>}
                    {heading.title && <h2 style={{ color: 'var(--sec-title, inherit)' }}>{heading.title}</h2>}
                    {heading.text && <p style={{ color: 'var(--sec-posttext, inherit)' }}>{heading.text}</p>}
                </Reveal>

                <div className="orbit-stage" role="img" aria-label={`Logo ${site.name} dikelilingi logo mitra`}>
                    <div className="orbit-center">
                        {site.logo
                            ? <img src={site.logo} alt={site.name} />
                            : <span className="orbit-center-mark"><GraduationCap size={30} /></span>}
                        <span className="orbit-center-name">{site.name}</span>
                    </div>

                    {rings.map((ring, ri) => (
                        <div
                            key={ri}
                            className={`orbit-ring ${ring.reverse ? 'orbit-ring--outer' : 'orbit-ring--inner'}`}
                            style={{ '--dur': ring.dur }}
                        >
                            {ring.items.map(client => (
                                <span key={client.id} className="orbit-item" style={{ '--tx': `${client.tx}px`, '--ty': `${client.ty}px` }}>
                                    <span className="orbit-dot">
                                        {client.logo
                                            ? <img src={client.logo} alt={client.name} loading="lazy" />
                                            : <span className="client-logos-initial">{client.name.charAt(0)}</span>}
                                    </span>
                                </span>
                            ))}
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
