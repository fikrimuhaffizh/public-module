import React from 'react';
import { Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Reveal } from '@public/components/motion/effects';
import { heroCopy } from '../index';

/**
 * Hero Mode 1 — split: teks di kiri, gambar di kanan.
 * Elemen: pretitle (badge), title (h1), subtitle (p), gambar.
 * Tanpa tombol CTA atau microcopy — fokus pada konten.
 */
export default function HeroMode1({ section, data }) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    return (
        <section className="hero hero--modern">
            <div className="hero-orb hero-orb--one" />
            <div className="hero-orb hero-orb--two" />
            <div className="shell modern-hero-grid">
                <Reveal className="hero-content">
                    <Badge style={{ color: 'var(--sec-pretext, inherit)' }}><Sparkles size={14} /> {section.pre_title || 'Digital campus platform'}</Badge>
                    <h1 style={{ color: 'var(--sec-title, inherit)' }}>{copy.title}</h1>
                    <p style={{ color: 'var(--sec-posttext, inherit)' }}>{copy.subtitle}</p>
                </Reveal>
                <Reveal className="modern-visual" delay={0.15}>
                    {hero?.image && <img src={hero.image} alt={copy.imageAlt} />}
                </Reveal>
            </div>
        </section>
    );
}
