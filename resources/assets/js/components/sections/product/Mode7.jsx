import React from 'react';
import { motion } from 'framer-motion';
import { ArrowRight } from 'lucide-react';

/**
 * Product Mode 7 — Overlay: gambar + overlay teks.
 * Animasi: fade-up.
 */
export default function ProductMode7({ section, data }) {
    const products = data.landing?.products || [];
    if (!products.length) return null;
    const limit = section?.limit_data || 4;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="product product--overlay" id="informasi">
            <div className="shell">
                <div style={{ textAlign: 'center', marginBottom: 36 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Produk Kami'}</h2>
                </div>
                <div className="product-overlay-grid">
                    {products.slice(0, limit).map((p, i) => (
                        <motion.div
                            key={p.id}
                            className="product-overlay-card"
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, margin: '-30px' }}
                            transition={{ duration: 0.5, ease, delay: i * 0.08 }}
                        >
                            {p.image && <img src={p.image} alt={p.name} />}
                            <div className="product-overlay-content">
                                <h3>{p.name}</h3>
                                <p>{p.shortDescription || p.description}</p>
                                {p.demoUrl && <a href={p.demoUrl} target="_blank" rel="noreferrer">Lihat <ArrowRight size={14} /></a>}
                            </div>
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
}
