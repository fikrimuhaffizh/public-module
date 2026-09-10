import React from 'react';
import { ArrowUpRight } from 'lucide-react';
import { heroCopy } from '../index';
import HeroActions from './HeroActions';

/** Eight compositions sharing the CMS content contract and accessible actions. */
export default function HeroComposition({ mode, section, data }) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    const products = (data.landing?.products || []).slice(0, 4);
    const photos = products.filter(product => product.image);
    const stats = (data.landing?.statistics || []).slice(0, 2);
    const image = hero?.image || photos[0]?.image;
    const showImage = image && mode !== 6;
    const centered = [2, 6].includes(mode);
    const imageAlt = copy.imageAlt || data.site?.name || '';

    return (
        <section className={`landing-hero landing-hero--${mode}${!showImage ? ' landing-hero--text' : ''}`}>
            {mode === 4 && image && <img className="landing-hero__backdrop" src={image} alt="" aria-hidden="true" fetchPriority="high" />}
            <div className="shell landing-hero__grid">
                <div className="landing-hero__copy">
                    {section.pre_title && <p className="eyebrow landing-hero__eyebrow">{section.pre_title}</p>}
                    <h1>{copy.title}</h1>
                    {copy.subtitle && <p className="landing-hero__description">{copy.subtitle}</p>}
                    {section.post_title && <p className="landing-hero__posttitle">{section.post_title}</p>}
                    <HeroActions hero={hero} site={data.site} align={centered ? 'center' : 'left'} />
                </div>
                {showImage && mode !== 4 && (
                    <div className="landing-hero__media">
                        <img src={image} alt={imageAlt} fetchPriority="high" />
                        {mode === 3 && photos.length > 1 && <div className="landing-hero__gallery">
                            {photos.slice(1, 4).map((product, index) => <img key={product.id || index} src={product.image} alt={product.name || imageAlt} loading="lazy" />)}
                        </div>}
                        {mode === 8 && data.site?.name && <span className="landing-hero__caption">{data.site.name}<ArrowUpRight size={18} aria-hidden="true" /></span>}
                    </div>
                )}
                {[2, 3, 7].includes(mode) && stats.length > 0 && <dl className="landing-hero__stats">
                    {stats.map((stat, index) => <div key={stat.id || index}><dt>{stat.label}</dt><dd>{stat.value}</dd></div>)}
                </dl>}
            </div>
        </section>
    );
}
