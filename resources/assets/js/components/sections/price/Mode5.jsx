import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Check, Star } from 'lucide-react';
import { Section, pricePackages, combinedText } from '../index';

/**
 * Price Mode 5 — Toggle monthly/yearly.
 * Animasi: fade saat ganti period.
 */
export default function PriceMode5({ section, data }) {
    const packages = pricePackages(section, data?.landing?.pricing);
    if (!packages.length) return null;
    const [annual, setAnnual] = useState(false);
    const ease = [0.22, 1, 0.36, 1];

    return (
        <Section section={section} id="harga"
            eyebrow={section.pre_title || 'Paket & Harga'}
            title={section.title || 'Pilih paket yang sesuai'}
            text={combinedText(section)}
        >
            <div className="price-toggle-wrap">
                <button className={`price-toggle-btn ${!annual ? 'active' : ''}`} onClick={() => setAnnual(false)}>Bulanan</button>
                <button className={`price-toggle-btn ${annual ? 'active' : ''}`} onClick={() => setAnnual(true)}>Tahunan</button>
            </div>
            <AnimatePresence mode="wait">
                <motion.div key={annual ? 'y' : 'm'} className="price-grid"
                    initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -12 }}
                    transition={{ duration: 0.35, ease }}
                >
                    {packages.map(pkg => (
                        <article key={pkg.name} className={`price-card gen-card ${pkg.highlight ? 'price-card--featured' : ''}`}>
                            {pkg.highlight && <span className="price-card-badge"><Star size={12} /> Paling Laris</span>}
                            <h3 className="price-card-name">{pkg.name}</h3>
                            <p className="price-card-desc">{pkg.description}</p>
                            <div className="price-card-amount">
                                <span className="price-card-currency">Rp</span>
                                <span className="price-card-value">{pkg.price}</span>
                                {pkg.period && <span className="price-card-period">/{pkg.period}</span>}
                            </div>
                            <ul className="price-card-features">
                                {(pkg.features || []).map((f, i) => <li key={i}><Check size={15} /> {f}</li>)}
                            </ul>
                            <a className="price-card-cta" href={pkg.ctaLink || '#kontak'}>{pkg.ctaText || 'Pilih paket'}</a>
                        </article>
                    ))}
                </motion.div>
            </AnimatePresence>
        </Section>
    );
}
