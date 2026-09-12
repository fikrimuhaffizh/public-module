import React from 'react';
import { ArrowUpRight } from 'lucide-react';
import { HeroCopy, HeroMedia, useHeroData } from './HeroData';

/** Mode 8 — Lebar: copy atas, visual panorama bawah dengan caption institusi. */
export default function HeroMode8({ section, data }) {
    const { hero, copy, image, imageAlt, siteName, site } = useHeroData(section, data);
    return (
        <section className={`landing-hero landing-hero--8${!image ? ' landing-hero--text' : ''}`}>
            <div className="shell landing-hero__grid">
                <HeroCopy section={section} copy={copy} hero={hero} site={site} />
                {image && (
                    <HeroMedia image={image} imageAlt={imageAlt}>
                        {siteName && (
                            <span className="landing-hero__caption">
                                {siteName}<ArrowUpRight size={18} aria-hidden="true" />
                            </span>
                        )}
                    </HeroMedia>
                )}
            </div>
        </section>
    );
}
