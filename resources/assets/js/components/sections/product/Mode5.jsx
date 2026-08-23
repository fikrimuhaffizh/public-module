import React from 'react';
import { motion } from 'framer-motion';
import { ArrowRight } from 'lucide-react';

/**
 * Product Mode 5 — Featured: 1 produk besar + grid kecil.
 * Animasi: stagger.
 */
export default function ProductMode5({ section, data }) {
    const products = data.landing?.products || [];
    if (!products.length) return null;
    const limit = section?.limit_data || 5;
    const items = products.slice(0, limit);
    const [featured, ...rest] = items;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="product product--featured" id="informasi">
            <div className="shell">
                <div style={{ textAlign: 'center', marginBottom: 36 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Produk Kami'}</h2>
                </div>
                <motion.div
                    className="product-featured-grid"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.1 } } }}
                >
                    {featured && (
                        <motion.article
                            className="product-featured-main gen-card"
                            variants={{ hidden: { opacity: 0, y: 20 }, visible: { opacity: 1, y: 0, transition: { duration: 0.5, ease } } }}
                        >
                            {featured.image && <img src={featured.image} alt={featured.name} className="product-featured-img" />}
                            <h3>{featured.name}</h3>
                            <p>{featured.shortDescription || featured.description}</p>
                            {featured.demoUrl && <a className="text-link" href={featured.demoUrl} target="_blank" rel="noreferrer">Lihat Demo <ArrowRight size={16} /></a>}
                        </motion.article>
                    )}
                    <div className="product-featured-list">
                        {rest.map(p => (
                            <motion.div
                                key={p.id}
                                className="product-featured-item"
                                variants={{ hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease } } }}
                            >
                                {p.image && <img src={p.image} alt={p.name} />}
                                <div>
                                    <strong>{p.name}</strong>
                                    <span>{p.shortDescription || p.description}</span>
                                </div>
                            </motion.div>
                        ))}
                    </div>
                </motion.div>
            </div>
        </section>
    );
}
