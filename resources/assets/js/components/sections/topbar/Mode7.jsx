import React from 'react';
import { Globe, MapPin } from 'lucide-react';

/**
 * Topbar Mode 7 — Social.
 * Alamat di kiri, social media links di kanan (text-based, no brand icons in lucide).
 * Prop: { site, settings }
 */
export default function TopbarMode7({ site = {}, settings = {} }) {
    const address = site.address || '';
    const facebook = site.facebook || site.facebook_url || '';
    const instagram = site.instagram || site.instagram_url || '';
    const linkedin = site.linkedin || site.linkedin_url || '';
    const youtube = site.youtube || site.youtube_url || '';

    const socials = [
        facebook && { label: 'Facebook', href: facebook },
        instagram && { label: 'Instagram', href: instagram },
        linkedin && { label: 'LinkedIn', href: linkedin },
        youtube && { label: 'YouTube', href: youtube },
    ].filter(Boolean);

    if (!address && !socials.length) return null;

    return (
        <header className="topbar topbar--social">
            <div className="shell topbar-inner">
                <div className="topbar-left">
                    {address && (
                        <span className="topbar-item">
                            <MapPin size={13} />
                            <span>{address}</span>
                        </span>
                    )}
                </div>
                <div className="topbar-right">
                    {socials.map((item, i) => (
                        <a
                            key={i}
                            className="topbar-item topbar-link"
                            href={item.href}
                            target="_blank"
                            rel="noreferrer"
                            title={item.label}
                        >
                            <Globe size={13} />
                            <span>{item.label}</span>
                        </a>
                    ))}
                </div>
            </div>
        </header>
    );
}
