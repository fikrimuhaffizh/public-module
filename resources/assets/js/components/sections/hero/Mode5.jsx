import React from 'react';
import { motion } from 'framer-motion';
import { Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { heroCopy } from '../index';

/**
 * Hero Mode 5 — Split Reversed: gambar di kiri, teks di kanan.
 * Animasi: gambar slide-in dari kiri, teks fade-up dari kanan.
 * Elemen: pretitle, title, subtitle, gambar.
 */
export default function HeroMode5({ section, data }) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="hero hero--split-reverse">
            <div className="shell hero-split-grid">
                {/* Image — left */}
                <motion.div
                    className="hero-split-visual"
                    initial={{ opacity: 0, x: -40 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ duration: 0.7, ease, delay: 0.1 }}
                >
                    {hero?.image && (
                        <img src={hero.image} alt={copy.imageAlt} />
                    )}
                </motion.div>

                {/* Text — right */}
                <motion.div
                    className="hero-split-copy"
                    initial={{ opacity: 0, x: 40 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ duration: 0.7, ease, delay: 0.25 }}
                >
                    <Badge style={{ color: 'var(--sec-pretext, inherit)' }}>
                        <Sparkles size={14} /> {section.pre_title || 'Digital campus platform'}
                    </Badge>
                    <h1 style={{ color: 'var(--sec-title, inherit)' }}>{copy.title}</h1>
                    <p style={{ color: 'var(--sec-posttext, inherit)' }}>{copy.subtitle}</p>
                </motion.div>
            </div>
        </section>
    );
}
