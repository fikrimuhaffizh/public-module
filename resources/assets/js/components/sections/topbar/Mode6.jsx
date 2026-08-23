import React from 'react';
import { Phone, Mail, MessageCircle, MapPin } from 'lucide-react';

/**
 * Topbar Mode 6 — Icons.
 * Ikon + teks clickable, info di kiri, kontak di kanan.
 * Prop: { site, settings }
 */
export default function TopbarMode6({ site = {}, settings = {} }) {
    const address = site.address || '';
    const phone = site.phone || '';
    const email = site.email || '';
    const whatsapp = site.whatsapp || '';

    const left = [
        address && { icon: MapPin, text: address },
    ].filter(Boolean);

    const right = [
        phone && { icon: Phone, text: phone, href: `tel:${phone}` },
        email && { icon: Mail, text: email, href: `mailto:${email}` },
        whatsapp && { icon: MessageCircle, text: 'Chat WhatsApp', href: `https://wa.me/${whatsapp}`, blank: true },
    ].filter(Boolean);

    if (!left.length && !right.length) return null;

    return (
        <header className="topbar topbar--icons">
            <div className="shell topbar-inner">
                <div className="topbar-left">
                    {left.map((item, i) => (
                        <span key={i} className="topbar-item">
                            <item.icon size={13} />
                            <span>{item.text}</span>
                        </span>
                    ))}
                </div>
                <div className="topbar-right">
                    {right.map((item, i) => (
                        <a
                            key={i}
                            className="topbar-item topbar-link"
                            href={item.href}
                            {...(item.blank ? { target: '_blank', rel: 'noreferrer' } : {})}
                        >
                            <item.icon size={13} />
                            <span>{item.text}</span>
                        </a>
                    ))}
                </div>
            </div>
        </header>
    );
}
