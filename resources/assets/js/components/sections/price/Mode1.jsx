import React from 'react';
import { Check, Star } from 'lucide-react';
import { Section, pricePackages, combinedText } from '../index';

/**
 * Harga Mode 1 — kartu 3 kolom klasik: tiap paket satu kartu, paket unggulan
 * ditandai dan lebih menonjol. Prop: { section, data }
 * Data dari section.settings.packages (array JSON), fallback demo.
 */
export default function PriceMode1({ section, data }) {
    const packages = pricePackages(section, data?.landing?.pricing);
    if (!packages.length) return null;

    return (
        <Section
            section={section}
            id="harga"
            eyebrow={section.pre_title || 'Paket & Harga'}
            title={section.title || 'Pilih paket yang sesuai'}
            text={combinedText(section, 'Harga transparan, tanpa biaya tersembunyi. Tingkatkan kapan saja.')}
        >
            <div className="price-grid">
                {packages.map(pkg => (
                    <article
                        key={pkg.name}
                        className={`price-card gen-card${pkg.highlight ? ' price-card--featured' : ''}`}
                    >
                        {pkg.highlight && (
                            <span className="price-card-badge"><Star size={12} /> Paling Laris</span>
                        )}
                        <h3 className="price-card-name">{pkg.name}</h3>
                        <p className="price-card-desc">{pkg.description}</p>
                        <div className="price-card-amount">
                            <span className="price-card-currency">Rp</span>
                            <span className="price-card-value">{pkg.price}</span>
                            {pkg.period && <span className="price-card-period">/{pkg.period}</span>}
                        </div>
                        <ul className="price-card-features">
                            {(pkg.features || []).map((feature, i) => (
                                <li key={i}><Check size={15} /> {feature}</li>
                            ))}
                        </ul>
                        <a
                            className="price-card-cta"
                            href={pkg.ctaLink || '#kontak'}
                            {...(pkg.ctaLink && pkg.ctaLink.startsWith('http') ? { target: '_blank', rel: 'noreferrer' } : {})}
                        >
                            {pkg.ctaText || 'Pilih paket'}
                        </a>
                    </article>
                ))}
            </div>
        </Section>
    );
}
