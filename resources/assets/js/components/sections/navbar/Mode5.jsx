import React from 'react';
import { Link } from '@inertiajs/react';
import { useScrollShadow } from './useNavbarEffects';
import NavbarMenuItem from './NavbarMenuItem';

/**
 * Navbar Mode 5 — Centered: logo center, menu di bawah.
 * Fitur: scroll shadow, active page indicator, dropdown submenus.
 */
export default function NavbarMode5({ site, menus, open, onToggle, settings }) {
    const scrolled = useScrollShadow();
    const showLogin = settings?.show_login !== false;

    return (
        <header className={`site-header site-header--centered ${scrolled ? 'site-header--scrolled' : ''}`}>
            <div className="shell">
                <div className="nav-centered-brand">
                    {site?.logo && <img src={site.logo} alt={site?.title || ''} className="brand-logo" width="32" height="32" loading="eager" decoding="async" />}
                </div>
                <nav className="desktop-nav nav-centered-menu">
                    {(menus || []).map(menu => (
                        <NavbarMenuItem key={menu.id} item={menu} />
                    ))}
                </nav>
                {showLogin && site?.loginUrl && (
                    <div className="nav-centered-actions">
                        <a className="ui-btn ui-btn--primary ui-btn--sm" href={site.loginUrl}>Masuk</a>
                    </div>
                )}
                <button className="mobile-toggle" onClick={onToggle} aria-label="Menu">
                    <span className={`hamburger ${open ? 'is-open' : ''}`} />
                </button>
            </div>
            {open && (
                <div className="mobile-nav">
                    {(menus || []).map(menu => (
                        <NavbarMenuItem key={menu.id} item={menu} onToggle={onToggle} isMobile />
                    ))}
                    {showLogin && site?.loginUrl && <a href={site.loginUrl} className="ui-btn ui-btn--primary">Masuk</a>}
                </div>
            )}
        </header>
    );
}
