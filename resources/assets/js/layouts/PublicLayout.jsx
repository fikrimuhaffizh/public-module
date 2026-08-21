import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import {
    ThemeCustomizerProvider,
    ThemeSettingsDrawer,
    useThemeCustomizer,
} from '@public/components/theme/ThemeCustomizer';
import { sectionColorStyle, useSectionVariant } from '@public/components/sections/renderer';
import FloatingWhatsApp from '@public/components/FloatingWhatsApp';
import { usePauseOffscreenAnimations } from '@public/lib/pauseOffscreen';
import { resolveVariant } from '@public/components/sections/registry';


function isSectionActive(sections, key) {
    const section = sections?.find(s => s.section_key === key);
    return section ? section.is_active : true; // default to true if section not found
}

function sectionVariantOf(sections, key, fallback) {
    const section = sections?.find(s => s.section_key === key);
    return section?.variant || fallback;
}

/**
 * Persistent layout — reads all shared props from usePage().
 * Pages only need to attach: Page.layout = PublicPageLayout
 */
export function PublicPageLayout({ children }) {
    usePauseOffscreenAnimations();
    return (
        <ThemeCustomizerProvider>
            <ThemedRoot>{children}</ThemedRoot>
        </ThemeCustomizerProvider>
    );
}

/**
 * Root bertema — konsumsi state customizer (CSS vars warna/font + kelas
 * struktur navbar/kartu/tombol) lalu render header, konten, dan footer.
 * Navbar & footer dirender dari komponen variant section (registry.js) —
 * ganti variant-nya live dari Theme Settings di /preview.
 */
function ThemedRoot({ children }) {
    const { site, menus, footerMenus, template, seo = {}, sections = [], page = null, announcement = null, header = null } = usePage().props;
    const customizer = useThemeCustomizer();
    const [open, setOpen] = React.useState(false);
    const siteName = site?.name || '';
    const showHeader = customizer?.sectionSettings?.navbar?.active ?? isSectionActive(sections, 'navbar');
    const showFooter = customizer?.sectionSettings?.footer?.active ?? isSectionActive(sections, 'footer');

    const customClass = customizer?.appliedClasses || '';
    const customVars = { ...(customizer?.appliedVars || {}), ...(customizer?.darkVars || {}) };

    // Kelas wrapper override warna/pola/gambar per-section (layout-level).
    const secWrapClasses = (colors) => {
        const hasBg = Boolean(colors?.bg || colors?.pattern || colors?.image);
        return `sec-colored${hasBg ? ' sec-colored--bg' : ''}${colors?.pattern ? ` sec-colored--p-${colors.pattern}` : ''}${colors?.image ? ' sec-colored--img' : ''}`;
    };

    // Footer pakai variant section (bisa diganti live); navbar tetap satu gaya klasik.
    const navbarKey = useSectionVariant('navbar', sectionVariantOf(sections, 'navbar', 'navbar_1'));
    const NavbarComponent = resolveVariant('navbar', navbarKey)?.component;
    const navbarStyle = sectionColorStyle(customizer?.sectionColors?.['navbar']);
    const navbarSection = sections?.find(s => s.section_key === 'navbar');
    const navbarSettings = { ...(navbarSection?.settings || {}), ...(customizer?.sectionSettings?.navbar || {}) };

    const footerKey = useSectionVariant('footer', sectionVariantOf(sections, 'footer', 'footer_1'));
    const FooterComponent = resolveVariant('footer', footerKey)?.component;
    const footerStyle = sectionColorStyle(customizer?.sectionColors?.['footer']);

    // Top Bar — section layout-level (variant + warna dari offcanvas).
    const topbarKey = useSectionVariant('topbar', sectionVariantOf(sections, 'topbar', 'topbar_1'));
    const TopBarComponent = resolveVariant('topbar', topbarKey)?.component;
    const topbarSection = sections?.find(s => s.section_key === 'topbar');
    const topbarActive = topbarSection
        ? (customizer?.sectionSettings?.topbar?.active ?? topbarSection.is_active)
        : false;
    const topbarStyle = sectionColorStyle(customizer?.sectionColors?.['topbar']);
    const topbarSettings = { ...(topbarSection?.settings || {}), ...(customizer?.sectionSettings?.topbar || {}) };

    // Page Header — section layout-level untuk halaman dalam (berita detail,
    // halaman statis, berita, kontak). Konten diturunkan dari props halaman;
    // home tidak punya konteks → tidak dirender. Mode/warna/toggle dari
    // Theme Settings (offcanvas), sama seperti navbar/topbar/footer.
    // Color props: pretitleColor, titleColor, subtitleColor bisa diatur per-halaman.
    const pageHeaderContext = page
        ? { eyebrow: page.eyebrow || 'Informasi institusi', title: page.title, excerpt: page.excerpt, crumb: page.title, pretitleColor: page.pretitle_color || null, titleColor: page.title_color || null, subtitleColor: page.subtitle_color || null }
        : announcement
            ? { eyebrow: announcement.type || 'Pengumuman', title: announcement.title, excerpt: announcement.excerpt, crumb: announcement.type || 'Pengumuman', pretitleColor: announcement.pretitle_color || null, titleColor: announcement.title_color || null, subtitleColor: announcement.subtitle_color || null }
            : header || null;
    const pageheaderKey = useSectionVariant('pageheader', sectionVariantOf(sections, 'pageheader', 'pageheader_1'));
    const PageHeaderComponent = resolveVariant('pageheader', pageheaderKey)?.component;
    const pageheaderSection = sections?.find(s => s.section_key === 'pageheader');
    const pageheaderActive = pageheaderSection
        ? (customizer?.sectionSettings?.pageheader?.active ?? pageheaderSection.is_active)
        : true;
    const pageheaderStyle = sectionColorStyle(customizer?.sectionColors?.['pageheader']);

    return <div className={`theme-${template} ${customClass}`} style={{ ...customVars, fontFamily: "var(--font-body)", color: "var(--foreground)", background: "var(--background)" }}>
        <Head>
            <title>{site?.title || siteName}</title>
            {seo?.description && <meta head-key="description" name="description" content={seo.description} />}
            {site?.description && <meta head-key="og:description" property="og:description" content={site.description} />}
            {seo?.keywords && <meta head-key="keywords" name="keywords" content={seo.keywords} />}
            {site?.favicon && <link rel="icon" href={site.favicon} />}
            <meta head-key="og:type" property="og:type" content="website" />
            <meta head-key="og:site_name" property="og:site_name" content={siteName} />
            {site?.logo && <meta head-key="og:image" property="og:image" content={site.logo} />}
            <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        </Head>
        {topbarActive && TopBarComponent && (
            topbarStyle
                ? <div className={secWrapClasses(customizer?.sectionColors?.topbar)} style={topbarStyle}><TopBarComponent site={site} settings={topbarSettings} /></div>
                : <TopBarComponent site={site} settings={topbarSettings} />
        )}
        {showHeader && NavbarComponent && (
            navbarStyle
                ? <div className={secWrapClasses(customizer?.sectionColors?.navbar)} style={navbarStyle}><NavbarComponent site={site} menus={menus} open={open} onToggle={() => setOpen(!open)} settings={navbarSettings} /></div>
                : <NavbarComponent site={site} menus={menus} open={open} onToggle={() => setOpen(!open)} settings={navbarSettings} />
        )}
        {pageheaderActive && PageHeaderComponent && pageHeaderContext && (
            pageheaderStyle
                ? <div className={secWrapClasses(customizer?.sectionColors?.pageheader)} style={pageheaderStyle}><PageHeaderComponent context={pageHeaderContext} site={site} /></div>
                : <PageHeaderComponent context={pageHeaderContext} site={site} />
        )}
        {children}
        {showFooter && FooterComponent && (
            footerStyle
                ? <div className={secWrapClasses(customizer?.sectionColors?.footer)} style={footerStyle}><FooterComponent site={site} footerMenus={footerMenus} /></div>
                : <FooterComponent site={site} footerMenus={footerMenus} />
        )}
        <FloatingWhatsApp />
        <ThemeSettingsDrawer />
    </div>;
}

// Backwards-compatible named export (delegates to PublicPageLayout)
export function SiteLayout({ children }) {
    return <PublicPageLayout>{children}</PublicPageLayout>;
}
