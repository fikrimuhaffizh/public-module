import React from 'react';
import { Section, combinedText } from '../index';

/**
 * Fitur Mode 5 — bento grid: kartu unggulan besar (2×2, opsional gambar latar)
 * + kartu kecil asimetris, gaya Velora UI. Prop: { section, data }
 */
export default function FeatureMode5({ section, data }) {
    const features = data.landing?.features || [];
    if (!features.length) return null;
    const limit = section?.limit_data || 6;
    const items = features.slice(0, limit);

    return (
        <Section
            section={section}
            id="keunggulan"
            eyebrow={section.pre_title || 'Apa yang Kami Tawarkan'}
            title={section.title || 'Fitur Unggulan'}
            text={combinedText(section)}
        >
            <div className="bento-grid">
                {items.map((feature, i) => {
                    const featured = i === 0;
                    return (
                        <article
                            key={feature.id}
                            className={`bento-card gen-card${featured ? ' bento-card--featured' : ''}`}
                            style={{ animationDelay: `${i * 70}ms` }}
                        >
                            {featured && feature.image && (
                                <img src={feature.image} alt={feature.title} className="bento-card-img" loading="lazy" />
                            )}
                            <div className="bento-card-body">
                                {feature.icon && <span className={`feature-icon ${feature.icon}`} aria-hidden="true" />}
                                <h3>{feature.title}</h3>
                                <p>{feature.description}</p>
                            </div>
                        </article>
                    );
                })}
            </div>
        </Section>
    );
}
