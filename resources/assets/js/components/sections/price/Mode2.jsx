import React from 'react';
import { Check } from 'lucide-react';
import { Section, pricePackages, combinedText } from '../index';

/**
 * Harga Mode 2 — daftar baris horizontal: tiap paket satu baris penuh (nama,
 * fitur ringkas, harga di kanan). Cocok untuk layanan/jasa. Prop: { section, data }
 */
export default function PriceMode2({ section }) {
    const packages = pricePackages(section);
    if (!packages.length) return null;

    return (
        <Section
            section={section}
            id="harga"
            eyebrow={section.pre_title || 'Paket & Harga'}
            title={section.title || 'Pilih paket yang sesuai'}
            text={combinedText(section, 'Harga transparan, tanpa biaya tersembunyi.')}
        >
            <div className="price-rows">
                {packages.map(pkg => (
                    <article
                        key={pkg.name}
                        className={`price-row-card gen-card${pkg.highlight ? ' price-row-card--featured' : ''}`}
                    >
                        <div className="price-row-info">
                            <h3>{pkg.name}</h3>
                            <p>{pkg.description}</p>
                            {(pkg.features || []).length > 0 && (
                                <ul className="price-row-tags">
                                    {(pkg.features || []).slice(0, 3).map((feature, i) => (
                                        <li key={i}><Check size={12} /> {feature}</li>
                                    ))}
                                </ul>
                            )}
                        </div>
                        <div className="price-row-side">
                            <div className="price-row-amount">
                                <span className="price-card-currency">Rp</span>
                                <span className="price-card-value">{pkg.price}</span>
                                {pkg.period && <span className="price-card-period">/{pkg.period}</span>}
                            </div>
                            <a
                                className="price-card-cta"
                                href={pkg.ctaLink || '#kontak'}
                                {...(pkg.ctaLink && pkg.ctaLink.startsWith('http') ? { target: '_blank', rel: 'noreferrer' } : {})}
                            >
                                {pkg.ctaText || 'Pilih'}
                            </a>
                        </div>
                    </article>
                ))}
            </div>
        </Section>
    );
}
