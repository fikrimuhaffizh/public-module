import React, { useMemo } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, ExternalLink } from 'lucide-react';

// Simple SVG social icons (lucide-react doesn't have brand icons)
const FacebookIcon = () => (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
    </svg>
);

const TwitterIcon = () => (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z" />
    </svg>
);

const LinkedinIcon = () => (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
        <rect x="2" y="9" width="4" height="12" />
        <circle cx="4" cy="4" r="2" />
    </svg>
);
import { Button } from '@public/components/ui/button';
import { Reveal } from '@public/components/motion/effects';
import { PublicPageLayout } from '@public/layouts/PublicLayout';

/**
 * ContentPage — detail page untuk halaman statis (/page/{slug}).
 * 
 * Layout options:
 * - default: contained width (max-width 780px)
 * - wide: wider content (max-width 1024px)
 * - sidebar: content + sidebar (TOC, related, share)
 */
export default function ContentPage() {
    const { site, page } = usePage().props;
    
    const layout = page?.content_layout || 'default';
    const contentWidth = page?.content_width || 'default';
    const contentBg = page?.content_bg || null;
    
    // Generate Table of Contents from headings
    const toc = useMemo(() => {
        if (!page?.content || layout !== 'sidebar') return [];
        const headings = [];
        const regex = /<h([2-3])[^>]*id="([^"]*)"[^>]*>(.*?)<\/h\1>/gi;
        let match;
        while ((match = regex.exec(page.content)) !== null) {
            headings.push({
                level: parseInt(match[1]),
                id: match[2],
                text: match[3].replace(/<[^>]+>/g, ''),
            });
        }
        return headings;
    }, [page?.content, layout]);
    
    // Share URLs
    const shareUrl = typeof window !== 'undefined' ? window.location.href : '';
    const shareText = encodeURIComponent(page?.title || '');
    
    const contentWidthClass = {
        narrow: 'shell--narrow',
        default: 'shell--article',
        wide: 'shell--wide',
    }[contentWidth] || 'shell--article';
    
    const layoutClass = `detail-layout detail-layout--${layout}`;
    
    return (
        <>
            <Head title={`${page.title} - ${site.name}`}>
                <meta head-key="description" name="description" content={page.excerpt || ''} />
                <meta head-key="og:title" property="og:title" content={`${page.title} - ${site.name}`} />
                {page.excerpt && <meta head-key="og:description" property="og:description" content={page.excerpt} />}
            </Head>
            
            <main className={`dynamic-page ${layoutClass}`} style={contentBg ? { background: contentBg } : undefined}>
                <div className="shell detail-layout__inner">
                    {/* Main Content */}
                    <article className={contentWidthClass}>
                        <Reveal>
                            <div
                                className="prose-content"
                                dangerouslySetInnerHTML={{ __html: page.content }}
                            />
                        </Reveal>
                        
                        {/* Navigation Footer */}
                        <div className="dynamic-page-footer">
                            <Button variant="outline" asChild>
                                <Link href={site.homeUrl}><ArrowLeft size={17} /> Kembali ke beranda</Link>
                            </Button>
                            <Button asChild>
                                <Link href={site.contactUrl}>Hubungi kami <ArrowRight size={17} /></Link>
                            </Button>
                        </div>
                    </article>
                    
                    {/* Sidebar (only for sidebar layout) */}
                    {layout === 'sidebar' && (
                        <aside className="detail-sidebar">
                            {/* Table of Contents */}
                            {toc.length > 0 && (
                                <div className="sidebar-card">
                                    <h4 className="sidebar-card__title">Daftar Isi</h4>
                                    <nav className="sidebar-toc">
                                        {toc.map((item, i) => (
                                            <a
                                                key={i}
                                                href={`#${item.id}`}
                                                className={`sidebar-toc__link sidebar-toc__link--h${item.level}`}
                                            >
                                                {item.text}
                                            </a>
                                        ))}
                                    </nav>
                                </div>
                            )}
                            
                            {/* Share */}
                            <div className="sidebar-card">
                                <h4 className="sidebar-card__title">Bagikan</h4>
                                <div className="sidebar-share">
                                    <a
                                        href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="sidebar-share__link"
                                        title="Facebook"
                                    >
                                        <FacebookIcon />
                                    </a>
                                    <a
                                        href={`https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${shareText}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="sidebar-share__link"
                                        title="Twitter"
                                    >
                                        <TwitterIcon />
                                    </a>
                                    <a
                                        href={`https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(shareUrl)}&title=${shareText}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="sidebar-share__link"
                                        title="LinkedIn"
                                    >
                                        <LinkedinIcon />
                                    </a>
                                </div>
                            </div>
                            
                            {/* CTA */}
                            <div className="sidebar-card sidebar-card--cta">
                                <h4 className="sidebar-card__title">Butuh bantuan?</h4>
                                <p className="sidebar-card__text">Hubungi kami untuk informasi lebih lanjut.</p>
                                <Button asChild size="sm" className="w-full">
                                    <Link href={site.contactUrl}>Hubungi Kami</Link>
                                </Button>
                            </div>
                        </aside>
                    )}
                </div>
            </main>
        </>
    );
}

ContentPage.layout = PublicPageLayout;
