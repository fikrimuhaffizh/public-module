import React from 'react';
import { motion } from 'framer-motion';

/**
 * Product Mode 6 — Bento: grid asimetris.
 * Animasi: stagger.
 */
export default function ProductMode6({ section, data }) {
    const products = data.landing?.products || [];
    if (!products.length) return null;
    const limit = section?.limit_data || 4;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="product product--bento" id="informasi">
            <div className="shell">
                <div style={{ textAlign: 'center', marginBottom: 36 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Produk Kami'}</h2>
                </div>
                <motion.div
                    className="product-bento-grid"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.1 } } }}
                >
                    {products.slice(0, limit).map((p, i) => (
                        <motion.article
                            key={p.id}
                            className={`product-bento-card gen-card ${i === 0 ? 'product-bento-card--large' : ''}`}
                            variants={{ hidden: { opacity: 0, y: 20 }, visible: { opacity: 1, y: 0, transition: { duration: 0.5, ease } } }}
                        >
                            {p.image && <img src={p.image} alt={p.name} />}
                            <h3>{p.name}</h3>
                            <p>{p.shortDescription || p.description}</p>
                        </motion.article>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
