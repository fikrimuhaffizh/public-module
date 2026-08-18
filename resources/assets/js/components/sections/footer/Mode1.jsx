import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap } from 'lucide-react';

/**
 * Footer Mode 1 — grid 3 kolom klasik: brand+sosmed, navigasi, kontak.
 * Prop: { site, footerMenus }
 */
export function FooterSocials({ social }) {
    return (
        <div className="footer-socials">
            {social?.facebook && <a href={social.facebook} target="_blank" rel="noreferrer" aria-label="Facebook">&#xf09a;</a>}
            {social?.instagram && <a href={social.instagram} target="_blank" rel="noreferrer" aria-label="Instagram">&#xf16d;</a>}
            {social?.linkedin && <a href={social.linkedin} target="_blank" rel="noreferrer" aria-label="LinkedIn">&#xf08c;</a>}
            {social?.youtube && <a href={social.youtube} target="_blank" rel="noreferrer" aria-label="YouTube">&#xf167;</a>}
        </div>
    );
}

export default function FooterMode1({ site, footerMenus }) {
    const siteName = site?.name || '';
    return (
        <footer className="site-footer">
            <div className="shell footer-grid">
                <div>
                    <div className="brand brand--footer">
                        {site.logoFooter
                            ? <img src={site.logoFooter} alt={siteName} className="brand-logo" />
                            : <span className="brand-mark"><GraduationCap size={24} /></span>}
                        <span>{siteName}</span>
                    </div>
                    <p>{site?.description || site?.tagline}</p>
                    <FooterSocials social={site?.social} />
                </div>
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
                    <span>&copy; {new Date().getFullYear()} {siteName}</span>
                </div>
            </div>
        </footer>
    );
}
