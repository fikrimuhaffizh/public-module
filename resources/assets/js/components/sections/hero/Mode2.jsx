import React, { useMemo } from 'react';
import { motion, useReducedMotion } from 'framer-motion';
import { Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import HeroActions from './HeroActions';
import { heroCopy } from '../index';

/**
 * Hero Mode 2 — aurora animasi: latar gradien bergerak, judul muncul kata per kata
 * (stagger + blur), plus chip mengambang dari data statistik.
 * Prop: { section, data }
 */
export default function HeroMode2({ section, data }) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    const reduceMotion = useReducedMotion();

    const words = useMemo(
        () => (copy.title || '').trim().split(/\s+/).filter(Boolean),
        [copy.title]
    );
    const chips = useMemo(
        () => (data.landing?.statistics || []).slice(0, 2),
        [data.landing]
    );
    const ease = [0.22, 1, 0.36, 1];
    const revealDelay = 0.45 + Math.min(words.length * 0.05, 0.4);

    const blobs = [
        { className: 'hero-aurora-blob hero-aurora-blob--1', dur: 16, x: [0, 42, -22, 0], y: [0, -30, 20, 0] },
        { className: 'hero-aurora-blob hero-aurora-blob--2', dur: 20, x: [0, -36, 26, 0], y: [0, 26, -24, 0] },
        { className: 'hero-aurora-blob hero-aurora-blob--3', dur: 18, x: [0, 24, -30, 0], y: [0, -18, 16, 0] },
    ];

    return (
        <section className="hero hero--aurora">
            <div className="hero-aurora-bg" aria-hidden="true">
                {blobs.map((b, i) => (
                    reduceMotion
                        ? <span key={i} className={b.className} />
                        : <motion.span
                            key={i}
                            className={b.className}
                            animate={{ x: b.x, y: b.y }}
                            transition={{ duration: b.dur, repeat: Infinity, ease: 'easeInOut' }}
                        />
                ))}
            </div>

            <div className="shell hero-aurora-inner">
                <div className="hero-aurora-copy">
                    <motion.div
                        initial={{ opacity: 0, y: 14 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5, ease }}
                    >
                        <Badge style={{ color: 'var(--sec-pretext, inherit)' }}><Sparkles size={14} /> {section.pre_title || 'Digital campus platform'}</Badge>
                    </motion.div>

                    <h1 className="hero-aurora-title" style={{ color: 'var(--sec-title, inherit)' }}>
                        {reduceMotion
                            ? copy.title
                            : words.map((word, i) => (
                                <motion.span
                                    key={i}
                                    className="hero-aurora-word"
                                    initial={{ opacity: 0, y: 22, filter: 'blur(8px)' }}
                                    animate={{ opacity: 1, y: 0, filter: 'blur(0px)' }}
                                    transition={{ delay: 0.1 + i * 0.07, duration: 0.55, ease }}
                                >
                                    {word}
                                </motion.span>
                            ))}
                    </h1>

                    <motion.p
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: revealDelay, duration: 0.5, ease }}
                        style={{ color: 'var(--sec-posttext, inherit)' }}
                    >
                        {copy.subtitle}
                    </motion.p>

                    <motion.div
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: revealDelay + 0.1, duration: 0.5, ease }}
                    >
                        <HeroActions hero={hero} site={data.site} align="center" />
                    </motion.div>
                </div>

                {hero?.image && (
                    <motion.div
                        className="hero-aurora-visual"
                        initial={{ opacity: 0, y: 28 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: revealDelay + 0.15, duration: 0.6, ease }}
                    >
                        <img src={hero.image} alt={copy.imageAlt} />
                        {chips[0] && (
                            <span className="hero-aurora-chip hero-aurora-chip--1">
                                <strong>{chips[0].value}</strong>{chips[0].label}
                            </span>
                        )}
                        {chips[1] && (
                            <span className="hero-aurora-chip hero-aurora-chip--2">
                                <strong>{chips[1].value}</strong>{chips[1].label}
                            </span>
                        )}
                    </motion.div>
                )}
            </div>
        </section>
    );
}
