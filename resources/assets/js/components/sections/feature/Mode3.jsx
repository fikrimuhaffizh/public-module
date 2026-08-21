import React from 'react';
import { Check } from 'lucide-react';
import { Section, combinedText } from '../index';

/** Fitur Mode 3 — daftar dua kolom dengan ikon centang. Prop: { section, data } */
export default function FeatureMode3({ section, data }) {
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
            <div className="feature-check-grid">
                {features.slice(0, limit).map(feature => (
                    <div key={feature.id} className="feature-check">
                        {feature.image
                            ? <span className="feature-check-thumb"><img src={feature.image} alt={feature.title} loading="lazy" /></span>
                            : <span className="feature-check-icon"><Check size={16} /></span>
                        }
                        <div>
                            <strong>{feature.title}</strong>
                            <p>{feature.description}</p>
                        </div>
                    </div>
                ))}
            </div>
        </Section>
    );
}
