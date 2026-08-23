import React from 'react';
import { motion } from 'framer-motion';
import { Section } from '../index';

/**
 * CTA Mode 4 — Split: teks kiri, visual kanan.
 * Animasi: slide-in from sides.
 */
export default function CtaMode4({ section, data }) {
    const cta = data.landing?.cta;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="cta cta--split">
            <div className="shell cta-split-grid">
                <motion.div
                    className="cta-split-copy"
                    initial={{ opacity: 0, x: -30 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true, margin: '-60px' }}
                    transition={{ duration: 0.6, ease }}
                >
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Siap Memulai?'}</h2>
                    <p style={{ color: 'var(--sec-posttext, inherit)' }}>
                        {section.subtitle || section.post_title || 'Hubungi kami untuk konsultasi gratis.'}
                    </p>
                    {cta?.link && (
                        <a className="ui-btn ui-btn--primary" href={cta.link}>{cta.text || 'Hubungi Kami'}</a>
                    )}
                </motion.div>
                <motion.div
                    className="cta-split-visual"
                    initial={{ opacity: 0, x: 30 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true, margin: '-60px' }}
                    transition={{ duration: 0.6, ease, delay: 0.15 }}
                >
                    {cta?.backgroundImage
                        ? <img src={cta.backgroundImage} alt="" />
                        : <div className="cta-split-placeholder" />
                    }
                </motion.div>
            </div>
        </section>
    );
}
