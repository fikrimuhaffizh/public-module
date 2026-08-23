import React from 'react';
import { motion } from 'framer-motion';

/**
 * Statistik Mode 4 — Counter cards: kartu counter.
 * Animasi: stagger + count-up.
 */
export default function StatisticMode4({ section, data }) {
    const stats = data.landing?.statistics || [];
    if (!stats.length) return null;
    const limit = section?.limit_data || 4;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="stats stats--cards" id="statistik">
            <div className="shell">
                <div style={{ textAlign: 'center', marginBottom: 32 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Angka yang Berbicara'}</h2>
                </div>
                <motion.div
                    className="stats-cards-grid"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.1 } } }}
                >
                    {stats.slice(0, limit).map((stat, i) => (
                        <motion.div
                            key={stat.id || i}
                            className="stats-card gen-card"
                            variants={{ hidden: { opacity: 0, y: 20 }, visible: { opacity: 1, y: 0, transition: { duration: 0.5, ease } } }}
                        >
                            <span className="stats-card-value" style={{ color: 'var(--sec-title, inherit)' }}>
                                {stat.value}
                            </span>
                            <span className="stats-card-label" style={{ color: 'var(--sec-posttext, inherit)' }}>
                                {stat.label}
                            </span>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
