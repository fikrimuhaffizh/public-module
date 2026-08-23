import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { useScrollShadow } from './useNavbarEffects';
import NavbarMenuItem from './NavbarMenuItem';

/**
 * Navbar Mode 1 — logo di kiri, menu navigasi di kanan (klasik).
 * Fitur: scroll shadow, active page indicator, dropdown submenus.
 * Prop: { site, menus, open, onToggle, settings }
 */
export default function NavbarMode1({ site, menus, open, onToggle, settings = {} }) {
    const siteName = site?.name || '';
    const scrolled = useScrollShadow();

    return (
        <header className={`site-header${scrolled ? ' site-header--scrolled' : ''}`}>
            <div className="shell nav-wrap">
                <Link href={site.homeUrl} className="brand">
                    {site.logo
                        ? <img src={site.logo} alt={siteName} className="brand-logo" width="32" height="32" loading="eager" decoding="async" />
                        : <span className="brand-mark"><GraduationCap size={24} /></span>}
                </Link>
                <nav className="desktop-nav">
                    {(menus || []).map(menu => (
                        <NavbarMenuItem key={menu.id} item={menu} />
                    ))}
                </nav>
                <div className="nav-actions">
                    {settings.show_login !== false && <Button asChild><a href={site.loginUrl}>Masuk</a></Button>}
                    <button className="mobile-toggle" onClick={onToggle} aria-label="Buka navigasi">{open ? <X /> : <Menu />}</button>
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
