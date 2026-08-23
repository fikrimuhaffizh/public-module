import React from 'react';
import { motion } from 'framer-motion';
import { Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { heroCopy } from '../index';

/**
 * Hero Mode 8 — Immersive Gradient: gradasi warna bergerak sebagai background,
 * teks + gambar mengambang.
 * Animasi: gradient berputar pelan, elemen fade-in dari bawah.
 * Elemen: pretitle, title, subtitle, gambar.
 */
export default function HeroMode8({ section, data }) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="hero hero--gradient">
            {/* Animated gradient orbs */}
            <div className="hero-gradient-bg" aria-hidden="true">
                <motion.div
                    className="hero-gradient-orb hero-gradient-orb--1"
                    animate={{ rotate: 360 }}
                    transition={{ duration: 30, repeat: Infinity, ease: 'linear' }}
                />
                <motion.div
                    className="hero-gradient-orb hero-gradient-orb--2"
                    animate={{ rotate: -360 }}
                    transition={{ duration: 24, repeat: Infinity, ease: 'linear' }}
                />
            </div>

            <div className="shell hero-gradient-inner">
                <motion.div
                    className="hero-gradient-copy"
                    initial={{ opacity: 0, y: 28 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, ease, delay: 0.15 }}
                >
                    <Badge style={{ color: 'var(--sec-pretext, inherit)' }}>
                        <Sparkles size={14} /> {section.pre_title || 'Digital campus platform'}
                    </Badge>
                    <h1 style={{ color: 'var(--sec-title, inherit)' }}>{copy.title}</h1>
                    <p style={{ color: 'var(--sec-posttext, inherit)' }}>{copy.subtitle}</p>
                </motion.div>

                {hero?.image && (
                    <motion.div
                        className="hero-gradient-visual"
                        initial={{ opacity: 0, scale: 0.92, y: 20 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        transition={{ duration: 0.7, ease, delay: 0.35 }}
                    >
                        <img src={hero.image} alt={copy.imageAlt} />
                    </motion.div>
                )}
            </div>
        </section>
    );
}
