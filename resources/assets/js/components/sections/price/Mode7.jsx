import React from 'react';
import { motion } from 'framer-motion';
import { Check } from 'lucide-react';
import { Section, pricePackages, combinedText } from '../index';

/**
 * Price Mode 7 — Split: paket kiri, info kanan.
 * Animasi: slide-in.
 */
export default function PriceMode7({ section, data }) {
    const packages = pricePackages(section, data?.landing?.pricing);
    if (!packages.length) return null;
    const ease = [0.22, 1, 0.36, 1];
    const featured = packages.find(p => p.highlight) || packages[0];

    return (
        <Section section={section} id="harga"
            eyebrow={section.pre_title || 'Paket & Harga'}
            title={section.title || 'Pilih paket yang sesuai'}
            text={combinedText(section)}
        >
            <div className="price-split-grid">
                <motion.div className="price-split-list"
                    initial={{ opacity: 0, x: -20 }} whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }} transition={{ duration: 0.5, ease }}
                >
                    {packages.map(pkg => (
                        <div key={pkg.name} className={`price-split-item ${pkg.highlight ? 'price-split-item--featured' : ''}`}>
                            <div className="price-split-header">
                                <h3>{pkg.name}</h3>
                                <span>Rp {pkg.price}/{pkg.period || 'bln'}</span>
                            </div>
                            <p>{pkg.description}</p>
                        </div>
                    ))}
                </motion.div>
                <motion.div className="price-split-detail gen-card"
                    initial={{ opacity: 0, x: 20 }} whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }} transition={{ duration: 0.5, ease, delay: 0.15 }}
                >
                    <h3>{featured.name}</h3>
                    <p>{featured.description}</p>
                    <ul className="price-card-features">
                        {(featured.features || []).map((f, i) => <li key={i}><Check size={15} /> {f}</li>)}
                    </ul>
                    <a className="ui-btn ui-btn--primary" href={featured.ctaLink || '#kontak'}>{featured.ctaText || 'Pilih paket'}</a>
                </motion.div>
            </div>
        </Section>
    );
}
