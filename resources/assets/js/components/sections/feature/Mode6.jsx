import React from 'react';
import { motion } from 'framer-motion';

/**
 * Feature Mode 6 — Side-by-side zigzag: teks + gambar bergantian.
 * Animasi: fade-in dari kiri/kanan bergantian.
 */
export default function FeatureMode6({ section, data }) {
    const features = data.landing?.features || [];
    if (!features.length) return null;
    const limit = section?.limit_data || 4;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="feature feature--zigzag" id="keunggulan">
            <div className="shell">
                {section.pre_title && <span className="eyebrow" style={{ display: 'block', textAlign: 'center', marginBottom: 8 }}>{section.pre_title}</span>}
                <h2 className="section-heading" style={{ textAlign: 'center', color: 'var(--sec-title, inherit)' }}>
                    {section.title || 'Fitur Unggulan'}
                </h2>
                <div className="feature-zigzag-list">
                    {features.slice(0, limit).map((feature, i) => (
                        <motion.div
                            key={feature.id}
                            className={`feature-zigzag-row ${i % 2 === 1 ? 'feature-zigzag-row--reversed' : ''}`}
                            initial={{ opacity: 0, x: i % 2 === 0 ? -30 : 30 }}
                            whileInView={{ opacity: 1, x: 0 }}
                            viewport={{ once: true, margin: '-40px' }}
                            transition={{ duration: 0.6, ease }}
                        >
                            <div className="feature-zigzag-copy">
                                <h3>{feature.title}</h3>
                                <p>{feature.description}</p>
                            </div>
                            <div className="feature-zigzag-img">
                                {feature.image
                                    ? <img src={feature.image} alt={feature.title} loading="lazy" />
                                    : <div className="feature-zigzag-placeholder" />
                                }
                            </div>
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
}
