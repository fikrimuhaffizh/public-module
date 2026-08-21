import React, { useEffect, useRef, useState } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { Reveal } from '@public/components/motion/effects';
import { sectionHeading } from '../index';

/** Klien/Logo Mode 2 — showcase: panggung logo aktif (besar) + panah + strip thumbnail, auto-putar, swipe untuk mobile. Prop: { section, data } */
export default function ClientMode2({ section, data }) {
    const clients = data.landing?.clients || [];
    if (!clients.length) return null;

    const limit = section?.limit_data;
    const items = limit ? clients.slice(0, limit) : clients;
    const [active, setActive] = useState(0);
    const [dragX, setDragX] = useState(null);
    const timer = useRef(null);
    const touchStart = useRef(null);
    const dragXRef = useRef(null);

    const heading = sectionHeading(section, {
        eyebrow: 'Dipercaya oleh', title: 'Institusi Mitra',
        text: 'Ribuan institusi telah mempercayakan manajemennya kepada kami.',
    });

    useEffect(() => {
        if (items.length < 2) return undefined;
        timer.current = window.setInterval(() => setActive(a => (a + 1) % items.length), 4000);

        return () => window.clearInterval(timer.current);
    }, [items.length]);

    const pause = () => window.clearInterval(timer.current);
    const resume = () => {
        if (items.length < 2) return;
        window.clearInterval(timer.current);
        timer.current = window.setInterval(() => setActive(a => (a + 1) % items.length), 4000);
    };
    const go = i => setActive((i + items.length) % items.length);

    const onTouchStart = e => {
        const t = e.touches[0];
        touchStart.current = { x: t.clientX, y: t.clientY };
        pause();
    };
    const onTouchMove = e => {
        if (!touchStart.current) return;
        const t = e.touches[0];
        const dx = t.clientX - touchStart.current.x;
        const dy = t.clientY - touchStart.current.y;
        // Geser dominan horizontal — ikuti jari (dibatasi ±120px), biarkan scroll vertikal jalan.
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 8) {
            dragXRef.current = Math.max(-120, Math.min(120, dx));
            setDragX(dragXRef.current);
        }
    };
    const onTouchEnd = () => {
        const dx = dragXRef.current;
        if (dx !== null) {
            if (Math.abs(dx) > 48) go(safe + (dx < 0 ? 1 : -1));
            dragXRef.current = null;
            setDragX(null);
        }
        touchStart.current = null;
        resume();
    };

    const safe = active % items.length;
    const current = items[safe];

    return (
        <section className="client-logos">
            <div className="shell">
                <Reveal className={`section-heading section-heading--${heading.align}`}>
                    {heading.eyebrow && <span className="eyebrow" style={{ color: 'var(--sec-pretext, inherit)' }}>{heading.eyebrow}</span>}
                    {heading.title && <h2 style={{ color: 'var(--sec-title, inherit)' }}>{heading.title}</h2>}
                    {heading.text && <p style={{ color: 'var(--sec-posttext, inherit)' }}>{heading.text}</p>}
                </Reveal>
                <div className="client-logos--showcase" onMouseEnter={pause} onMouseLeave={resume}>
                    <button type="button" className="client-logos-nav" onClick={() => go(safe - 1)} aria-label="Klien sebelumnya">
                        <ChevronLeft size={22} />
                    </button>
                    <div
                        className={`client-logos-stage${dragX !== null ? ' is-dragging' : ''}`}
                        style={dragX !== null ? { transform: `translateX(${dragX}px)` } : undefined}
                        onTouchStart={onTouchStart}
                        onTouchMove={onTouchMove}
                        onTouchEnd={onTouchEnd}
                        onTouchCancel={onTouchEnd}
                    >
                        <div key={safe} className="client-logos-stage__inner">
                            {current.logo
                                ? <img src={current.logo} alt={current.name} loading="lazy" />
                                : <span className="client-logos-initial">{current.name.charAt(0)}</span>}
                            <strong>{current.name}</strong>
                            {current.website && (
                                <a href={current.website} target="_blank" rel="noreferrer">Kunjungi situs <span aria-hidden="true">↗</span></a>
                            )}
                        </div>
                    </div>
                    <button type="button" className="client-logos-nav" onClick={() => go(safe + 1)} aria-label="Klien berikutnya">
                        <ChevronRight size={22} />
                    </button>
                    <div className="client-logos-strip" role="tablist" aria-label="Pilih klien">
                        {items.map((client, i) => (
                            <button
                                type="button"
                                role="tab"
                                aria-selected={i === safe}
                                key={client.id}
                                className={`client-logos-thumb${i === safe ? ' is-active' : ''}`}
                                onClick={() => go(i)}
                                title={client.name}
                            >
                                {client.logo
                                    ? <img src={client.logo} alt={client.name} loading="lazy" />
                                    : <span className="client-logos-initial">{client.name.charAt(0)}</span>}
                            </button>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
