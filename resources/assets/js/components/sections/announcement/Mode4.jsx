import React from 'react';
import { Link } from '@inertiajs/react';
import { Megaphone } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Section, combinedText } from '../index';

/** Pengumuman Mode 4 — ticker berjalan: judul berita melintas terus-menerus. Prop: { section, data } */
export default function AnnouncementMode4({ section, data }) {
    const list = data.announcements || [];
    if (!list.length) return null;
    const limit = section?.limit_data || 6;
    const items = list.slice(0, limit);

    return (
        <Section
            section={section}
            id="berita"
            tint
            eyebrow={section.pre_title || 'Tetap terhubung'}
            title={section.title || 'Kabar terbaru'}
            text={combinedText(section)}
        >
            <div className="announcement-ticker" role="marquee" aria-label="Pengumuman terbaru">
                <span className="announcement-ticker-label">
                    <Megaphone size={18} />
                    <strong>Berita</strong>
                </span>
                <div className="announcement-ticker-viewport">
                    <div className="announcement-ticker-track" data-pause-offscreen>
                        {[...items, ...items].map((item, index) => (
                            <Link key={`${item.id}-${index}`} href={item.url} className="announcement-ticker-item">
                                <Badge>{item.type}</Badge>
                                <span>{item.title}</span>
                                <i aria-hidden="true" />
                            </Link>
                        ))}
                    </div>
                </div>
            </div>
        </Section>
    );
}
