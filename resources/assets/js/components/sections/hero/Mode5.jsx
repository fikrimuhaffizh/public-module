import React from 'react';
import { HeroCopy, HeroMedia, useHeroData } from './HeroData';

/** Mode 5 — Split reversed: visual dulu (potret 4/5), copy kemudian. Urutan visual diatur CSS. */
export default function HeroMode5({ section, data }) {
    const { hero, copy, image, imageAlt, site } = useHeroData(section, data);
    return (
        <section className={`landing-hero landing-hero--5${!image ? ' landing-hero--text' : ''}`}>
            <div className="shell landing-hero__grid">
                <HeroCopy section={section} copy={copy} hero={hero} site={site} />
                {image && <HeroMedia image={image} imageAlt={imageAlt} />}
            </div>
        </section>
    );
}
