import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { useScrollShadow, isMenuActive } from './useNavbarEffects';

/**
 * Navbar Mode 3 — tanpa tombol login: logo di kiri, menu navigasi di kanan.
 * Hanya tombol hamburger di mobile.
 * Fitur: scroll shadow, active page indicator.
 * Prop: { site, menus, open, onToggle }
 */
export default function NavbarMode3({ site, menus, open, onToggle }) {
    const siteName = site?.name || '';
    const { url } = usePage();
    const scrolled = useScrollShadow();

    return (
        <header className={`site-header site-header--nologin${scrolled ? ' site-header--scrolled' : ''}`}>
            <div className="shell nav-wrap">
                <Link href={site.homeUrl} className="brand">
                    {site.logo
                        ? <img src={site.logo} alt={siteName} className="brand-logo" />
                        : <span className="brand-mark"><GraduationCap size={24} /></span>}
                </Link>
                <div className="nav-right-group">
                    <nav className="desktop-nav">
                        {(menus || []).map(menu => {
                            const active = isMenuActive(menu.url, url);
                            const cls = active ? ' nav-active' : '';
                            return menu.target === '_blank'
                                ? <a key={menu.id} href={menu.url} target="_blank" rel="noreferrer" className={cls}>{menu.title}</a>
                                : <Link key={menu.id} href={menu.url} className={cls}>{menu.title}</Link>;
                        })}
                    </nav>
                    <button className="mobile-toggle" onClick={onToggle} aria-label="Buka navigasi">{open ? <X /> : <Menu />}</button>
                </div>
            </div>
            {open && <nav className="mobile-nav shell">{(menus || []).map(menu => <Link key={menu.id} href={menu.url} onClick={onToggle} className={isMenuActive(menu.url, url) ? 'nav-active' : ''}>{menu.title}</Link>)}</nav>}
        </header>
    );
}
