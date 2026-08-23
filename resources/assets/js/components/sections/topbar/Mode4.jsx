import React from 'react';
import { Mail, MapPin, Phone, Clock } from 'lucide-react';
import { openStatusNow, useNow } from './hours';

/**
 * Topbar Mode 4 — Minimal Center.
 * Semua info di tengah, rapi dan bersih.
 * Prop: { site, settings }
 */
export default function TopbarMode4({ site = {}, settings = {} }) {
    const hours = settings.topbar_hours || '';
    const status = openStatusNow(hours, useNow());
    const address = site.address || '';
    const phone = site.phone || '';
    const email = site.email || '';

    const items = [
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
        phone && { icon: Phone, text: phone, href: `tel:${phone}` },
        email && { icon: Mail, text: email, href: `mailto:${email}` },
    ].filter(Boolean);

    if (!items.length) return null;

    return (
        <header className="topbar topbar--minimal-center">
            <div className="shell topbar-inner topbar-inner--center">
                {items.map((item, i) =>
                    item.node ? (
                        <span key={i} className="topbar-item">{item.node}</span>
                    ) : (
                        <span key={i} className="topbar-item">
                            {item.href ? (
                                <a className="topbar-link" href={item.href}>
                                    <item.icon size={13} />
                                    <span>{item.text}</span>
                                </a>
                            ) : (
                                <>
                                    <item.icon size={13} />
                                    <span>{item.text}</span>
                                </>
                            )}
                        </span>
                    )
                )}
            </div>
        </header>
    );
}
