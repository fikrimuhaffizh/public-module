import React from 'react';
import { motion } from 'framer-motion';
import { Check } from 'lucide-react';
import { Section, pricePackages, combinedText } from '../index';

/**
 * Price Mode 3 — Horizontal: paket dalam baris.
 * Animasi: stagger.
 */
export default function PriceMode3({ section, data }) {
    const packages = pricePackages(section, data?.landing?.pricing);
    if (!packages.length) return null;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <Section section={section} id="harga"
            eyebrow={section.pre_title || 'Paket & Harga'}
            title={section.title || 'Pilih paket yang sesuai'}
            text={combinedText(section, 'Harga transparan, tanpa biaya tersembunyi.')}
        >
            <motion.div className="price-horizontal-list"
                initial="hidden" whileInView="visible" viewport={{ once: true, margin: '-40px' }}
                variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.12 } } }}
            >
                {packages.map(pkg => (
                    <motion.div key={pkg.name} className={`price-horizontal-item ${pkg.highlight ? 'price-horizontal-item--featured' : ''}`}
                        variants={{ hidden: { opacity: 0, y: 20 }, visible: { opacity: 1, y: 0, transition: { duration: 0.5, ease } } }}
                    >
                        <div className="price-horizontal-header">
                            <h3>{pkg.name}</h3>
                            <div className="price-horizontal-amount">
                                <span className="price-card-currency">Rp</span>
                                <span className="price-card-value">{pkg.price}</span>
                                {pkg.period && <span className="price-card-period">/{pkg.period}</span>}
                            </div>
                        </div>
                        <p className="price-horizontal-desc">{pkg.description}</p>
                        <ul className="price-card-features">
                            {(pkg.features || []).map((f, i) => <li key={i}><Check size={15} /> {f}</li>)}
                        </ul>
                        <a className="price-card-cta" href={pkg.ctaLink || '#kontak'}>{pkg.ctaText || 'Pilih paket'}</a>
                    </motion.div>
                ))}
            </motion.div>
        </Section>
    );
}
