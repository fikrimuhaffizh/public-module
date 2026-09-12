import React from 'react';
import { HeroCopy, HeroMedia, useHeroData } from './HeroData';

/** Mode 1 — Split klasik: copy kiri, visual kanan. */
export default function HeroMode1({ section, data }) {
    const { hero, copy, image, imageAlt, site } = useHeroData(section, data);
    return (
        <section className={`landing-hero landing-hero--1${!image ? ' landing-hero--text' : ''}`}>
            <div className="shell landing-hero__grid">
                <HeroCopy section={section} copy={copy} hero={hero} site={site} />
                {image && <HeroMedia image={image} imageAlt={imageAlt} />}
            </div>
        </section>
    );
}
