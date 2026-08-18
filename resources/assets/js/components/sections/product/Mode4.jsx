import React, { useRef, useState } from 'react';
import { ArrowRight, ChevronLeft, ChevronRight } from 'lucide-react';
import { Section, combinedText } from '../index';

/** Produk Mode 4 — carousel geser dengan scroll-snap + panah. Prop: { section, data } */
export default function ProductMode4({ section, data }) {
    const products = data.landing?.products || [];
    if (!products.length) return null;

    const limit = section?.limit_data || 5;
    const items = products.slice(0, limit);
    const trackRef = useRef(null);
    const [canPrev, setCanPrev] = useState(false);
    const [canNext, setCanNext] = useState(true);

    const updateArrows = () => {
        const track = trackRef.current;
        if (!track) return;
        setCanPrev(track.scrollLeft > 4);
        setCanNext(track.scrollLeft < track.scrollWidth - track.clientWidth - 4);
    };

    const scrollByCard = dir => {
        const track = trackRef.current;
        if (!track) return;
        const card = track.querySelector('.product-snap-card');
        const step = card ? card.offsetWidth + 22 : 320;
        track.scrollBy({ left: dir * step, behavior: 'smooth' });
    };

    return (
        <Section
            section={section}
            id="informasi"
            eyebrow={section.pre_title || 'Satu ekosistem'}
            title={section.title || 'Semua yang dibutuhkan sivitas akademika'}
            text={combinedText(section)}
        >
            <div className="product-snap-wrap">
                <div
                    className="product-snap"
                    ref={trackRef}
                    onScroll={updateArrows}
                    tabIndex="0"
                    aria-label="Daftar produk — geser untuk melihat"
                >
                    {items.map(product => (
                        <article key={product.id} className="product-snap-card">
                            {product.image && <img src={product.image} alt={product.name} loading="lazy" />}
                            <div className="product-snap-card__body">
                                <h3>{product.name}</h3>
                                <p>{product.shortDescription || product.description}</p>
                                {product.demoUrl && (
                                    <a className="text-link" href={product.demoUrl} target="_blank" rel="noreferrer">
                                        Coba demo <ArrowRight size={16} />
                                    </a>
                                )}
                            </div>
                        </article>
                    ))}
                </div>
                {items.length > 1 && (
                    <div className="product-snap-nav">
                        <button
                            type="button"
                            className="product-snap-arrow"
                            onClick={() => scrollByCard(-1)}
                            disabled={!canPrev}
                            aria-label="Produk sebelumnya"
                        >
                            <ChevronLeft size={20} />
                        </button>
                        <button
                            type="button"
                            className="product-snap-arrow"
                            onClick={() => scrollByCard(1)}
                            disabled={!canNext}
                            aria-label="Produk berikutnya"
                        >
                            <ChevronRight size={20} />
                        </button>
                    </div>
                )}
            </div>
        </Section>
    );
}
