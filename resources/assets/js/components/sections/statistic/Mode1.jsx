import React from 'react';
import { SectionHeader } from '../index';
import CountUp from './CountUp';

/** Statistik Mode 1 — grid counter sederhana (angka + label). Prop: { section, data } */
export default function StatsMode1({ section, data }) {
    const stats = data.landing?.statistics || [];
    if (!stats.length) return null;
    const limit = section?.limit_data || 4;
    return (
        <section className="section section--tint">
            <div className="shell">
                <SectionHeader section={section} />
                <div className="stats-grid">
                    {stats.slice(0, limit).map(stat => (
                        <div key={stat.id} className="stats-cell">
                            <strong><CountUp value={stat.value} /></strong>
                            <span>{stat.label}</span>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
