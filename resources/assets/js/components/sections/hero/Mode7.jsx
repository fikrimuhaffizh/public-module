import React from 'react';
import { motion } from 'framer-motion';
import { Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { heroCopy } from '../index';

/**
 * Hero Mode 7 — Bento Grid: layout asimetris dengan gambar + teks + statistik.
 * Animasi: stagger — semua elemen muncul satu per satu dari bawah.
 * Elemen: pretitle, title, subtitle, gambar, statistik ringkas.
 */
export default function HeroMode7({ section, data }) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    const ease = [0.22, 1, 0.36, 1];
    const stats = (data.landing?.statistics || []).slice(0, 2);

    const stagger = {
        hidden: {},
        visible: { transition: { staggerChildren: 0.12 } },
    };
    const fadeUp = {
        hidden: { opacity: 0, y: 24 },
        visible: { opacity: 1, y: 0, transition: { duration: 0.55, ease } },
    };

    return (
        <section className="hero hero--bento">
            <div className="shell">
                <motion.div
                    className="hero-bento-grid"
                    variants={stagger}
                    initial="hidden"
                    animate="visible"
                >
                    {/* Main copy — large cell */}
                    <motion.div className="hero-bento-main" variants={fadeUp}>
                        <Badge style={{ color: 'var(--sec-pretext, inherit)' }}>
                            <Sparkles size={14} /> {section.pre_title || 'Digital campus platform'}
                        </Badge>
                        <h1 style={{ color: 'var(--sec-title, inherit)' }}>{copy.title}</h1>
                        <p style={{ color: 'var(--sec-posttext, inherit)' }}>{copy.subtitle}</p>
                    </motion.div>

                    {/* Image cell */}
                    {hero?.image && (
                        <motion.div className="hero-bento-img" variants={fadeUp}>
                            <img src={hero.image} alt={copy.imageAlt} loading="eager" />
                        </motion.div>
                    )}

                    {/* Stats cells */}
                    {stats.map((stat, i) => (
                        <motion.div key={i} className="hero-bento-stat" variants={fadeUp}>
                            <strong style={{ color: 'var(--sec-title, inherit)' }}>
                                {stat.value}
                            </strong>
                            <span style={{ color: 'var(--sec-posttext, inherit)' }}>
                                {stat.label}
                            </span>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
