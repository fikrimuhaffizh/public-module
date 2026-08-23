import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap, Send } from 'lucide-react';
import { FooterSocials } from './Mode1';

/**
 * Footer Mode 7 — Newsletter.
 * Newsletter form + nav links + social + contact + copyright.
 * Modern SaaS style — cocok untuk startup/tech.
 * Prop: { site, footerMenus }
 */
export default function FooterMode7({ site, footerMenus }) {
    const siteName = site?.name || '';

    return (
        <footer className="site-footer site-footer--newsletter">
            <div className="shell">
                {/* Newsletter CTA */}
                <div className="footer-newsletter-row">
                    <div className="footer-newsletter-copy">
                        <h4>{siteName}</h4>
                        <p>Dapatkan update terbaru langsung di inbox Anda.</p>
                    </div>
                    <form className="footer-newsletter-form" onSubmit={e => e.preventDefault()}>
                        <input type="email" placeholder="Email address" className="footer-newsletter-input" />
                        <button type="submit" className="footer-newsletter-btn">
                            <Send size={16} />
                            Subscribe
                        </button>
                    </form>
                </div>

                {/* Navigation Links */}
                <div className="footer-newsletter-links">
                    <Link href={site.homeUrl}>Beranda</Link>
                    {(footerMenus || []).map(m =>
                        m.target === '_blank'
                            ? <a key={m.id} href={m.url} target="_blank" rel="noreferrer">{m.title}</a>
                            : <Link key={m.id} href={m.url}>{m.title}</Link>
                    )}
                    <Link href={site.contactUrl}>Kontak</Link>
                </div>

                {/* Social + Contact Row */}
                <div className="footer-newsletter-bottom">
                    <FooterSocials social={site?.social} />
                    <div className="footer-contact-row">
                        {site?.email && <a href={`mailto:${site.email}`}>{site.email}</a>}
                        {site?.phone && <a href={`tel:${site.phone}`}>{site.phone}</a>}
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
