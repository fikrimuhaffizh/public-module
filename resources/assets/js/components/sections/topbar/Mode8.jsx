import React from 'react';
import { Phone, Mail, MessageCircle, MapPin, Clock } from 'lucide-react';
import { openStatusNow, useNow } from './hours';

/**
 * Topbar Mode 8 — Dark Premium.
 * Background gelap, info di kiri, kontak di kanan.
 * Prop: { site, settings }
 */
export default function TopbarMode8({ site = {}, settings = {} }) {
    const hours = settings.topbar_hours || '';
    const status = openStatusNow(hours, useNow());
    const address = site.address || '';
    const phone = site.phone || '';
    const whatsapp = site.whatsapp || '';

    const left = [
        status && {
            node: (
                <span className={`topbar-status ${status.open ? 'open' : 'closed'}`}>
                    <i />
                    {status.open ? 'Buka' : 'Tutup'}
                </span>
            ),
        },
        address && { icon: MapPin, text: address },
        hours && { icon: Clock, text: hours },
    ].filter(Boolean);

    const right = [
        phone && { icon: Phone, text: phone, href: `tel:${phone}` },
        whatsapp && { icon: MessageCircle, text: 'WhatsApp', href: `https://wa.me/${whatsapp}`, blank: true },
    ].filter(Boolean);

    if (!left.length && !right.length) return null;

    return (
        <header className="topbar topbar--dark">
            <div className="shell topbar-inner">
                <div className="topbar-left">
                    {left.map((item, i) =>
                        item.node ? (
                            <span key={i} className="topbar-item">{item.node}</span>
                        ) : (
                            <span key={i} className="topbar-item">
                                <item.icon size={13} />
                                <span>{item.text}</span>
                            </span>
                        )
                    )}
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
