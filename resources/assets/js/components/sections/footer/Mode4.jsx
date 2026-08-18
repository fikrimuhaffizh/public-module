import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap, MessageCircle } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { FooterSocials } from './Mode1';

/**
 * Footer Mode 4 — big type: nama brand raksasa sebagai watermark, strip CTA
 * WhatsApp di atas, lalu grid brand + navigasi + kontak.
 * Prop: { site, footerMenus }
 */
export default function FooterMode4({ site, footerMenus }) {
    const siteName = site?.name || '';
    return (
        <footer className="site-footer site-footer--bigtype">
            <div className="footer-bigtype-name" aria-hidden="true">{siteName}</div>

            <div className="shell">
                <div className="footer-bigtype-cta">
                    <div>
                        <strong>Siap memulai?</strong>
                        <p>Konsultasi gratis — tim kami siap membantu Anda.</p>
                    </div>
                    {site?.whatsapp && (
                        <Button asChild><a href={`https://wa.me/${site.whatsapp}`} target="_blank" rel="noreferrer"><MessageCircle size={16} /> Chat WhatsApp</a></Button>
                    )}
                </div>

                <div className="footer-grid">
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
                            <a href={`https://wa.me/${site.whatsapp}`} target="_blank" rel="noreferrer">WhatsApp</a>
                        )}
                        <span>&copy; {new Date().getFullYear()} {siteName}</span>
                    </div>
                </div>
            </div>

            <div className="shell footer-bigtype-bottom">
                <span>&copy; {new Date().getFullYear()} {siteName}. Hak cipta dilindungi.</span>
                <span>Ditenagai Ekosistem Pemutu</span>
            </div>
        </footer>
    );
}
