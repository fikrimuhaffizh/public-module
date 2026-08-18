import React from 'react';
import { ArrowRight } from 'lucide-react';
import { Section, combinedText } from '../index';

/**
 * Produk Mode 2 — baris kartu: thumbnail media (foto/inisial) + nama + deskripsi,
 * panah di kanan, seluruh baris bisa diklik bila ada demo URL.
 * Prop: { section, data }
 */
export default function ProductMode2({ section, data }) {
    const products = data.landing?.products || [];
    if (!products.length) return null;
    const limit = section?.limit_data || 5;

    return (
        <Section
            section={section}
            id="informasi"
            eyebrow={section.pre_title || 'Satu ekosistem'}
            title={section.title || 'Semua yang dibutuhkan sivitas akademika'}
            text={combinedText(section)}
        >
            <div className="product-rows">
                {products.slice(0, limit).map((product) => {
                    const Wrapper = product.demoUrl ? 'a' : 'div';
                    return (
                        <Wrapper
                            key={product.id}
                            className="product-row"
                            href={product.demoUrl || undefined}
                            target={product.demoUrl ? '_blank' : undefined}
                            rel={product.demoUrl ? 'noreferrer' : undefined}
                        >
                            <span className="product-row-media">
                                {product.image
                                    ? <img src={product.image} alt={product.name} loading="lazy" />
                                    : <span>{product.name.slice(0, 1)}</span>}
                            </span>
                            <span className="product-row-body">
                                <span className="product-row-name">{product.name}</span>
                                <span className="product-row-desc">
                                    {product.shortDescription || product.description}
                                </span>
                            </span>
                            {product.demoUrl && (
                                <span className="product-row-arrow" aria-hidden="true">
                                    <ArrowRight size={18} />
                                </span>
                            )}
                        </Wrapper>
                    );
                })}
            </div>
        </Section>
    );
}
