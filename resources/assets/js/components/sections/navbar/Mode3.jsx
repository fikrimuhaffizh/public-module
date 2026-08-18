import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';

/**
 * Navbar Mode 3 — tanpa tombol login: logo di kiri, menu navigasi di kanan.
 * Hanya tombol hamburger di mobile. Prop: { site, menus, open, onToggle }
 */
export default function NavbarMode3({ site, menus, open, onToggle }) {
    const siteName = site?.name || '';
    return (
        <header className="site-header site-header--nologin">
            <div className="shell nav-wrap">
                <Link href={site.homeUrl} className="brand">
                    {site.logo
                        ? <img src={site.logo} alt={siteName} className="brand-logo" />
                        : <span className="brand-mark"><GraduationCap size={24} /></span>}
                </Link>
                <div className="nav-right-group">
                    <nav className="desktop-nav">
                        {(menus || []).map(menu => menu.target === '_blank'
                            ? <a key={menu.id} href={menu.url} target="_blank" rel="noreferrer">{menu.title}</a>
                            : <Link key={menu.id} href={menu.url}>{menu.title}</Link>)}
                    </nav>
                    <button className="mobile-toggle" onClick={onToggle} aria-label="Buka navigasi">{open ? <X /> : <Menu />}</button>
                </div>
            </div>
            {open && <nav className="mobile-nav shell">{(menus || []).map(menu => <Link key={menu.id} href={menu.url} onClick={onToggle}>{menu.title}</Link>)}</nav>}
        </header>
    );
}
