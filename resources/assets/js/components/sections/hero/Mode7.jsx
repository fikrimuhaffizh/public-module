import React from 'react';
import { HeroCopy, HeroMedia, HeroStats, useHeroData } from './HeroData';

/** Mode 7 - Bento: copy dalam kartu + visual + statistik berbentuk kartu. */
export default function HeroMode7({ section, data }) {
    const { hero, copy, image, imageAlt, stats, site } = useHeroData(section, data);
    return (
        <section className={`landing-hero landing-hero--7${!image ? ' landing-hero--text' : ''}`}>
            <div className="shell landing-hero__grid">
                <HeroCopy section={section} copy={copy} hero={hero} site={site} />
                {image && <HeroMedia image={image} imageAlt={imageAlt} />}
                {stats.length > 0 && <HeroStats stats={stats} />}
            </div>
        </section>
    );
}
