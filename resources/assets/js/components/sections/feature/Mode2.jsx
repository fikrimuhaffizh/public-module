import React from 'react';
import { Section, combinedText } from '../index';

/** Fitur Mode 2 — grid kartu ikon (dari landing.features). Prop: { section, data } */
export default function FeatureMode2({ section, data }) {
    const features = data.landing?.features || [];
    if (!features.length) return null;
    const limit = section?.limit_data || 6;
    return (
        <Section
            section={section}
            id="keunggulan"
            eyebrow={section.pre_title || 'Apa yang Kami Tawarkan'}
            title={section.title || 'Fitur Unggulan'}
            text={combinedText(section)}
        >
            <div className="feature-grid">
                {features.slice(0, limit).map(feature => (
                    <div key={feature.id} className="feature-icon-card gen-card">
                        {feature.image && (
                            <div className="feature-card-img">
                                <img src={feature.image} alt={feature.title} loading="lazy" />
                            </div>
                        )}
                        {feature.icon && <span className={`feature-icon ${feature.icon}`} aria-hidden="true" />}
                        <h3>{feature.title}</h3>
                        <p>{feature.description}</p>
                    </div>
                ))}
            </div>
        </Section>
    );
}
