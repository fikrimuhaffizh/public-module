import React, { useEffect, useState } from 'react';
import { motion, useReducedMotion } from 'framer-motion';
import { Section, combinedText, TestimonialRating } from '../index';

/**
 * Testimoni Mode 5 — animated list: daftar testimoni masuk berurutan, item aktif
 * berganti otomatis dengan progress bar + highlight halus. Prop: { section, data }
 */
export default function TestimonialMode5({ section, data }) {
    const list = data.testimonials || [];
    if (!list.length) return null;

    const limit = section?.limit_data || 6;
    const items = list.slice(0, limit);
    const [index, setIndex] = useState(0);
    const [paused, setPaused] = useState(false);
    const reduceMotion = useReducedMotion();

    // Auto-advance — mati saat hover, kurang dari 2 item, atau reduced motion.
    useEffect(() => {
        if (items.length < 2 || reduceMotion || paused) return undefined;
        const timer = window.setInterval(() => setIndex(i => (i + 1) % items.length), 5000);
        return () => window.clearInterval(timer);
    }, [items.length, reduceMotion, paused]);

    const active = index % items.length;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <Section
            section={section}
            eyebrow={section.pre_title || 'Kata Mereka'}
            title={section.title || 'Ulasan Pengguna'}
            text={combinedText(section)}
            narrow
        >
            <div
                className={`testimonial-animlist${paused ? ' is-paused' : ''}`}
                onMouseEnter={() => setPaused(true)}
                onMouseLeave={() => setPaused(false)}
            >
                <ol className="testimonial-animlist__list">
                    {items.map((t, i) => (
                        <motion.li
                            key={t.id}
                            className={`testimonial-animlist__item${i === active ? ' is-active' : ''}`}
                            initial={reduceMotion ? false : { opacity: 0, x: 34 }}
                            animate={{ opacity: i === active ? 1 : 0.55, x: 0 }}
                            transition={{
                                opacity: { duration: 0.45, ease },
                                x: { delay: reduceMotion ? 0 : i * 0.09, duration: 0.45, ease },
                            }}
                        >
                            <div className="testimonial-animlist__avatar">
                                {t.photo
                                    ? <img src={t.photo} alt={t.name} loading="lazy" />
                                    : <span className="testimonial-avatar">{t.name.charAt(0)}</span>}
                            </div>
                            <div className="testimonial-animlist__body">
                                <div className="testimonial-animlist__head">
                                    <strong>{t.name}</strong>
                                    <span>{t.position}{t.organization ? ` · ${t.organization}` : ''}</span>
                                    <TestimonialRating rating={t.rating} size={15} />
                                </div>
                                <p>“{t.quote}”</p>
                            </div>
                            {i === active && items.length > 1 && (
                                <span key={active} className="testimonial-animlist__progress" aria-hidden="true" />
                            )}
                        </motion.li>
                    ))}
                </ol>

                {items.length > 1 && (
                    <div className="testimonial-animlist__dots" role="tablist" aria-label="Pilih testimoni">
                        {items.map((t, i) => (
                            <button
                                type="button"
                                role="tab"
                                aria-selected={i === active}
                                key={t.id}
                                className={`testimonial-animlist__dot${i === active ? ' is-active' : ''}`}
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
