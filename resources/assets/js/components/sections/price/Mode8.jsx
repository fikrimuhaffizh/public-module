import React from 'react';
import { motion } from 'framer-motion';
import { Check, Star } from 'lucide-react';
import { Section, pricePackages, combinedText } from '../index';

/**
 * Price Mode 8 — Glow card: paket center dengan glow.
 * Animasi: scale-in.
 */
export default function PriceMode8({ section, data }) {
    const packages = pricePackages(section, data?.landing?.pricing);
    if (!packages.length) return null;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <Section section={section} id="harga"
            eyebrow={section.pre_title || 'Paket & Harga'}
            title={section.title || 'Pilih paket yang sesuai'}
            text={combinedText(section)}
        >
            <div className="price-glow-grid">
                {packages.map((pkg, i) => (
                    <motion.div key={pkg.name}
                        className={`price-glow-card ${pkg.highlight ? 'price-glow-card--featured' : ''}`}
                        initial={{ opacity: 0, scale: 0.92 }}
                        whileInView={{ opacity: 1, scale: 1 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5, ease, delay: i * 0.1 }}
                    >
                        {pkg.highlight && <span className="price-glow-badge"><Star size={12} /> Populer</span>}
                        <h3>{pkg.name}</h3>
                        <p>{pkg.description}</p>
                        <div className="price-glow-amount">
                            <span>Rp</span><strong>{pkg.price}</strong>
                            {pkg.period && <small>/{pkg.period}</small>}
                        </div>
                        <ul className="price-card-features">
                            {(pkg.features || []).map((f, j) => <li key={j}><Check size={15} /> {f}</li>)}
                        </ul>
                        <a className="ui-btn ui-btn--gradient" href={pkg.ctaLink || '#kontak'}>{pkg.ctaText || 'Pilih paket'}</a>
                    </motion.div>
                ))}
            </div>
        </Section>
    );
}
