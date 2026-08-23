import React from 'react';
import { motion } from 'framer-motion';
import { Quote } from 'lucide-react';

/**
 * Testimonial Mode 6 — Masonry grid: card berbagai ukuran.
 * Animasi: stagger.
 */
export default function TestimonialMode6({ section, data }) {
    const testimonials = data.testimonials || [];
    if (!testimonials.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="testimonial testimonial--masonry" id="testimoni">
            <div className="shell">
                <div style={{ textAlign: 'center', marginBottom: 36 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Apa Kata Mereka'}</h2>
                </div>
                <motion.div
                    className="testimonial-masonry-grid"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-60px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.1 } } }}
                >
                    {testimonials.slice(0, limit).map((t, i) => (
                        <motion.div
                            key={t.id}
                            className={`testimonial-masonry-card gen-card ${i % 3 === 0 ? 'testimonial-masonry-card--tall' : ''}`}
                            variants={{ hidden: { opacity: 0, y: 20 }, visible: { opacity: 1, y: 0, transition: { duration: 0.5, ease } } }}
                        >
                            <Quote size={20} style={{ color: 'var(--primary)', opacity: .3, marginBottom: 8 }} />
                            <p className="testimonial-masonry-text">{t.text || t.content}</p>
                            <div className="testimonial-masonry-author">
                                {t.avatar && <img src={t.avatar} alt={t.name} className="testimonial-masonry-avatar" />}
                                <div>
                                    <strong>{t.name}</strong>
                                    {t.role && <span>{t.role}</span>}
                                </div>
                            </div>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
