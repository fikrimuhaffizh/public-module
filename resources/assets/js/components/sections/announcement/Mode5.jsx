import React from 'react';
import { motion } from 'framer-motion';
import { Link } from '@inertiajs/react';

/**
 * Announcement Mode 5 — Featured: 1 berita besar + sidebar.
 * Animasi: fade.
 */
export default function AnnouncementMode5({ section, data }) {
    const items = data.announcements || [];
    if (!items.length) return null;
    const limit = section?.limit_data || 5;
    const [featured, ...rest] = items.slice(0, limit);
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="news news--featured" id="berita">
            <div className="shell news-featured-grid">
                <motion.div
                    className="news-featured-main"
                    initial={{ opacity: 0, x: -20 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, ease }}
                >
                    {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Berita & Pengumuman'}</h2>
                    {featured && (
                        <Link href={featured.url} className="news-featured-card">
                            {featured.image && <img src={featured.image} alt={featured.title} />}
                            <h3>{featured.title}</h3>
                            <p>{featured.excerpt || featured.description}</p>
                        </Link>
                    )}
                </motion.div>
                <motion.div
                    className="news-featured-sidebar"
                    initial={{ opacity: 0, x: 20 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, ease, delay: 0.15 }}
                >
                    {rest.map(item => (
                        <Link key={item.id} href={item.url} className="news-featured-list-item">
                            {item.image && <img src={item.image} alt={item.title} />}
                            <div>
                                <strong>{item.title}</strong>
                                <span>{item.date || ''}</span>
                            </div>
                        </Link>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
