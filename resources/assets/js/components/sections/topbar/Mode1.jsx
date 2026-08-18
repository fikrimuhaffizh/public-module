import React from 'react';
import { Clock, MapPin, MessageCircle, Phone } from 'lucide-react';
import { openStatusNow, useNow } from './hours';

/**
 * Top Bar Mode 1 — strip klasik: kiri alamat + jam, kanan telepon + WhatsApp,
 * plus status "Buka/Tutup" real-time di depan.
 * Prop: { site, settings } — layout-level, seperti navbar/footer.
 */
export default function TopBarMode1({ site = {}, settings = {} }) {
    const hours = settings.topbar_hours || '';
    const status = openStatusNow(hours, useNow());

    const address = site.address || '';
    const phone = site.phone || '';
    const whatsapp = site.whatsapp || '';

    const left = [
        address && { icon: MapPin, text: address, href: null },
        (hours || status) && { icon: Clock, text: hours || status.label, href: null },
    ].filter(Boolean);
    const right = [
        phone && { icon: Phone, text: phone, href: `tel:${phone}` },
        whatsapp && { icon: MessageCircle, text: 'WhatsApp', href: `https://wa.me/${whatsapp}`, blank: true },
    ].filter(Boolean);

    if (!left.length && !right.length) return null;

    return (
        <header className="topbar">
            <div className="shell topbar-inner">
                <div className="topbar-left">
                    {status && (
                        <span className={`topbar-status ${status.open ? 'open' : 'closed'}`} title="Berdasarkan jam operasional & waktu saat ini">
                            <i />
                            {status.open ? 'Buka sekarang' : 'Tutup sekarang'}
                        </span>
                    )}
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
