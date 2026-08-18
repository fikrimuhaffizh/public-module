import React from 'react';
import { Clock, MessageCircle, Phone } from 'lucide-react';
import { openStatusNow, useNow } from './hours';

/**
 * Top Bar Mode 3 — compact satu baris: status singkat + jam + kontak penting.
 * Hemat ruang — pas untuk halaman yang ingin tetap bersih.
 * Prop: { site, settings }
 */
export default function TopBarMode3({ site = {}, settings = {} }) {
    const hours = settings.topbar_hours || '';
    const status = openStatusNow(hours, useNow());

    const phone = site.phone || '';
    const whatsapp = site.whatsapp || '';

    const items = [
        status && {
            node: (
                <span className={`topbar-status ${status.open ? 'open' : 'closed'}`} title="Berdasarkan jam operasional & waktu saat ini">
                    <i />{status.open ? 'Buka' : 'Tutup'}
                </span>
            ),
        },
        hours && { icon: Clock, text: hours, href: null },
        phone && { icon: Phone, text: phone, href: `tel:${phone}` },
        whatsapp && { icon: MessageCircle, text: 'WhatsApp', href: `https://wa.me/${whatsapp}`, blank: true },
    ].filter(Boolean);

    if (!items.length) return null;

    return (
        <header className="topbar">
            <div className="shell topbar-inner topbar-inner--center">
                {items.map((item, i) => (
                    item.node ? (
                        <span key={i}>{item.node}</span>
                    ) : (
                        <a
                            key={i}
                            className="topbar-item topbar-link"
                            href={item.href}
                            {...(item.blank ? { target: '_blank', rel: 'noreferrer' } : {})}
                        >
                            <item.icon size={13} />
                            <span>{item.text}</span>
                        </a>
                    )
                ))}
            </div>
        </header>
    );
}
