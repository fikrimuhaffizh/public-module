import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { Button } from '@public/components/ui/button';

function isSectionActive(sections, key) {
    const section = sections?.find(s => s.section_key === key);
    return section ? section.is_active : true; // default to true if section not found
}

/**
 * Persistent layout — reads all shared props from usePage().
 * Pages only need to attach: Page.layout = PublicPageLayout
 */
export function PublicPageLayout({ children }) {
    const { site, menus, footerMenus, template, seo = {}, sections = [] } = usePage().props;
    const [open, setOpen] = React.useState(false);
    const siteName = site?.name || '';
    const pageDesc = seo?.description || site?.tagline || '';
    const showHeader = isSectionActive(sections, 'navbar');
    const showFooter = isSectionActive(sections, 'footer');

    return <div className={`theme-${template}`}>
        <Head>
            {seo?.keywords && <meta head-key="keywords" name="keywords" content={seo.keywords} />}
            {site?.favicon && <link rel="icon" href={site.favicon} />}
            <meta head-key="og:type" property="og:type" content="website" />
            <meta head-key="og:site_name" property="og:site_name" content={siteName} />
            {site?.logo && <meta head-key="og:image" property="og:image" content={site.logo} />}
            <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        </Head>
        {showHeader && (
            <header className="site-header">
                <div className="shell nav-wrap">
                    <Link href={site.homeUrl} className="brand">
                        {site.logo
                            ? <img src={site.logo} alt={siteName} className="brand-logo" />
                            : <span className="brand-mark"><GraduationCap size={24} /></span>}
                    </Link>
                    <nav className="desktop-nav">
                        {(menus || []).map(menu => menu.target === '_blank'
                            ? <a key={menu.id} href={menu.url} target="_blank" rel="noreferrer">{menu.title}</a>
                            : <Link key={menu.id} href={menu.url}>{menu.title}</Link>)}
                    </nav>
                    <div className="nav-actions">
                        <Button asChild><a href={site.loginUrl}>Masuk</a></Button>
                        <button className="mobile-toggle" onClick={() => setOpen(!open)} aria-label="Buka navigasi">{open ? <X /> : <Menu />}</button>
                    </div>
                </div>
                {open && <nav className="mobile-nav shell">{(menus || []).map(menu => <Link key={menu.id} href={menu.url} onClick={() => setOpen(false)}>{menu.title}</Link>)}</nav>}
            </header>
        )}
        {children}
        {showFooter && (
            <footer className="site-footer">
                <div className="shell footer-grid">
                    <div>
                        <div className="brand brand--footer">
                            {site.logo
                                ? <img src={site.logo} alt={siteName} className="brand-logo" />
                                : <span className="brand-mark"><GraduationCap size={24} /></span>}
                            <span>{siteName}</span>
                        </div>
                        <p>{site?.tagline}</p>
                    </div>
                    <div>
                        <strong>Navigasi</strong>
                        <Link href={site.homeUrl}>Beranda</Link>
                        {(footerMenus || []).map(m =>
                            m.target === '_blank'
                                ? <a key={m.id} href={m.url} target="_blank" rel="noreferrer">{m.title}</a>
                                : <Link key={m.id} href={m.url}>{m.title}</Link>
                        )}
                        <Link href={site.contactUrl}>Hubungi Kami</Link>
                    </div>
                    <div>
                        <strong>Kontak</strong>
                        {site?.address && <span>{site.address}</span>}
                        {site?.email && <a href={`mailto:${site.email}`}>{site.email}</a>}
                        <span>&copy; {new Date().getFullYear()} {siteName}</span>
                    </div>
                </div>
            </footer>
        )}
    </div>;
}

// Backwards-compatible named export (delegates to PublicPageLayout)
export function SiteLayout({ children }) {
    return <PublicPageLayout>{children}</PublicPageLayout>;
}

export function TemplatePicker({ template }) {
    const labels = {
        modern: 'Modern',
        editorial: 'Editorial',
        corporate: 'Corporate',
        launch: 'Launch UI',
        aurora: 'Aurora',
        enterprise: 'Enterprise',
        registration: 'Registration',
        profile: 'Profile',
    };
    return <div className="template-picker"><span>Pratinjau:</span>{Object.entries(labels).map(([key, label]) =>
        <a key={key} className={template === key ? 'active' : ''} href={`?template=${key}`}>{label}</a>)}</div>;
}
