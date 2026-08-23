import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { ChevronLeft, ChevronRight, Star } from 'lucide-react';

/**
 * Testimonial Mode 7 — Card stack: tumpukan card dengan navigasi.
 * Animasi: slide left/right.
 */
export default function TestimonialMode7({ section, data }) {
    const testimonials = data.testimonials || [];
    if (!testimonials.length) return null;
    const [idx, setIdx] = useState(0);
    const ease = [0.22, 1, 0.36, 1];
    const t = testimonials[idx] || testimonials[0];

    const prev = () => setIdx(i => (i - 1 + testimonials.length) % testimonials.length);
    const next = () => setIdx(i => (i + 1) % testimonials.length);

    return (
        <section className="testimonial testimonial--stack" id="testimoni">
            <div className="shell" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                <div style={{ textAlign: 'center', marginBottom: 32 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Apa Kata Mereka'}</h2>
                </div>
                <div className="testimonial-stack-wrap">
                    <AnimatePresence mode="wait">
                        <motion.div
                            key={idx}
                            className="testimonial-stack-card gen-card"
                            initial={{ opacity: 0, x: 40 }}
                            animate={{ opacity: 1, x: 0 }}
                            exit={{ opacity: 0, x: -40 }}
                            transition={{ duration: 0.4, ease }}
                        >
                            <div className="testimonial-stack-stars">
                                {[...Array(5)].map((_, i) => <Star key={i} size={16} fill="currentColor" />)}
                            </div>
                            <p className="testimonial-stack-text">"{t.text || t.content}"</p>
                            <div className="testimonial-stack-author">
                                {t.avatar && <img src={t.avatar} alt={t.name} />}
                                <div>
                                    <strong>{t.name}</strong>
                                    {t.role && <span>{t.role}</span>}
                                </div>
                            </div>
                        </motion.div>
                    </AnimatePresence>
                    <div className="testimonial-stack-nav">
                        <button onClick={prev} aria-label="Sebelumnya"><ChevronLeft size={20} /></button>
                        <span>{idx + 1} / {testimonials.length}</span>
                        <button onClick={next} aria-label="Berikutnya"><ChevronRight size={20} /></button>
                    </div>
                </div>
            </div>
        </section>
    );
}
