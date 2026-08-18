import React from 'react';
import { Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Reveal } from '@public/components/motion/effects';
import HeroActions from './HeroActions';
import { heroCopy } from '../index';

/**
 * Hero Mode 1 — split: teks di kiri, visual/browser mockup di kanan.
 * Prop: { section, data }
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
                    <Badge><Sparkles size={14} /> {section.pre_title || 'Digital campus platform'}</Badge>
                    <h1>{copy.title}</h1>
                    <p>{copy.subtitle}</p>
                    <HeroActions hero={hero} site={data.site} align="left" />
                </Reveal>
                <Reveal className="modern-visual" delay={0.15}>
                    <div className="modern-browser">
                        <div className="modern-browser-bar"><i /><i /><i /></div>
                        {hero?.image && <img src={hero.image} alt={copy.imageAlt} />}
                    </div>
                    <div className="floating-stat">
                        <strong>{data.pages.length}+</strong>
                        <span>Layanan informasi</span>
                    </div>
                    <div className="floating-status">
                        <span className="status-dot" />
                        Sistem informasi aktif
                    </div>
                </Reveal>
            </div>
        </section>
    );
}
