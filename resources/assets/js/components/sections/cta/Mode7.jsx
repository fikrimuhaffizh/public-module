import React from 'react';
import { motion } from 'framer-motion';

/**
 * CTA Mode 7 — Minimal center: teks besar, clean, elegan.
 * Animasi: fade-up.
 */
export default function CtaMode7({ section, data }) {
    const cta = data.landing?.cta;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="cta cta--minimal">
            <div className="shell" style={{ textAlign: 'center', maxWidth: 720 }}>
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, margin: '-60px' }}
                    transition={{ duration: 0.6, ease }}
                >
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
                </motion.div>
            </div>
        </section>
    );
}
