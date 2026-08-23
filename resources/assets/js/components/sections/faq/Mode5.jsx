import React from 'react';
import { motion } from 'framer-motion';
import { ChevronDown } from 'lucide-react';

/**
 * FAQ Mode 5 — Two column: FAQ kiri, info kanan.
 */
export default function FaqMode5({ section, data }) {
    const faqs = data.faqs || [];
    if (!faqs.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="faq faq--two-col" id="faq">
            <div className="shell faq-two-col-grid">
                <motion.div
                    className="faq-two-col-list"
                    initial={{ opacity: 0, x: -20 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, ease }}
                >
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'FAQ'}</h2>
                    {faqs.slice(0, limit).map((faq, i) => (
                        <details key={faq.id || i} className="faq-two-col-item">
                            <summary>{faq.question || faq.title}</summary>
                            <div className="faq-two-col-answer">{faq.answer || faq.description}</div>
                        </details>
                    ))}
                </motion.div>
                <motion.div
                    className="faq-two-col-info gen-card"
                    initial={{ opacity: 0, x: 20 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, ease, delay: 0.15 }}
                >
                    <h3>Masih ada pertanyaan?</h3>
                    <p>Hubungi kami dan kami akan membantu Anda.</p>
                </motion.div>
            </div>
        </section>
    );
}
