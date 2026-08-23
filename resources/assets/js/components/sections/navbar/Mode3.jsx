import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { useScrollShadow } from './useNavbarEffects';
import NavbarMenuItem from './NavbarMenuItem';

/**
 * Navbar Mode 3 — tanpa tombol login: logo di kiri, menu navigasi di kanan.
 * Hanya tombol hamburger di mobile.
 * Fitur: scroll shadow, active page indicator, dropdown submenus.
 * Prop: { site, menus, open, onToggle }
 */
export default function NavbarMode3({ site, menus, open, onToggle }) {
    const siteName = site?.name || '';
    const scrolled = useScrollShadow();

    return (
        <header className={`site-header site-header--nologin${scrolled ? ' site-header--scrolled' : ''}`}>
            <div className="shell nav-wrap">
                <Link href={site.homeUrl} className="brand">
                    {site.logo
                        ? <img src={site.logo} alt={siteName} className="brand-logo" width="32" height="32" loading="eager" decoding="async" />
                        : <span className="brand-mark"><GraduationCap size={24} /></span>}
                </Link>
                <div className="nav-right-group">
                    <nav className="desktop-nav">
                        {(menus || []).map(menu => (
                            <NavbarMenuItem key={menu.id} item={menu} />
                        ))}
                    </nav>
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
