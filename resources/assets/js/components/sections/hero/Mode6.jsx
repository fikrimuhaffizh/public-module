import React from 'react';
import { HeroCopy, useHeroData } from './HeroData';

/** Mode 6 — Minimal: tipografi raksasa tengah, tanpa visual sama sekali. */
export default function HeroMode6({ section, data }) {
    const { hero, copy, site } = useHeroData(section, data);
    return (
        <section className="landing-hero landing-hero--6 landing-hero--text">
            <div className="shell landing-hero__grid">
                <HeroCopy section={section} copy={copy} hero={hero} site={site} align="center" />
            </div>
        </section>
    );
}
