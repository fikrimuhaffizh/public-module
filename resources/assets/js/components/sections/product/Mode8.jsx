import React from 'react';
import { motion } from 'framer-motion';
import { ArrowRight } from 'lucide-react';

/**
 * Product Mode 8 — Minimal list: list bersih.
 * Animasi: stagger.
 */
export default function ProductMode8({ section, data }) {
    const products = data.landing?.products || [];
    if (!products.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="product product--minimal" id="informasi">
            <div className="shell" style={{ maxWidth: 720 }}>
                <div style={{ marginBottom: 32 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Produk Kami'}</h2>
                </div>
                <motion.div
                    className="product-minimal-list"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-30px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.08 } } }}
                >
                    {products.slice(0, limit).map((p, i) => (
                        <motion.div
                            key={p.id}
                            className="product-minimal-item"
                            variants={{ hidden: { opacity: 0, x: -16 }, visible: { opacity: 1, x: 0, transition: { duration: 0.4, ease } } }}
                        >
                            <div className="product-minimal-num">{String(i + 1).padStart(2, '0')}</div>
                            <div className="product-minimal-body">
                                <strong>{p.name}</strong>
                                <span>{p.shortDescription || p.description}</span>
                            </div>
                            {p.demoUrl && <a href={p.demoUrl} target="_blank" rel="noreferrer"><ArrowRight size={16} /></a>}
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
