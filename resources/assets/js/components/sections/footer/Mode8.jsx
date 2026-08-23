import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap } from 'lucide-react';
import { FooterSocials } from './Mode1';

/**
 * Footer Mode 8 — Dark Gradient.
 * Background gelap gradient, 3 kolom: Brand, Navigasi, Kontak.
 * Premium look — cocok untuk corporate/enterprise.
 * Prop: { site, footerMenus }
 */
export default function FooterMode8({ site, footerMenus }) {
    const siteName = site?.name || '';

    return (
        <footer className="site-footer site-footer--gradient">
            <div className="shell">
                <div className="footer-gradient-grid">
                    {/* Brand Column */}
                    <div className="footer-gradient-brand">
                        <div className="brand brand--footer" style={{ color: '#fff' }}>
                            {site.logoFooter
                                ? <img src={site.logoFooter} alt={siteName} className="brand-logo" />
                                : <span className="brand-mark"><GraduationCap size={20} /></span>}
                            <span>{siteName}</span>
                        </div>
                        {site?.description && <p>{site.description}</p>}
                        <FooterSocials social={site?.social} />
                    </div>

                    {/* Navigation Column */}
                    <nav className="footer-gradient-nav">
                        <strong>Navigasi</strong>
                        <Link href={site.homeUrl}>Beranda</Link>
                        {(footerMenus || []).map(m =>
                            m.target === '_blank'
                                ? <a key={m.id} href={m.url} target="_blank" rel="noreferrer">{m.title}</a>
                                : <Link key={m.id} href={m.url}>{m.title}</Link>
                        )}
                        <Link href={site.contactUrl}>Kontak</Link>
                    </nav>

                    {/* Contact Column */}
                    <div className="footer-gradient-contact">
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
                <div className="footer-copy">
                    &copy; {new Date().getFullYear()} {siteName}. All rights reserved.
                </div>
            </div>
        </footer>
    );
}
