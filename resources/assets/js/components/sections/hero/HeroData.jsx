import React from 'react';
import { heroCopy } from '../index';
import HeroActions from './HeroActions';

/**
 * Kontrak konten + blok penyusun bersama semua mode Hero.
 *
 * Tiap ModeN.jsx memakai hook & blok ini, tapi MENYUSUN strukturnya
 * sendiri-sendiri (split, centered, gallery, cinematic, dst.) - tidak ada
 * lagi `mode === n` conditional tersebar. Class CSS dipertahankan identik
 * (`landing-hero--n`, `landing-hero__*`) supaya visual tidak berubah.
 */
export function useHeroData(section, data) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    const products = (data.landing?.products || []).slice(0, 4);
    const photos = products.filter((product) => product.image);
    const stats = (data.landing?.statistics || []).slice(0, 2);
    const image = hero?.image || photos[0]?.image;
    const imageAlt = copy.imageAlt || data.site?.name || '';
    const siteName = data.site?.name || '';
    return { hero, copy, image, imageAlt, photos, stats, siteName, site: data.site };
}

export function HeroCopy({ section, copy, hero, site, align = 'left' }) {
    return (
        <div className="landing-hero__copy">
            {section.pre_title && <p className="eyebrow landing-hero__eyebrow">{section.pre_title}</p>}
            <h1>{copy.title}</h1>
            {copy.subtitle && <p className="landing-hero__description">{copy.subtitle}</p>}
            {section.post_title && <p className="landing-hero__posttitle">{section.post_title}</p>}
            <HeroActions hero={hero} site={site} align={align} />
        </div>
    );
}

export function HeroMedia({ image, imageAlt, children }) {
    return (
        <div className="landing-hero__media">
            <img src={image} alt={imageAlt} fetchPriority="high" />
            {children}
        </div>
    );
}

export function HeroGallery({ photos, imageAlt }) {
    return (
        <div className="landing-hero__gallery">
            {photos.slice(1, 4).map((product, index) => (
                <img key={product.id || index} src={product.image} alt={product.name || imageAlt} loading="lazy" />
            ))}
        </div>
    );
}

export function HeroStats({ stats }) {
    return (
        <dl className="landing-hero__stats">
            {stats.map((stat, index) => (
                <div key={stat.id || index}><dt>{stat.label}</dt><dd>{stat.value}</dd></div>
            ))}
        </dl>
    );
}
