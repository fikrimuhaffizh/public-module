import React from 'react';
import { Reveal } from '@public/components/motion/effects';

/**
 * CTA Mode 7 — Minimal center: teks besar, clean, elegan.
 * Animasi: fade-up (budgeted).
 */
export default function CtaMode7({ section, data }) {
    const cta = data.landing?.cta;

    return (
        <section className="cta cta--minimal">
            <div className="shell" style={{ textAlign: section.settings?.text_align || 'center', maxWidth: 720 }}>
                <Reveal budgeted>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)', fontSize: 'clamp(28px, 4vw, 48px)' }}>
                        {section.title || 'Ada Pertanyaan?'}
                    </h2>
                    <p style={{ color: 'var(--sec-posttext, inherit)', margin: '16px auto 32px', maxWidth: 520 }}>
                        {section.subtitle || section.post_title || 'Tim kami siap membantu Anda.'}
                    </p>
                    {cta?.link && (
                        <a className="ui-btn ui-btn--outline ui-btn--lg" href={cta.link}>{cta.text || 'Hubungi Kami'}</a>
                    )}
                </Reveal>
            </div>
        </section>
    );
}
