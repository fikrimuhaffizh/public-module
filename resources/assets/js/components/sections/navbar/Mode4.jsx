import React from 'react';
import { Link } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { Button } from '@public/components/ui/button';

/**
 * Navbar Mode 4 — dock mengambang: logo + menu + CTA dalam satu pill terpusat,
 * item membesar saat hover (efek magnification ala macOS dock).
 * Prop: { site, menus, open, onToggle }
 */
export default function NavbarMode4({ site, menus, open, onToggle }) {
    const siteName = site?.name || '';
    return (
        <header className="site-header site-header--dock">
            <div className="shell nav-dock-wrap">
                <div className="nav-dock">
                    <Link href={site.homeUrl} className="nav-dock-brand" title={siteName} aria-label={siteName}>
                        {site.logo
                            ? <img src={site.logo} alt={siteName} className="brand-logo" />
                            : <span className="brand-mark"><GraduationCap size={20} /></span>}
                    </Link>

                    <nav className="nav-dock-menu" aria-label="Navigasi utama">
                        {(menus || []).map(menu => menu.target === '_blank'
                            ? <a key={menu.id} href={menu.url} target="_blank" rel="noreferrer">{menu.title}</a>
                            : <Link key={menu.id} href={menu.url}>{menu.title}</Link>)}
                    </nav>

                    <div className="nav-dock-cta">
                        <Button asChild size="sm"><a href={site.loginUrl}>Masuk</a></Button>
                    </div>

                    <button className="mobile-toggle nav-dock-toggle" onClick={onToggle} aria-label="Buka navigasi">
                        {open ? <X /> : <Menu />}
                    </button>
                </div>
            </div>

            {open && <nav className="mobile-nav shell">{(menus || []).map(menu => <Link key={menu.id} href={menu.url} onClick={onToggle}>{menu.title}</Link>)}</nav>}
        </header>
    );
}
