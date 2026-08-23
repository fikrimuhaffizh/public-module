import React from 'react';
import { motion } from 'framer-motion';

/**
 * FAQ Mode 7 — Grid cards: FAQ dalam card grid.
 * Animasi: stagger.
 */
export default function FaqMode7({ section, data }) {
    const faqs = data.faqs || [];
    if (!faqs.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="faq faq--grid" id="faq">
            <div className="shell">
                <div style={{ textAlign: 'center', marginBottom: 32 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'FAQ'}</h2>
                </div>
                <motion.div
                    className="faq-grid-list"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.08 } } }}
                >
                    {faqs.slice(0, limit).map((faq, i) => (
                        <motion.div
                            key={faq.id || i}
                            className="faq-grid-card gen-card"
                            variants={{ hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease } } }}
                        >
                            <h4 className="faq-grid-q">{faq.question || faq.title}</h4>
                            <p className="faq-grid-a">{faq.answer || faq.description}</p>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
