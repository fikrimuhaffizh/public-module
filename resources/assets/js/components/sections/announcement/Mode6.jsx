import React from 'react';
import { motion } from 'framer-motion';
import { Link } from '@inertiajs/react';

/**
 * Announcement Mode 6 — Timeline: berita dalam timeline.
 * Animasi: stagger.
 */
export default function AnnouncementMode6({ section, data }) {
    const items = data.announcements || [];
    if (!items.length) return null;
    const limit = section?.limit_data || 5;
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="news news--timeline" id="berita">
            <div className="shell" style={{ maxWidth: 680 }}>
                <div style={{ textAlign: 'center', marginBottom: 36 }}>
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Berita & Pengumuman'}</h2>
                </div>
                <motion.div
                    className="news-timeline"
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: '-40px' }}
                    variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.12 } } }}
                >
                    {items.slice(0, limit).map((item, i) => (
                        <motion.div
                            key={item.id}
                            className="news-timeline-item"
                            variants={{ hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease } } }}
                        >
                            <div className="news-timeline-dot" />
                            <Link href={item.url} className="news-timeline-content">
                                <span className="news-timeline-date">{item.date || ''}</span>
                                <h4>{item.title}</h4>
                                <p>{item.excerpt || item.description}</p>
                            </Link>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
