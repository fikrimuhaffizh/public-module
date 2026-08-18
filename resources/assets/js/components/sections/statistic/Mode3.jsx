import React from 'react';
import { SectionHeader } from '../index';
import CountUp from './CountUp';

/** Statistik Mode 3 — band angka lebar dengan pembatas vertikal. Prop: { section, data } */
export default function StatsMode3({ section, data }) {
    const stats = data.landing?.statistics || [];
    if (!stats.length) return null;
    const limit = section?.limit_data || 4;
    return (
        <section className="stats-band">
            <div className="shell">
                <SectionHeader section={section} />
                <div className="stats-band-row">
                    {stats.slice(0, limit).map(stat => (
                        <div key={stat.id} className="stats-band-cell">
                            <strong><CountUp value={stat.value} /></strong>
                            <span>{stat.label}</span>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
