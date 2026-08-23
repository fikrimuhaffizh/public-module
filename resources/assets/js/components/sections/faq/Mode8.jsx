import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

/**
 * FAQ Mode 8 — Tabs: FAQ dalam tab switching.
 * Animasi: fade content saat ganti tab.
 */
export default function FaqMode8({ section, data }) {
    const faqs = data.faqs || [];
    if (!faqs.length) return null;
    const limit = section?.limit_data || 4;
    const items = faqs.slice(0, limit);
    const [active, setActive] = useState(0);
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="faq faq--tabs" id="faq">
            <div className="shell">
                <div style={{ textAlign: 'center', marginBottom: 32 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'FAQ'}</h2>
                </div>
                <div className="faq-tabs-nav">
                    {items.map((f, i) => (
                        <button key={f.id || i} className={`faq-tabs-btn ${i === active ? 'active' : ''}`} onClick={() => setActive(i)}>
                            {f.question || f.title}
                        </button>
                    ))}
                </div>
                <AnimatePresence mode="wait">
                    <motion.div
                        key={active}
                        className="faq-tabs-content gen-card"
                        initial={{ opacity: 0, y: 12 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -12 }}
                        transition={{ duration: 0.35, ease }}
                    >
                        <h3>{items[active]?.question || items[active]?.title}</h3>
                        <p>{items[active]?.answer || items[active]?.description}</p>
                    </motion.div>
                </AnimatePresence>
            </div>
        </section>
    );
}
