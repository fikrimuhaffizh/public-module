import React from 'react';
import { Section, combinedText, TestimonialRating } from '../index';

/** Testimoni Mode 3 — daftar kutipan satu kolom dengan pembatas tegas. Prop: { section, data } */
export default function TestimonialMode3({ section, data }) {
    const list = data.testimonials || [];
    if (!list.length) return null;
    const limit = section?.limit_data || 6;
    return (
        <Section
            section={section}
            eyebrow={section.pre_title || 'Kata Mereka'}
            title={section.title || 'Ulasan Pengguna'}
            text={combinedText(section)}
        >
            <div className="testimonial-list">
                {list.slice(0, limit).map(item => (
                    <div key={item.id} className="testimonial-row">
                        <span className="testimonial-avatar">{item.name.charAt(0)}</span>
                        <div>
                            <TestimonialRating rating={item.rating} size={14} />
                            <p>“{item.quote}”</p>
                            <strong>{item.name}</strong>
                            <span>{item.position}{item.organization ? ` · ${item.organization}` : ''}</span>
                        </div>
                    </div>
                ))}
            </div>
        </Section>
    );
}
