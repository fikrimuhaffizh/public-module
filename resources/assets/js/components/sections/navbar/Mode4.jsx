import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { useScrollShadow } from './useNavbarEffects';
import NavbarMenuItem from './NavbarMenuItem';

/**
 * Navbar Mode 4 — dock mengambang: logo + menu + CTA dalam satu pill terpusat,
 * item membesar saat hover (efek magnification ala macOS dock).
 * Fitur: scroll shadow, active page indicator, dropdown submenus.
 * Prop: { site, menus, open, onToggle, settings }
 */
export default function NavbarMode4({ site, menus, open, onToggle, settings = {} }) {
    const siteName = site?.name || '';
    const scrolled = useScrollShadow();

    return (
        <header className={`site-header site-header--dock${scrolled ? ' site-header--scrolled' : ''}`}>
            <div className="shell nav-dock-wrap">
                <div className="nav-dock">
                    <Link href={site.homeUrl} className="nav-dock-brand" title={siteName} aria-label={siteName}>
                        {site.logo
                            ? <img src={site.logo} alt={siteName} className="brand-logo" width="32" height="32" loading="eager" decoding="async" />
                            : <span className="brand-mark"><GraduationCap size={20} /></span>}
                    </Link>

                    <nav className="nav-dock-menu" aria-label="Navigasi utama">
                        {(menus || []).map(menu => (
                            <NavbarMenuItem key={menu.id} item={menu} />
                        ))}
                    </nav>

                    {settings.show_login !== false && (
                        <div className="nav-dock-cta">
                            <Button asChild size="sm"><a href={site.loginUrl}>Masuk</a></Button>
                        </div>
                    )}

                    <button className="mobile-toggle nav-dock-toggle" onClick={onToggle} aria-label="Buka navigasi">
                        {open ? <X /> : <Menu />}
                    </button>
                </div>
            </div>

            {open && (
                <nav className="mobile-nav shell">
                    {(menus || []).map(menu => (
                        <NavbarMenuItem key={menu.id} item={menu} onToggle={onToggle} isMobile />
                    ))}
                </nav>
            )}
        </header>
    );
}
