import React from 'react';
import { motion } from 'framer-motion';

/**
 * Feature Mode 7 — Numbered list: fitur bernomor besar.
 * Animasi: stagger — nomor muncul lalu teks slide-in.
 */
export default function FeatureMode7({ section, data }) {
    const features = data.landing?.features || [];
    if (!features.length) return null;
    const limit = section?.limit_data || 5;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="feature feature--numbered" id="keunggulan">
            <div className="shell">
                <div className="feature-numbered-grid">
                    <motion.div
                        className="feature-numbered-header"
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5, ease }}
                    >
                        {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                        <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Fitur Unggulan'}</h2>
                    </motion.div>
                    <div className="feature-numbered-list">
                        {features.slice(0, limit).map((feature, i) => (
                            <motion.div
                                key={feature.id}
                                className="feature-numbered-item"
                                initial={{ opacity: 0, x: 20 }}
                                whileInView={{ opacity: 1, x: 0 }}
                                viewport={{ once: true, margin: '-30px' }}
                                transition={{ duration: 0.5, ease, delay: i * 0.08 }}
                            >
                                <span className="feature-numbered-num">{String(i + 1).padStart(2, '0')}</span>
                                <div>
                                    <h3>{feature.title}</h3>
                                    <p>{feature.description}</p>
                                </div>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
