import React from 'react';
import { Reveal } from '@public/components/motion/effects';
import { Section } from '../index';

/**
 * CTA Mode 4 - Split: teks kiri, visual kanan.
 * Animasi: slide-in from sides (Reveal x, budgeted).
 */
export default function CtaMode4({ section, data }) {
    const cta = data.landing?.cta;

    return (
        <section className="cta cta--split">
            <div className="shell cta-split-grid">
                <Reveal className="cta-split-copy" x={-30} budgeted>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Siap Memulai?'}</h2>
                    <p style={{ color: 'var(--sec-posttext, inherit)' }}>
                        {section.subtitle || section.post_title || 'Hubungi kami untuk konsultasi gratis.'}
                    </p>
                    {cta?.link && (
                        <a className="ui-btn ui-btn--primary" href={cta.link}>{cta.text || 'Hubungi Kami'}</a>
                    )}
                </Reveal>
                <Reveal className="cta-split-visual" x={30} delay={0.15} budgeted>
                    {cta?.backgroundImage
                        ? <img src={cta.backgroundImage} alt="" />
                        : <div className="cta-split-placeholder" />
                    }
                </Reveal>
            </div>
        </section>
    );
}
