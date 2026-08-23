import React from 'react';
import { motion } from 'framer-motion';
import { Check } from 'lucide-react';
import { Section, pricePackages, combinedText } from '../index';

/**
 * Price Mode 6 — Minimal center: paket center.
 * Animasi: fade-up.
 */
export default function PriceMode6({ section, data }) {
    const packages = pricePackages(section, data?.landing?.pricing);
    if (!packages.length) return null;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <Section section={section} id="harga"
            eyebrow={section.pre_title || 'Paket & Harga'}
            title={section.title || 'Pilih paket yang sesuai'}
            text={combinedText(section)}
        >
            <div className="price-minimal-grid">
                {packages.map((pkg, i) => (
                    <motion.div key={pkg.name}
                        className={`price-minimal-card ${pkg.highlight ? 'price-minimal-card--featured' : ''}`}
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5, ease, delay: i * 0.1 }}
                    >
                        <h3>{pkg.name}</h3>
                        <div className="price-minimal-amount">
                            <span>Rp</span><strong>{pkg.price}</strong>
                            {pkg.period && <small>/{pkg.period}</small>}
                        </div>
                        <p>{pkg.description}</p>
                        <ul>
                            {(pkg.features || []).map((f, j) => <li key={j}><Check size={14} /> {f}</li>)}
                        </ul>
                        <a className="ui-btn ui-btn--primary" href={pkg.ctaLink || '#kontak'}>{pkg.ctaText || 'Pilih'}</a>
                    </motion.div>
                ))}
            </div>
        </Section>
    );
}
