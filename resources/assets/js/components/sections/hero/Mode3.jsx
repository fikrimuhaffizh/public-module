import React from 'react';
import { HeroCopy, HeroGallery, HeroMedia, HeroStats, useHeroData } from './HeroData';

/** Mode 3 — Split + galeri foto + statistik bergaris batas. */
export default function HeroMode3({ section, data }) {
    const { hero, copy, image, imageAlt, photos, stats, site } = useHeroData(section, data);
    return (
        <section className={`landing-hero landing-hero--3${!image ? ' landing-hero--text' : ''}`}>
            <div className="shell landing-hero__grid">
                <HeroCopy section={section} copy={copy} hero={hero} site={site} />
                {image && (
                    <HeroMedia image={image} imageAlt={imageAlt}>
                        {photos.length > 1 && <HeroGallery photos={photos} imageAlt={imageAlt} />}
                    </HeroMedia>
                )}
                {stats.length > 0 && <HeroStats stats={stats} />}
            </div>
        </section>
    );
}
