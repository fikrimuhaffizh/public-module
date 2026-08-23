import React from 'react';
import { motion } from 'framer-motion';

/**
 * Statistik Mode 5 — Horizontal scroll: stat dalam scroll.
 * Animasi: slide-in dari bawah.
 */
export default function StatisticMode5({ section, data }) {
    const stats = data.landing?.statistics || [];
    if (!stats.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="stats stats--scroll" id="statistik">
            <div className="shell">
                {section.pre_title && <span className="eyebrow" style={{ display: 'block', textAlign: 'center', marginBottom: 8 }}>{section.pre_title}</span>}
                <h2 className="section-heading" style={{ textAlign: 'center', color: 'var(--sec-title, inherit)' }}>
                    {section.title || 'Angka yang Berbicara'}
                </h2>
                <motion.div
                    className="stats-scroll-track"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.08 } } }}
                >
                    {stats.slice(0, limit).map((stat, i) => (
                        <motion.div
                            key={stat.id || i}
                            className="stats-scroll-item"
                            variants={{ hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease } } }}
                        >
                            <strong style={{ color: 'var(--sec-title, inherit)' }}>{stat.value}</strong>
                            <span style={{ color: 'var(--sec-posttext, inherit)' }}>{stat.label}</span>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
