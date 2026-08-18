import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap } from 'lucide-react';
import { FooterSocials } from './Mode1';

/**
 * Footer Mode 3 — brand + sosmed di baris atas, kolom navigasi/kontak di tengah,
 * bar copyright di bawah (struktur korporat).
 * Prop: { site, footerMenus }
 */
export default function FooterMode3({ site, footerMenus }) {
    const siteName = site?.name || '';
    return (
        <footer className="site-footer site-footer--corporate">
            <div className="shell">
                <div className="footer-corp-top">
                    <div className="brand brand--footer">
                        {site.logoFooter
                            ? <img src={site.logoFooter} alt={siteName} className="brand-logo" />
                            : <span className="brand-mark"><GraduationCap size={24} /></span>}
                        <span>{siteName}</span>
                    </div>
                    <p>{site?.description || site?.tagline}</p>
                    <FooterSocials social={site?.social} />
                </div>
                <div className="footer-grid">
                    <div>
                        <strong>Navigasi</strong>
                        <Link href={site.homeUrl}>Beranda</Link>
                        {(footerMenus || []).map(m =>
                            m.target === '_blank'
                                ? <a key={m.id} href={m.url} target="_blank" rel="noreferrer">{m.title}</a>
                                : <Link key={m.id} href={m.url}>{m.title}</Link>
                        )}
                        <Link href={site.contactUrl}>Hubungi Kami</Link>
                    </div>
                    <div>
                        <strong>Kontak</strong>
                        {site?.address && <span>{site.address}</span>}
                        {site?.email && <a href={`mailto:${site.email}`}>{site.email}</a>}
                        {site?.phone && <a href={`tel:${site.phone}`}>{site.phone}</a>}
                        {site?.whatsapp && (
                            <a href={`https://wa.me/${site.whatsapp}`} target="_blank" rel="noreferrer">
                                WhatsApp
                            </a>
                        )}
                    </div>
                    <div className="footer-corp-hours">
                        <strong>Jam Layanan</strong>
                        <span>Senin – Jumat: 08.00 – 16.00</span>
                        <span>Sabtu: 08.00 – 12.00</span>
                    </div>
                </div>
                <div className="footer-corp-bottom">
                    <span>&copy; {new Date().getFullYear()} {siteName}. All rights reserved.</span>
                </div>
            </div>
        </footer>
    );
}
