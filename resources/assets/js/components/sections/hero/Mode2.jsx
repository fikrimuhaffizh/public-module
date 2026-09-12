import React from 'react';
import { HeroCopy, HeroMedia, HeroStats, useHeroData } from './HeroData';

/** Mode 2 — Centered: copy tengah, banner visual lebar, deret statistik. */
export default function HeroMode2({ section, data }) {
    const { hero, copy, image, imageAlt, stats, site } = useHeroData(section, data);
    return (
        <section className={`landing-hero landing-hero--2${!image ? ' landing-hero--text' : ''}`}>
            <div className="shell landing-hero__grid">
                <HeroCopy section={section} copy={copy} hero={hero} site={site} align="center" />
                {image && <HeroMedia image={image} imageAlt={imageAlt} />}
                {stats.length > 0 && <HeroStats stats={stats} />}
            </div>
        </section>
    );
}
