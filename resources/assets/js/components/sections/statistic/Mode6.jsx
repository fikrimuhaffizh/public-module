import React from 'react';
import { motion } from 'framer-motion';

/**
 * Statistik Mode 6 — Split: stats kiri, gambar kanan.
 * Animasi: fade alternatif kiri/kanan.
 */
export default function StatisticMode6({ section, data }) {
    const stats = data.landing?.statistics || [];
    if (!stats.length) return null;
    const limit = section?.limit_data || 4;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="stats stats--split" id="statistik">
            <div className="shell stats-split-grid">
                <motion.div
                    className="stats-split-list"
                    initial={{ opacity: 0, x: -20 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, ease }}
                >
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Angka yang Berbicara'}</h2>
                    {stats.slice(0, limit).map((stat, i) => (
                        <div key={stat.id || i} className="stats-split-item">
                            <strong style={{ color: 'var(--sec-title, inherit)' }}>{stat.value}</strong>
                            <span style={{ color: 'var(--sec-posttext, inherit)' }}>{stat.label}</span>
                        </div>
                    ))}
                </motion.div>
                <motion.div
                    className="stats-split-visual"
                    initial={{ opacity: 0, x: 20 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, ease, delay: 0.15 }}
                >
                    <div className="stats-split-placeholder" />
                </motion.div>
            </div>
        </section>
    );
}
