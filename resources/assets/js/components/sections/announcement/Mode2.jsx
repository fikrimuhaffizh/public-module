import React from 'react';
import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Section, combinedText } from '../index';

/** Pengumuman Mode 2 — baris daftar: gambar di kiri, info di kanan. Prop: { section, data } */
export default function AnnouncementMode2({ section, data }) {
    const list = data.announcements || [];
    if (!list.length) return null;
    const limit = section?.limit_data || 5;
    return (
        <Section
            section={section}
            id="berita"
            tint
            eyebrow={section.pre_title || 'Tetap terhubung'}
            title={section.title || 'Kabar terbaru kampus'}
            text={combinedText(section)}
        >
            <div className="announcement-list">
                {list.slice(0, limit).map(item => (
                    <Link key={item.id} href={item.url} className="announcement-row announcement-row--media">
                        <div className="announcement-row-media">
                            <img src={item.image} alt="" loading="lazy" />
                        </div>
                        <div className="announcement-row-body">
                            <div className="announcement-row-meta">
                                <Badge>{item.type}</Badge>
                                <span>{item.date}</span>
                            </div>
                            <h3>{item.title}</h3>
                            <p>{item.excerpt}</p>
                            <span className="text-link">Baca selengkapnya <ArrowRight size={16} /></span>
                        </div>
                    </Link>
                ))}
            </div>
        </Section>
    );
}
