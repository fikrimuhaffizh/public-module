import React from 'react';
import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { Section, combinedText } from '../index';
import { Stagger, SpotlightCard } from '@public/components/motion/effects';

/**
 * Produk Mode 1 — card grid dengan gambar cover.
 * Setiap card menampilkan gambar, nama, deskripsi singkat, dan link demo.
 * Prop: { section, data }
 */
export default function ProductMode1({ section, data }) {
    const products = data.landing?.products || [];
    if (!products.length) return null;
    const limit = section?.limit_data || 6;

    return (
        <Section
            section={section}
            id="informasi"
            eyebrow={section.pre_title || 'Satu ekosistem'}
            title={section.title || 'Semua yang dibutuhkan sivitas akademika'}
            text={combinedText(section, 'Pengalaman digital yang sederhana di depan, dengan pengelolaan konten yang terstruktur di belakang.')}
        >
            <Stagger className="feature-grid">
                {products.slice(0, limit).map((product) => (
                    <SpotlightCard key={product.id} className="feature-card product-card-cover">
                        {product.image && (
                            <div className="product-card-cover-img">
                                <img src={product.image} alt={product.name} loading="lazy" />
                            </div>
                        )}
                        <h3>{product.name}</h3>
                        <p>{product.shortDescription || product.description}</p>
                        {product.demoUrl && (
                            <Link className="text-link" href={product.demoUrl} target="_blank" rel="noreferrer">
                                Lihat Demo <ArrowRight size={16} />
                            </Link>
                        )}
                    </SpotlightCard>
                ))}
            </Stagger>
        </Section>
    );
}
