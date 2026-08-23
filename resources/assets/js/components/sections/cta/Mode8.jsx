import React from 'react';
import { motion } from 'framer-motion';
import { ArrowRight } from 'lucide-react';

/**
 * CTA Mode 8 — Glow card: card center dengan border glow.
 * Animasi: scale-in dari kecil.
 */
export default function CtaMode8({ section, data }) {
    const cta = data.landing?.cta;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="cta cta--glow">
            <div className="shell" style={{ display: 'flex', justifyContent: 'center' }}>
                <motion.div
                    className="cta-glow-card"
                    initial={{ opacity: 0, scale: 0.92 }}
                    whileInView={{ opacity: 1, scale: 1 }}
                    viewport={{ once: true, margin: '-60px' }}
                    transition={{ duration: 0.6, ease }}
                >
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
                </motion.div>
            </div>
        </section>
    );
}
