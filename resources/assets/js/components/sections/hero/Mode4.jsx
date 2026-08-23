import React from 'react';
import { motion, useReducedMotion } from 'framer-motion';
import { Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { heroCopy } from '../index';

/**
 * Hero Mode 4 — Cinematic: gambar full-width sebagai background,
 * overlay gelap gradasi, teks di tengah bawah.
 * Animasi: gambar zoom-in pelan, teks fade-up bertahap.
 * Elemen: pretitle, title, subtitle, gambar.
 */
export default function HeroMode4({ section, data }) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    const reduceMotion = useReducedMotion();
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="hero hero--cinematic">
            {/* Background image with slow zoom */}
            {hero?.image && (
                <motion.div
                    className="hero-cinematic-bg"
                    initial={{ scale: 1.1 }}
                    animate={{ scale: 1 }}
                    transition={{ duration: 12, ease: 'easeOut' }}
                >
                    <img src={hero.image} alt="" aria-hidden="true" />
                </motion.div>
            )}

            {/* Dark gradient overlay */}
            <div className="hero-cinematic-scrim" />

            {/* Content */}
            <div className="shell hero-cinematic-content">
                <motion.div
                    initial={{ opacity: 0, y: 30 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, ease, delay: 0.2 }}
                >
                    <Badge style={{ color: 'var(--sec-pretext, rgba(255,255,255,.85))' }}>
                        <Sparkles size={14} /> {section.pre_title || 'Digital campus platform'}
                    </Badge>
                </motion.div>

                <motion.h1
                    initial={{ opacity: 0, y: 24 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, ease, delay: 0.35 }}
                    style={{ color: 'var(--sec-title, #ffffff)' }}
                >
                    {copy.title}
                </motion.h1>

                <motion.p
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, ease, delay: 0.5 }}
                    style={{ color: 'var(--sec-posttext, rgba(255,255,255,.82))' }}
                >
                    {copy.subtitle}
                </motion.p>
            </div>
        </section>
    );
}
