import React from 'react';
import { HeroCopy, useHeroData } from './HeroData';

/** Mode 4 — Cinematic: backdrop foto full-bleed + scrim, copy menempel bawah. Tanpa kolom media. */
export default function HeroMode4({ section, data }) {
    const { hero, copy, image, site } = useHeroData(section, data);
    return (
        <section className={`landing-hero landing-hero--4${!image ? ' landing-hero--text' : ''}`}>
            {image && <img className="landing-hero__backdrop" src={image} alt="" aria-hidden="true" fetchPriority="high" />}
            <div className="shell landing-hero__grid">
                <HeroCopy section={section} copy={copy} hero={hero} site={site} />
            </div>
        </section>
    );
}
