import React from 'react';
import { motion } from 'framer-motion';

/**
 * Client Mode 8 — Minimal: logo kecil center.
 * Animasi: fade.
 */
export default function ClientMode8({ section, data }) {
    const clients = data.landing?.clients || [];
    if (!clients.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="client-logos client-logos--minimal" id="klien">
            <div className="shell" style={{ textAlign: 'center' }}>
                {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                <h2 style={{ color: 'var(--sec-title, inherit)', marginBottom: 32 }}>{section.title || 'Mitra Kami'}</h2>
                <motion.div
                    className="client-minimal-grid"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.06 } } }}
                >
                    {clients.slice(0, limit).map((c, i) => (
                        <motion.div
                            key={c.id || i}
                            className="client-minimal-item"
                            variants={{ hidden: { opacity: 0 }, visible: { opacity: 1, transition: { duration: 0.4, ease } } }}
                        >
                            {c.logo
                                ? <img src={c.logo} alt={c.name || ''} />
                                : <span>{(c.name || '').slice(0, 2)}</span>
                            }
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
