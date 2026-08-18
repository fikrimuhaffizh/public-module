import React, { useEffect, useRef, useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { Section, combinedText, TestimonialRating } from '../index';

/** Testimoni Mode 4 — rotator: satu kutipan besar berganti otomatis dengan transisi halus + dots. Prop: { section, data } */
export default function TestimonialMode4({ section, data }) {
    const list = data.testimonials || [];
    if (!list.length) return null;

    const limit = section?.limit_data || 6;
    const items = list.slice(0, limit);
    const [index, setIndex] = useState(0);
    const timer = useRef(null);

    useEffect(() => {
        if (items.length < 2) return undefined;
        timer.current = window.setInterval(() => setIndex(i => (i + 1) % items.length), 5200);

        return () => window.clearInterval(timer.current);
    }, [items.length]);

    const pause = () => window.clearInterval(timer.current);
    const resume = () => {
        if (items.length < 2) return;
        window.clearInterval(timer.current);
        timer.current = window.setInterval(() => setIndex(i => (i + 1) % items.length), 5200);
    };

    const item = items[index % items.length];

    return (
        <Section
            section={section}
            eyebrow={section.pre_title || 'Kata Mereka'}
            title={section.title || 'Ulasan Pengguna'}
            text={combinedText(section)}
            narrow
        >
            <div className="testimonial-rotator" onMouseEnter={pause} onMouseLeave={resume}>
                <AnimatePresence mode="wait">
                    <motion.figure
                        key={item.id}
                        className="testimonial-rotator__item"
                        initial={{ opacity: 0, x: 28 }}
                        animate={{ opacity: 1, x: 0 }}
                        exit={{ opacity: 0, x: -28 }}
                        transition={{ duration: 0.4, ease: [0.22, 1, 0.36, 1] }}
                    >
                        <TestimonialRating rating={item.rating} size={18} />
                        <blockquote>“{item.quote}”</blockquote>
                        <figcaption>
                            {item.photo
                                ? <img src={item.photo} alt={item.name} />
                                : <span className="testimonial-avatar">{item.name.charAt(0)}</span>}
                            <strong>{item.name}</strong>
                            <span>{item.position}{item.organization ? ` · ${item.organization}` : ''}</span>
                        </figcaption>
                    </motion.figure>
                </AnimatePresence>
                {items.length > 1 && (
                    <div className="testimonial-rotator__dots" role="tablist" aria-label="Pilih testimoni">
                        {items.map((t, i) => (
                            <button
                                type="button"
                                role="tab"
                                aria-selected={i === index % items.length}
                                key={t.id}
                                className={`testimonial-rotator__dot${i === index % items.length ? ' is-active' : ''}`}
                                onClick={() => setIndex(i)}
                                aria-label={`Testimoni ${i + 1}`}
                            />
                        ))}
                    </div>
                )}
            </div>
        </Section>
    );
}
