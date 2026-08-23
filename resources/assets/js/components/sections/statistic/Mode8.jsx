import React from 'react';
import { motion } from 'framer-motion';

/**
 * Statistik Mode 8 — Large center: angka besar center.
 * Animasi: zoom-in.
 */
export default function StatisticMode8({ section, data }) {
    const stats = data.landing?.statistics || [];
    if (!stats.length) return null;
    const limit = section?.limit_data || 3;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="stats stats--large" id="statistik">
            <div className="shell">
                <div style={{ textAlign: 'center', marginBottom: 36 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Angka yang Berbicara'}</h2>
                </div>
                <motion.div
                    className="stats-large-grid"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.15 } } }}
                >
                    {stats.slice(0, limit).map((stat, i) => (
                        <motion.div
                            key={stat.id || i}
                            className="stats-large-item"
                            variants={{ hidden: { opacity: 0, scale: 0.88 }, visible: { opacity: 1, scale: 1, transition: { duration: 0.6, ease } } }}
                        >
                            <strong className="stats-large-value">{stat.value}</strong>
                            <span className="stats-large-label">{stat.label}</span>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
