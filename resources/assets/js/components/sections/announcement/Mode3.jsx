import React from 'react';
import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Section, combinedText } from '../index';

/** Pengumuman Mode 3 — satu berita unggulan besar + sisa daftar kecil. Prop: { section, data } */
export default function AnnouncementMode3({ section, data }) {
    const list = data.announcements || [];
    if (!list.length) return null;
    const [lead, ...rest] = list;
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
            <div className="news-lead-wrap">
                <Link href={lead.url} className="news-lead">
                    <img src={lead.image} alt={lead.title} />
                    <div className="news-lead-body">
                        <div className="news-meta"><Badge>{lead.type}</Badge><span>{lead.date}</span></div>
                        <h3>{lead.title}</h3>
                        <p>{lead.excerpt}</p>
                        <span className="text-link">Baca selengkapnya <ArrowRight size={16} /></span>
                    </div>
                </Link>
                <div className="news-mini-list">
                    {rest.slice(0, limit - 1).map(item => (
                        <Link key={item.id} href={item.url} className="news-mini">
                            <span>{item.date}</span>
                            <strong>{item.title}</strong>
                        </Link>
                    ))}
                </div>
            </div>
        </Section>
    );
}
