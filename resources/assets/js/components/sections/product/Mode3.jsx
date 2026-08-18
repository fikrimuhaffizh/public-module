import React from 'react';
import { ArrowUpRight } from 'lucide-react';
import { Section, combinedText } from '../index';

/**
 * Produk Mode 3 — kartu bernomor: media (foto/inisial) + angka watermark besar
 * + nama + deskripsi, grid dua kolom di desktop. Prop: { section, data }
 */
export default function ProductMode3({ section, data }) {
    const products = data.landing?.products || [];
    if (!products.length) return null;
    const limit = section?.limit_data || 6;

    return (
        <Section
            section={section}
            id="informasi"
            eyebrow={section.pre_title || 'Satu ekosistem'}
            title={section.title || 'Semua yang dibutuhkan sivitas akademika'}
            text={combinedText(section)}
        >
            <div className="product-num-grid">
                {products.slice(0, limit).map((product, index) => (
                    <article key={product.id} className="product-num-card gen-card">
                        <span className="product-num-watermark" aria-hidden="true">
                            {String(index + 1).padStart(2, '0')}
                        </span>
                        <span className="product-num-media">
                            {product.image
                                ? <img src={product.image} alt={product.name} loading="lazy" />
                                : <span>{product.name.slice(0, 1)}</span>}
                        </span>
                        <h3>{product.name}</h3>
                        <p>{product.shortDescription || product.description}</p>
                        {product.demoUrl && (
                            <a
                                className="text-link"
                                href={product.demoUrl}
                                target="_blank"
                                rel="noreferrer"
                            >
                                Coba demo <ArrowUpRight size={15} />
                            </a>
                        )}
                    </article>
                ))}
            </div>
        </Section>
    );
}
