import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap } from 'lucide-react';
import { FooterSocials } from './Mode1';

/**
 * Footer Mode 2 — terpusat: brand + deskripsi di tengah, menu sebaris, sosmed, copyright.
 * Prop: { site, footerMenus }
 */
export default function FooterMode2({ site, footerMenus }) {
    const siteName = site?.name || '';
    return (
        <footer className="site-footer site-footer--center">
            <div className="shell footer-center">
                <div className="brand brand--footer">
                    {site.logoFooter
                        ? <img src={site.logoFooter} alt={siteName} className="brand-logo" />
                        : <span className="brand-mark"><GraduationCap size={24} /></span>}
                    <span>{siteName}</span>
                </div>
                <p>{site?.description || site?.tagline}</p>
                <nav className="footer-center-nav">
                    <Link href={site.homeUrl}>Beranda</Link>
                    {(footerMenus || []).map(m =>
                        m.target === '_blank'
                            ? <a key={m.id} href={m.url} target="_blank" rel="noreferrer">{m.title}</a>
                            : <Link key={m.id} href={m.url}>{m.title}</Link>
                    )}
                    <Link href={site.contactUrl}>Hubungi Kami</Link>
                </nav>
                <FooterSocials social={site?.social} />
                <div className="footer-center-bottom">
                    {site?.address && <span>{site.address}</span>}
                    {site?.email && <a href={`mailto:${site.email}`}>{site.email}</a>}
                    {site?.phone && <a href={`tel:${site.phone}`}>{site.phone}</a>}
                    <span>&copy; {new Date().getFullYear()} {siteName}</span>
                </div>
            </div>
        </footer>
    );
}
