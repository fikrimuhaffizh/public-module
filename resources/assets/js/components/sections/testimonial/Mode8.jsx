import React from 'react';
import { motion } from 'framer-motion';
import { Quote } from 'lucide-react';

/**
 * Testimonial Mode 8 — Minimal quote: quote besar center + foto.
 * Animasi: fade-up.
 */
export default function TestimonialMode8({ section, data }) {
    const testimonials = data.testimonials || [];
    if (!testimonials.length) return null;
    const t = testimonials[0];
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="testimonial testimonial--minimal" id="testimoni">
            <div className="shell" style={{ textAlign: 'center', maxWidth: 720 }}>
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, margin: '-60px' }}
                    transition={{ duration: 0.6, ease }}
                >
                    <Quote size={36} style={{ color: 'var(--primary)', opacity: .25, margin: '0 auto 16px' }} />
                    <blockquote className="testimonial-minimal-quote">
                        "{t.text || t.content}"
                    </blockquote>
                    <div className="testimonial-minimal-author">
                        {t.avatar && <img src={t.avatar} alt={t.name} className="testimonial-minimal-avatar" />}
                        <div>
                            <strong>{t.name}</strong>
                            {t.role && <span>{t.role}</span>}
                        </div>
                    </div>
                </motion.div>
            </div>
        </section>
    );
}
