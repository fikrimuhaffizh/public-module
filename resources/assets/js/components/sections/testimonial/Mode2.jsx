import React from 'react';
import { Section, combinedText, TestimonialRating } from '../index';

/** Testimoni Mode 2 — spotlight: satu kutipan besar di tengah, avatar di bawah. Prop: { section, data } */
export default function TestimonialMode2({ section, data }) {
    const list = data.testimonials || [];
    if (!list.length) return null;
    const item = list[0];
    return (
        <Section
            section={section}
            eyebrow={section.pre_title || 'Kata Mereka'}
            title={section.title || 'Ulasan Pengguna'}
            text={combinedText(section)}
            narrow
        >
            <figure className="testimonial-spotlight">
                <TestimonialRating rating={item.rating} size={18} />
                <blockquote>“{item.quote}”</blockquote>
                <figcaption>
                    {item.photo
                        ? <img src={item.photo} alt={item.name} />
                        : <span className="testimonial-avatar">{item.name.charAt(0)}</span>}
                    <strong>{item.name}</strong>
                    <span>{item.position}{item.organization ? ` · ${item.organization}` : ''}</span>
                </figcaption>
            </figure>
        </Section>
    );
}
