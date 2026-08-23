import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

/**
 * Feature Mode 8 — Tabs: fitur ditampilkan dalam tab switching.
 * Animasi: fade content saat ganti tab.
 */
export default function FeatureMode8({ section, data }) {
    const features = data.landing?.features || [];
    if (!features.length) return null;
    const limit = section?.limit_data || 4;
    const items = features.slice(0, limit);
    const [active, setActive] = useState(0);
    const ease = [0.22, 1, 0.36, 1];
    const current = items[active] || items[0];

    return (
        <section className="feature feature--tabs" id="keunggulan">
            <div className="shell">
                <div className="feature-tabs-header" style={{ textAlign: 'center', marginBottom: 32 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Fitur Unggulan'}</h2>
                </div>
                <div className="feature-tabs-nav">
                    {items.map((f, i) => (
                        <button
                            key={f.id}
                            className={`feature-tabs-btn ${i === active ? 'active' : ''}`}
                            onClick={() => setActive(i)}
                        >
                            {f.title}
                        </button>
                    ))}
                </div>
                <AnimatePresence mode="wait">
                    <motion.div
                        key={active}
                        className="feature-tabs-content"
                        initial={{ opacity: 0, y: 12 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -12 }}
                        transition={{ duration: 0.35, ease }}
                    >
                        {current.image && (
                            <div className="feature-tabs-img">
                                <img src={current.image} alt={current.title} loading="lazy" />
                            </div>
                        )}
                        <div className="feature-tabs-text">
                            <h3>{current.title}</h3>
                            <p>{current.description}</p>
                        </div>
                    </motion.div>
                </AnimatePresence>
            </div>
        </section>
    );
}
