import React from 'react';
import { Section, combinedText } from '../index';

/** Fitur Mode 1 — PlatformOverview: teks + visual split. Prop: { section, data } */
export default function FeatureMode1({ section, data }) {
    const items = (data.landing?.features || []).slice(0, section.limit_data || 4);
    if (!items.length) return null;
    const image = items.find(item => item.image)?.image || data.landing?.hero?.image;
    return (
        <Section section={section} id="keunggulan" eyebrow={section.pre_title || 'Keunggulan'}
            title={section.title || 'Dirancang untuk kebutuhan Anda'} text={combinedText(section)}>
            <div className={`feature-overview${image ? '' : ' feature-overview--text'}`}>
                {image && <img className="feature-overview__image" src={image} alt={items[0].title || ''} loading="lazy" />}
                <div className="feature-overview__list">
                    {items.map((item, index) => <article key={item.id || index}>
                        <span className="feature-overview__number" aria-hidden="true">{String(index + 1).padStart(2, '0')}</span>
                        <div><h3>{item.title}</h3><p>{item.description}</p></div>
                    </article>)}
                </div>
            </div>
        </Section>
    );
}
