import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { useScrollShadow } from './useNavbarEffects';
import NavbarMenuItem from './NavbarMenuItem';

/**
 * Navbar Mode 7 — Minimal Clean.
 * Border bottom tipis, spacing luas, typografi bersih.
 * Logo di kiri, menu center, CTA di kanan.
 * Inspirasi: Vercel, Notion, Linear.
 * Prop: { site, menus, open, onToggle, settings }
 */
export default function NavbarMode7({ site, menus, open, onToggle, settings = {} }) {
    const siteName = site?.name || '';
    const scrolled = useScrollShadow();
    const showLogin = settings?.show_login !== false;

    return (
        <header className={`site-header site-header--bordered ${scrolled ? 'site-header--scrolled' : ''}`}>
            <div className="shell nav-wrap">
                {/* Logo */}
                <Link href={site.homeUrl} className="brand">
                    {site.logo
                        ? <img src={site.logo} alt={siteName} className="brand-logo" width="32" height="32" loading="eager" decoding="async" />
                        : <span className="brand-mark"><GraduationCap size={20} /></span>}
                </Link>

                {/* Menu — center */}
                <nav className="desktop-nav">
                    {(menus || []).map(menu => (
                        <NavbarMenuItem key={menu.id} item={menu} />
                    ))}
                </nav>

                {/* CTA */}
                <div className="nav-actions">
                    {showLogin && (
                        <Button asChild variant="outline" size="sm" className="nav-bordered-btn">
                            <a href={site.loginUrl}>Masuk</a>
                        </Button>
                    )}
                    <button className="mobile-toggle" onClick={onToggle} aria-label="Menu">
                        {open ? <X size={20} /> : <Menu size={20} />}
                    </button>
                </div>
            </div>

            {/* Mobile menu */}
            {open && (
                <nav className="mobile-nav shell">
                    {(menus || []).map(menu => (
                        <NavbarMenuItem key={menu.id} item={menu} onToggle={onToggle} isMobile />
                    ))}
                    {showLogin && (
                        <a href={site.loginUrl} className="ui-btn ui-btn--primary" style={{ marginTop: '12px' }}>Masuk</a>
                    )}
                </nav>
            )}
        </header>
    );
}
