import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { useScrollShadow, isMenuActive } from './useNavbarEffects';

/**
 * Navbar Mode 4 — dock mengambang: logo + menu + CTA dalam satu pill terpusat,
 * item membesar saat hover (efek magnification ala macOS dock).
 * Fitur: scroll shadow, active page indicator.
 * Prop: { site, menus, open, onToggle }
 */
export default function NavbarMode4({ site, menus, open, onToggle, settings = {} }) {
    const siteName = site?.name || '';
    const { url } = usePage();
    const scrolled = useScrollShadow();

    return (
        <header className={`site-header site-header--dock${scrolled ? ' site-header--scrolled' : ''}`}>
            <div className="shell nav-dock-wrap">
                <div className="nav-dock">
                    <Link href={site.homeUrl} className="nav-dock-brand" title={siteName} aria-label={siteName}>
                        {site.logo
                            ? <img src={site.logo} alt={siteName} className="brand-logo" />
                            : <span className="brand-mark"><GraduationCap size={20} /></span>}
                    </Link>

                    <nav className="nav-dock-menu" aria-label="Navigasi utama">
                        {(menus || []).map(menu => {
                            const active = isMenuActive(menu.url, url);
                            const cls = active ? ' nav-active' : '';
                            return menu.target === '_blank'
                                ? <a key={menu.id} href={menu.url} target="_blank" rel="noreferrer" className={cls}>{menu.title}</a>
                                : <Link key={menu.id} href={menu.url} className={cls}>{menu.title}</Link>;
                        })}
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

            {open && <nav className="mobile-nav shell">{(menus || []).map(menu => <Link key={menu.id} href={menu.url} onClick={onToggle}>{menu.title}</Link>)}</nav>}
        </header>
    );
}
