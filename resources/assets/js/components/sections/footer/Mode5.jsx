import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap } from 'lucide-react';
import { FooterSocials } from './Mode1';

/**
 * Footer Mode 5 — Minimal Center.
 * Brand center, nav center, social, contact, copyright.
 * Bersih dan elegan — cocok untuk SaaS/Startup.
 * Prop: { site, footerMenus }
 */
export default function FooterMode5({ site, footerMenus }) {
    const siteName = site?.name || '';

    return (
        <footer className="site-footer site-footer--minimal-center">
            <div className="shell">
                {/* Brand */}
                <div className="footer-center-brand">
                    {site.logoFooter
                        ? <img src={site.logoFooter} alt={siteName} className="footer-logo" />
                        : <span className="brand-mark"><GraduationCap size={20} /></span>}
                </div>

                {/* Navigation */}
                <nav className="footer-nav-center">
                    <Link href={site.homeUrl}>Beranda</Link>
                    {(footerMenus || []).map(m =>
                        m.target === '_blank'
                            ? <a key={m.id} href={m.url} target="_blank" rel="noreferrer">{m.title}</a>
                            : <Link key={m.id} href={m.url}>{m.title}</Link>
                    )}
                    <Link href={site.contactUrl}>Kontak</Link>
                </nav>

                {/* Social */}
                <FooterSocials social={site?.social} />

                {/* Contact */}
                <div className="footer-contact-row">
                    {site?.email && <a href={`mailto:${site.email}`}>{site.email}</a>}
                    {site?.phone && <a href={`tel:${site.phone}`}>{site.phone}</a>}
                </div>

                {/* Copyright */}
                <div className="footer-copy">
                    &copy; {new Date().getFullYear()} {siteName}. All rights reserved.
                </div>
            </div>
        </footer>
    );
}
