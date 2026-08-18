import React from 'react';
import { Clock, MapPin, MessageCircle, Phone } from 'lucide-react';
import { openStatusNow, useNow } from './hours';

/**
 * Top Bar Mode 2 — banner terpusat: status buka/tutup besar + alamat + jam,
 * kontak di kanan. Cocok untuk UMKM yang ingin info kontak menonjol.
 * Prop: { site, settings }
 */
export default function TopBarMode2({ site = {}, settings = {} }) {
    const hours = settings.topbar_hours || '';
    const status = openStatusNow(hours, useNow());

    const address = site.address || '';
    const phone = site.phone || '';
    const whatsapp = site.whatsapp || '';

    if (!address && !hours && !phone && !whatsapp) return null;

    return (
        <header className="topbar topbar--banner">
            <div className="shell topbar-inner">
                {status && (
                    <span className={`topbar-status topbar-status--lg ${status.open ? 'open' : 'closed'}`}>
                        <i />
                        {status.open ? 'Buka sekarang' : 'Tutup sekarang'}
                    </span>
                )}
                <div className="topbar-banner-copy">
                    {address && (
                        <span className="topbar-item"><MapPin size={14} /><span>{address}</span></span>
                    )}
                    {hours && (
                        <span className="topbar-item"><Clock size={14} /><span>{hours}</span></span>
                    )}
                </div>
                <div className="topbar-right">
                    {phone && (
                        <a className="topbar-item topbar-link" href={`tel:${phone}`}>
                            <Phone size={14} /><span>{phone}</span>
                        </a>
                    )}
                    {whatsapp && (
                        <a className="topbar-item topbar-link" href={`https://wa.me/${whatsapp}`} target="_blank" rel="noreferrer">
                            <MessageCircle size={14} /><span>Chat WhatsApp</span>
                        </a>
                    )}
                </div>
            </div>
        </header>
    );
}
