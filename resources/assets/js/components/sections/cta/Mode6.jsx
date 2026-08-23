import React from 'react';
import { motion } from 'framer-motion';
import { Check } from 'lucide-react';

/**
 * CTA Mode 6 — Floating cards: teks center + kartu fitur.
 * Animasi: stagger — kartu muncul satu per satu.
 */
export default function CtaMode6({ section, data }) {
    const cta = data.landing?.cta;
    const ease = [0.22, 1, 0.36, 1];
    const features = [
        section.settings?.cta_feature_1 || 'Setup 5 menit',
        section.settings?.cta_feature_2 || 'Tanpa kartu kredit',
        section.settings?.cta_feature_3 || 'Support 24/7',
    ];

    return (
        <section className="cta cta--cards">
            <div className="shell" style={{ textAlign: 'center' }}>
                {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Mengapa Memilih Kami?'}</h2>
                <p style={{ color: 'var(--sec-posttext, inherit)', maxWidth: 520, margin: '0 auto 32px' }}>
                    {section.subtitle || section.post_title || ''}
                </p>
                <motion.div
                    className="cta-cards-row"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-60px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.12 } } }}
                >
                    {features.map((f, i) => (
                        <motion.div
                            key={i}
                            className="cta-card-item gen-card"
                            variants={{ hidden: { opacity: 0, y: 20 }, visible: { opacity: 1, y: 0, transition: { duration: 0.5, ease } } }}
                        >
                            <Check size={20} style={{ color: 'var(--primary)' }} />
                            <span>{f}</span>
                        </motion.div>
                    ))}
                </motion.div>
                {cta?.link && (
                    <a className="ui-btn ui-btn--primary ui-btn--lg" href={cta.link} style={{ marginTop: 28 }}>
                        {cta.text || 'Mulai Sekarang'}
                    </a>
                )}
            </div>
        </section>
    );
}
