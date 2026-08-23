import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap } from 'lucide-react';
import { FooterSocials } from './Mode1';

/**
 * Footer Mode 6 — Bordered.
 * 4 kolom: Brand + deskripsi, Navigasi, Kontak, Jam Layanan.
 * Border antar kolom, clean corporate look.
 * Prop: { site, footerMenus }
 */
export default function FooterMode6({ site, footerMenus }) {
    const siteName = site?.name || '';

    // Split menus into 2 columns if more than 4 items
    const mid = Math.ceil((footerMenus || []).length / 2);
    const menuCol1 = (footerMenus || []).slice(0, mid);
    const menuCol2 = (footerMenus || []).slice(mid);

    return (
        <footer className="site-footer site-footer--bordered">
            <div className="shell">
                <div className="footer-bordered-grid">
                    {/* Brand Column */}
                    <div className="footer-bordered-brand">
                        <div className="brand brand--footer">
                            {site.logoFooter
                                ? <img src={site.logoFooter} alt={siteName} className="brand-logo" />
                                : <span className="brand-mark"><GraduationCap size={20} /></span>}
                            <span>{siteName}</span>
                        </div>
                        {site?.description && <p className="footer-desc">{site.description}</p>}
                        <FooterSocials social={site?.social} />
                    </div>

                    {/* Navigation Column */}
                    <div className="footer-bordered-col">
                        <strong>Navigasi</strong>
                        <Link href={site.homeUrl}>Beranda</Link>
                        {menuCol1.map(m =>
                            m.target === '_blank'
                                ? <a key={m.id} href={m.url} target="_blank" rel="noreferrer">{m.title}</a>
                                : <Link key={m.id} href={m.url}>{m.title}</Link>
                        )}
                        <Link href={site.contactUrl}>Kontak</Link>
                    </div>

                    {/* Navigation Column 2 (if many items) */}
                    {menuCol2.length > 0 && (
                        <div className="footer-bordered-col">
                            <strong>Lainnya</strong>
                            {menuCol2.map(m =>
                                m.target === '_blank'
                                    ? <a key={m.id} href={m.url} target="_blank" rel="noreferrer">{m.title}</a>
                                    : <Link key={m.id} href={m.url}>{m.title}</Link>
                            )}
                        </div>
                    )}

                    {/* Contact Column */}
                    <div className="footer-bordered-col">
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
                </div>

                {/* Copyright */}
                <div className="footer-bordered-copy">
                    &copy; {new Date().getFullYear()} {siteName}. All rights reserved.
                </div>
            </div>
        </footer>
    );
}
