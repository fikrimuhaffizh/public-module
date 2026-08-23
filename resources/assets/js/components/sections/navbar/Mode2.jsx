import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { useScrollShadow } from './useNavbarEffects';
import NavbarMenuItem from './NavbarMenuItem';

/**
 * Navbar Mode 2 — dua baris:
 *   Baris 1: logo di kiri + tombol masuk di kanan.
 *   Baris 2: menu navigasi penuh (border-top tipis).
 * Fitur: scroll shadow, active page indicator, dropdown submenus.
 * Prop: { site, menus, open, onToggle, settings }
 */
export default function NavbarMode2({ site, menus, open, onToggle, settings = {} }) {
    const siteName = site?.name || '';
    const scrolled = useScrollShadow();

    return (
        <header className={`site-header site-header--rows${scrolled ? ' site-header--scrolled' : ''}`}>
            <div className="shell nav-wrap">
                <Link href={site.homeUrl} className="brand">
                    {site.logo
                        ? <img src={site.logo} alt={siteName} className="brand-logo" width="32" height="32" loading="eager" decoding="async" />
                        : <span className="brand-mark"><GraduationCap size={24} /></span>}
                </Link>
                <div className="nav-actions">
                    {settings.show_login !== false && <Button asChild><a href={site.loginUrl}>Masuk</a></Button>}
                    <button className="mobile-toggle" onClick={onToggle} aria-label="Buka navigasi">{open ? <X /> : <Menu />}</button>
                </div>
            </div>
            <div className="nav-rows-menu shell">
                <nav className="desktop-nav">
                    {(menus || []).map(menu => (
                        <NavbarMenuItem key={menu.id} item={menu} />
                    ))}
                </nav>
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
