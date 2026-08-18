import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { Button } from '@public/components/ui/button';

/**
 * Navbar Mode 2 — dua baris:
 *   Baris 1: logo di kiri + tombol masuk di kanan.
 *   Baris 2: menu navigasi penuh (border-top tipis).
 * Prop: { site, menus, open, onToggle }
 */
export default function NavbarMode2({ site, menus, open, onToggle }) {
    const siteName = site?.name || '';
    return (
        <header className="site-header site-header--rows">
            <div className="shell nav-wrap">
                <Link href={site.homeUrl} className="brand">
                    {site.logo
                        ? <img src={site.logo} alt={siteName} className="brand-logo" />
                        : <span className="brand-mark"><GraduationCap size={24} /></span>}
                </Link>
                <div className="nav-actions">
                    <Button asChild><a href={site.loginUrl}>Masuk</a></Button>
                    <button className="mobile-toggle" onClick={onToggle} aria-label="Buka navigasi">{open ? <X /> : <Menu />}</button>
                </div>
            </div>
            <div className="nav-rows-menu shell">
                <nav className="desktop-nav">
                    {(menus || []).map(menu => menu.target === '_blank'
                        ? <a key={menu.id} href={menu.url} target="_blank" rel="noreferrer">{menu.title}</a>
                        : <Link key={menu.id} href={menu.url}>{menu.title}</Link>)}
                </nav>
            </div>
            {open && <nav className="mobile-nav shell">{(menus || []).map(menu => <Link key={menu.id} href={menu.url} onClick={onToggle}>{menu.title}</Link>)}</nav>}
        </header>
    );
}
