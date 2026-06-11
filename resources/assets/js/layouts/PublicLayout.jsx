import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { GraduationCap, Menu, X } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';

export function SiteLayout({ children, title, site, menus, template, preview = false, seo = {} }) {
    const [open, setOpen] = React.useState(false);
    const pageTitle = seo?.title || (title ? `${title} - ${site.name}` : site.name);

    return <div className={`theme-${template}`}>
        <Head title={pageTitle}>
            {seo?.description && <meta head-key="description" name="description" content={seo.description} />}
            {seo?.keywords && <meta head-key="keywords" name="keywords" content={seo.keywords} />}
            {site.favicon && <link rel="icon" href={site.favicon} />}
        </Head>
        <header className="site-header">
            <div className="shell nav-wrap">
                <Link href={site.homeUrl} className="brand">
                    {site.logo
                        ? <img src={site.logo} alt={site.name} className="brand-logo" />
                        : <span className="brand-mark"><GraduationCap size={24} /></span>}
                    <span>{site.name}</span>
                </Link>
                <nav className="desktop-nav">
                    {menus.map(menu => menu.target === '_blank'
                        ? <a key={menu.id} href={menu.url} target="_blank" rel="noreferrer">{menu.title}</a>
                        : <Link key={menu.id} href={menu.url}>{menu.title}</Link>)}
                </nav>
                <div className="nav-actions">
                    {preview && <Badge>Mode Pratinjau</Badge>}
                    <Button asChild><a href={site.loginUrl}>Masuk</a></Button>
                    <button className="mobile-toggle" onClick={() => setOpen(!open)} aria-label="Buka navigasi">{open ? <X /> : <Menu />}</button>
                </div>
            </div>
            {open && <nav className="mobile-nav shell">{menus.map(menu => <Link key={menu.id} href={menu.url} onClick={() => setOpen(false)}>{menu.title}</Link>)}</nav>}
        </header>
        {children}
        <footer className="site-footer"><div className="shell footer-grid">
            <div><div className="brand brand--footer"><span className="brand-mark"><GraduationCap size={24} /></span><span>{site.name}</span></div><p>{site.tagline}</p></div>
            <div><strong>Navigasi</strong><Link href={site.homeUrl}>Beranda</Link><Link href={site.contactUrl}>Hubungi Kami</Link></div>
            <div><strong>Kontak</strong>{site.address && <span>{site.address}</span>}{site.email && <a href={`mailto:${site.email}`}>{site.email}</a>}<span>&copy; {new Date().getFullYear()} {site.name}</span></div>
        </div></footer>
    </div>;
}

export function TemplatePicker({ template }) {
    const labels = {
        institutional: 'Institusional',
        modern: 'Modern',
        editorial: 'Editorial',
        corporate: 'Corporate',
        launch: 'Launch UI',
    };
    return <div className="template-picker"><span>Pratinjau:</span>{Object.entries(labels).map(([key, label]) =>
        <a key={key} className={template === key ? 'active' : ''} href={`?template=${key}`}>{label}</a>)}</div>;
}
