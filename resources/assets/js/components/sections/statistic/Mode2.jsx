import React from 'react';
import { SectionHeader } from '../index';
import CountUp from './CountUp';

/** Statistik Mode 2 — kartu dengan ikon, angka, dan label. Prop: { section, data } */
export default function StatsMode2({ section, data }) {
    const stats = data.landing?.statistics || [];
    if (!stats.length) return null;
    const limit = section?.limit_data || 4;
    return (
        <section className="section section--tint">
            <div className="shell">
                <SectionHeader section={section} />
                <div className="stats-cards">
                    {stats.slice(0, limit).map(stat => (
                        <div key={stat.id} className="stats-card">
                            {stat.icon && <span className={`stats-card-icon ${stat.icon}`} aria-hidden="true" />}
                            <strong><CountUp value={stat.value} /></strong>
                            <span>{stat.label}</span>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
