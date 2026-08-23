import React from 'react';
import { motion } from 'framer-motion';

/**
 * Client Mode 6 — Bento grid: logo asimetris.
 * Animasi: stagger.
 */
export default function ClientMode6({ section, data }) {
    const clients = data.landing?.clients || [];
    if (!clients.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="client-logos client-logos--bento" id="klien">
            <div className="shell">
                <div style={{ textAlign: 'center', marginBottom: 32 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Mitra Kami'}</h2>
                </div>
                <motion.div
                    className="client-bento-grid"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.08 } } }}
                >
                    {clients.slice(0, limit).map((c, i) => (
                        <motion.div
                            key={c.id || i}
                            className={`client-bento-cell gen-card ${i === 0 ? 'client-bento-cell--large' : ''}`}
                            variants={{ hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease } } }}
                        >
                            {c.logo
                                ? <img src={c.logo} alt={c.name} />
                                : <span className="client-bento-initial">{(c.name || '').slice(0, 2)}</span>
                            }
                            {c.name && <span className="client-bento-name">{c.name}</span>}
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
