import React from 'react';
import { motion } from 'framer-motion';
import { Link } from '@inertiajs/react';

/**
 * Announcement Mode 7 — Horizontal scroll: berita scroll horizontal.
 * Animasi: slide-in.
 */
export default function AnnouncementMode7({ section, data }) {
    const items = data.announcements || [];
    if (!items.length) return null;
    const limit = section?.limit_data || 6;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="news news--scroll" id="berita">
            <div className="shell">
                <div style={{ marginBottom: 24 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Berita & Pengumuman'}</h2>
                </div>
                <motion.div
                    className="news-scroll-track"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.1 } } }}
                >
                    {items.slice(0, limit).map((item) => (
                        <motion.div
                            key={item.id}
                            className="news-scroll-card"
                            variants={{ hidden: { opacity: 0, x: 20 }, visible: { opacity: 1, x: 0, transition: { duration: 0.5, ease } } }}
                        >
                            {item.image && <img src={item.image} alt={item.title} />}
                            <div className="news-scroll-body">
                                <span className="news-scroll-date">{item.date || ''}</span>
                                <h4>{item.title}</h4>
                                <Link href={item.url} className="text-link">Baca selengkapnya</Link>
                            </div>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
