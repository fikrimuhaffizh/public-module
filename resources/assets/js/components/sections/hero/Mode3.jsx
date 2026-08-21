import React from 'react';
import { Heart, Sparkles, Star } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Reveal } from '@public/components/motion/effects';
import HeroActions from './HeroActions';
import { heroCopy } from '../index';

/**
 * Hero Mode 3 — dua kolom UMKM: teks kiri, galeri foto produk kanan.
 *
 * Galeri mengambil foto dari produk aktif (maks 4). Kalau belum ada foto,
 * fallback ke hero.image lalu ke tile dekoratif (inisial nama produk) —
 * layout tetap utuh tanpa foto, cocok untuk tenant baru.
 * Prop: { section, data }
 */
export default function HeroMode3({ section, data }) {
    const hero = data.landing?.hero;
    const copy = heroCopy(section, hero, data.site);
    const products = (data.landing?.products || []).slice(0, 4);

    // Susun 4 sel galeri: foto produk → hero.image → tile inisial produk.
    const cells = products.map((p) => ({ src: p.image || '', label: p.name || '' }));
    if (!cells.length && hero?.image) cells.push({ src: hero.image, label: '' });
    while (cells.length < 4) cells.push({ src: '', label: '' });
    const [main, ...rest] = cells;

    const stats = data.landing?.statistics || [];
    const stat = stats[0];
    const chipText = stat && (stat.value || stat.label)
        ? `${stat.value} ${stat.label || ''}`.trim()
        : 'Terpercaya UMKM lokal';

    const renderCell = (cell, extraClass = '') => (
        cell.src
            ? <img src={cell.src} alt={cell.label || copy.imageAlt} loading="lazy" />
            : (
                <div className={`hero-gallery-tile ${extraClass}`}>
                    {cell.label
                        ? <><span>{cell.label.slice(0, 1)}</span><small>{cell.label}</small></>
                        : <Heart size={30} />}
                </div>
            )
    );

    return (
        <section className="hero hero--gallery">
            <div className="shell hero-gallery-grid">
                <Reveal className="hero-gallery-copy">
                    <Badge style={{ color: 'var(--sec-pretext, inherit)' }}><Sparkles size={14} /> {section.pre_title || 'Usaha lokal, kualitas utama'}</Badge>
                    <h1 style={{ color: 'var(--sec-title, inherit)' }}>{copy.title}</h1>
                    <p style={{ color: 'var(--sec-posttext, inherit)' }}>{copy.subtitle}</p>
                    <HeroActions hero={hero} site={data.site} align="left" />
                </Reveal>
                <Reveal className="hero-gallery" delay={0.15}>
                    <div className="hero-gallery-main">
                        {renderCell(main, 'hero-gallery-tile--lg')}
                        <span className="hero-gallery-chip">
                            <Star size={13} fill="currentColor" /> {chipText}
                        </span>
                    </div>
                    <div className="hero-gallery-thumbs">
                        {rest.map((cell, i) => (
                            <div key={i} className="hero-gallery-thumb">
                                {renderCell(cell)}
                            </div>
                        ))}
                    </div>
                </Reveal>
            </div>
        </section>
    );
}
