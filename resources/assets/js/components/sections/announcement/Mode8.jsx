import React from 'react';
import { motion } from 'framer-motion';
import { Link } from '@inertiajs/react';

/**
 * Announcement Mode 8 — Minimal list: list bersih.
 * Animasi: stagger.
 */
export default function AnnouncementMode8({ section, data }) {
    const items = data.announcements || [];
    if (!items.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="news news--minimal" id="berita">
            <div className="shell" style={{ maxWidth: 720 }}>
                <div style={{ marginBottom: 32 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Berita & Pengumuman'}</h2>
                </div>
                <motion.div
                    className="news-minimal-list"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-30px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.08 } } }}
                >
                    {items.slice(0, limit).map((item, i) => (
                        <motion.div
                            key={item.id}
                            className="news-minimal-item"
                            variants={{ hidden: { opacity: 0, x: -16 }, visible: { opacity: 1, x: 0, transition: { duration: 0.4, ease } } }}
                        >
                            <span className="news-minimal-date">{item.date || ''}</span>
                            <Link href={item.url}>
                                <strong>{item.title}</strong>
                            </Link>
                            <p>{item.excerpt || item.description}</p>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
