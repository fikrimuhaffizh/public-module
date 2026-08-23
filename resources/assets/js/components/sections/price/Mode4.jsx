import React from 'react';
import { Check, X } from 'lucide-react';
import { Section, pricePackages, combinedText } from '../index';

/**
 * Price Mode 4 — Comparison table: tabel perbandingan.
 */
export default function PriceMode4({ section, data }) {
    const packages = pricePackages(section, data?.landing?.pricing);
    if (!packages.length) return null;

    // Collect all unique features
    const allFeatures = [...new Set(packages.flatMap(p => p.features || []))];

    return (
        <Section section={section} id="harga"
            eyebrow={section.pre_title || 'Paket & Harga'}
            title={section.title || 'Perbandingan Paket'}
            text={combinedText(section)}
        >
            <div className="price-table-wrap">
                <table className="price-table">
                    <thead>
                        <tr>
                            <th>Fitur</th>
                            {packages.map(pkg => (
                                <th key={pkg.name} className={pkg.highlight ? 'price-table--featured' : ''}>
                                    {pkg.name}
                                    <div className="price-table-price">Rp {pkg.price}/{pkg.period || 'bln'}</div>
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {allFeatures.map((f, i) => (
                            <tr key={i}>
                                <td>{f}</td>
                                {packages.map(pkg => (
                                    <td key={pkg.name} className={pkg.highlight ? 'price-table--featured' : ''}>
                                        {(pkg.features || []).includes(f) ? <Check size={16} style={{ color: 'var(--primary)' }} /> : <X size={16} style={{ opacity: .3 }} />}
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </Section>
    );
}
