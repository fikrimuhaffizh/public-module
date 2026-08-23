import React, { useMemo } from 'react';
import { motion, useReducedMotion } from 'framer-motion';
import { Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { heroCopy } from '../index';

/**
 * Hero Mode 6 — Minimal Large Typography: teks besar di tengah, tanpa gambar.
 * Animasi: word-by-word blur → clear reveal.
 * Elemen: pretitle, title, subtitle (tanpa gambar — fokus pada pesan).
 */
export default function HeroMode6({ section, data }) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    const reduceMotion = useReducedMotion();
    const ease = [0.22, 1, 0.36, 1];

    const words = useMemo(
        () => (copy.title || '').trim().split(/\s+/).filter(Boolean),
        [copy.title]
    );

    return (
        <section className="hero hero--minimal">
            <div className="hero-minimal-bg" aria-hidden="true" />
            <div className="shell hero-minimal-inner">
                <motion.div
                    initial={{ opacity: 0, y: 14 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, ease }}
                >
                    <Badge style={{ color: 'var(--sec-pretext, inherit)' }}>
                        <Sparkles size={14} /> {section.pre_title || 'Digital campus platform'}
                    </Badge>
                </motion.div>

                <h1 className="hero-minimal-title" style={{ color: 'var(--sec-title, inherit)' }}>
                    {reduceMotion
                        ? copy.title
                        : words.map((word, i) => (
                            <motion.span
                                key={i}
                                className="hero-minimal-word"
                                initial={{ opacity: 0, y: 20, filter: 'blur(6px)' }}
                                animate={{ opacity: 1, y: 0, filter: 'blur(0px)' }}
                                transition={{ delay: 0.15 + i * 0.06, duration: 0.55, ease }}
                            >
                                {word}
                            </motion.span>
                        ))
                    }
                </h1>

                <motion.p
                    className="hero-minimal-subtitle"
                    initial={{ opacity: 0, y: 16 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.5 + Math.min(words.length * 0.04, 0.3), duration: 0.5, ease }}
                    style={{ color: 'var(--sec-posttext, inherit)' }}
                >
                    {copy.subtitle}
                </motion.p>
            </div>
        </section>
    );
}
