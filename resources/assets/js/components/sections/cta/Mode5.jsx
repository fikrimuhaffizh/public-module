import React from 'react';
import { Reveal } from '@public/components/motion/effects';

/**
 * CTA Mode 5 — Banner gradient: background gradient + teks besar center.
 * Animasi: zoom-in dari kecil (Reveal scale, budgeted).
 */
export default function CtaMode5({ section, data }) {
    const cta = data.landing?.cta;

    return (
        <section className="cta cta--banner">
            <div className="shell">
                <Reveal className="cta-banner-inner" scale={0.95} budgeted>
                    {section.pre_title && <span className="eyebrow" style={{ color: 'var(--sec-pretext, rgba(255,255,255,.8))' }}>{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, #fff)' }}>{section.title || 'Mulai Sekarang'}</h2>
                    <p style={{ color: 'var(--sec-posttext, rgba(255,255,255,.85))' }}>
                        {section.subtitle || section.post_title || 'Jangan lewatkan kesempatan ini.'}
                    </p>
                    {cta?.link && (
                        <a className="ui-btn ui-btn--glass" href={cta.link}>{cta.text || 'Daftar Gratis'}</a>
                    )}
                </Reveal>
            </div>
        </section>
    );
}
