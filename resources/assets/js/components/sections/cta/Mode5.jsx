import React from 'react';
import { motion } from 'framer-motion';

/**
 * CTA Mode 5 — Banner gradient: background gradient + teks besar center.
 * Animasi: zoom-in dari kecil.
 */
export default function CtaMode5({ section, data }) {
    const cta = data.landing?.cta;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="cta cta--banner">
            <div className="shell">
                <motion.div
                    className="cta-banner-inner"
                    initial={{ opacity: 0, scale: 0.95 }}
                    whileInView={{ opacity: 1, scale: 1 }}
                    viewport={{ once: true, margin: '-60px' }}
                    transition={{ duration: 0.6, ease }}
                >
                    {section.pre_title && <span className="eyebrow" style={{ color: 'var(--sec-pretext, rgba(255,255,255,.8))' }}>{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, #fff)' }}>{section.title || 'Mulai Sekarang'}</h2>
                    <p style={{ color: 'var(--sec-posttext, rgba(255,255,255,.85))' }}>
                        {section.subtitle || section.post_title || 'Jangan lewatkan kesempatan ini.'}
                    </p>
                    {cta?.link && (
                        <a className="ui-btn ui-btn--glass" href={cta.link}>{cta.text || 'Daftar Gratis'}</a>
                    )}
                </motion.div>
            </div>
        </section>
    );
}
