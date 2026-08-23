import React from 'react';
import { motion } from 'framer-motion';

/**
 * Statistik Mode 7 — Band: stat dalam bar horizontal.
 * Animasi: slide-in dari kiri.
 */
export default function StatisticMode7({ section, data }) {
    const stats = data.landing?.statistics || [];
    if (!stats.length) return null;
    const limit = section?.limit_data || 4;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="stats stats--band" id="statistik">
            <div className="shell">
                <motion.div
                    className="stats-band-inner"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.12 } } }}
                >
                    {stats.slice(0, limit).map((stat, i) => (
                        <motion.div
                            key={stat.id || i}
                            className="stats-band-item"
                            variants={{ hidden: { opacity: 0, x: -30 }, visible: { opacity: 1, x: 0, transition: { duration: 0.5, ease } } }}
                        >
                            <strong>{stat.value}</strong>
                            <span>{stat.label}</span>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
