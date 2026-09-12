import React from 'react';
import { ArrowRight } from 'lucide-react';
import { Reveal } from '@public/components/motion/effects';

/**
 * CTA Mode 8 — Glow card: card center dengan border glow.
 * Animasi: scale-in dari kecil (budgeted).
 */
export default function CtaMode8({ section, data }) {
    const cta = data.landing?.cta;

    return (
        <section className="cta cta--glow">
            <div className="shell" style={{ display: 'flex', justifyContent: 'center' }}>
                <Reveal className="cta-glow-card" scale={0.92} budgeted>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Upgrade ke Pro'}</h2>
                    <p style={{ color: 'var(--sec-posttext, inherit)' }}>
                        {section.subtitle || section.post_title || 'Akses fitur premium tanpa batas.'}
                    </p>
                    {cta?.link && (
                        <a className="ui-btn ui-btn--gradient ui-btn--lg" href={cta.link}>
                            {cta.text || 'Upgrade Sekarang'} <ArrowRight size={18} />
                        </a>
                    )}
                </Reveal>
            </div>
        </section>
    );
}
