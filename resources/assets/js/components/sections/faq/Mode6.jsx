import React from 'react';
import { motion } from 'framer-motion';

/**
 * FAQ Mode 6 — Accordion center: FAQ center, lebar penuh.
 */
export default function FaqMode6({ section, data }) {
    const faqs = data.faqs || [];
    if (!faqs.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="faq faq--center" id="faq">
            <div className="shell" style={{ maxWidth: 720 }}>
                <div style={{ textAlign: 'center', marginBottom: 32 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'FAQ'}</h2>
                </div>
                <div className="faq-center-list">
                    {faqs.slice(0, limit).map((faq, i) => (
                        <motion.div
                            key={faq.id || i}
                            className="faq-center-item"
                            initial={{ opacity: 0, y: 12 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, margin: '-20px' }}
                            transition={{ duration: 0.4, ease, delay: i * 0.06 }}
                        >
                            <details>
                                <summary>{faq.question || faq.title}</summary>
                                <div className="faq-center-answer">{faq.answer || faq.description}</div>
                            </details>
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
}
