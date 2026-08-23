import React, { useMemo } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { Calendar, Clock, User, ArrowLeft } from 'lucide-react';

// Simple SVG social icons (lucide-react doesn't have brand icons)
const FacebookIcon = () => (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
    </svg>
);

const TwitterIcon = () => (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z" />
    </svg>
);

const LinkedinIcon = () => (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
        <rect x="2" y="9" width="4" height="12" />
        <circle cx="4" cy="4" r="2" />
    </svg>
);
import { Button } from '@public/components/ui/button';
import { Reveal } from '@public/components/motion/effects';
import { PublicPageLayout } from '@public/layouts/PublicLayout';
import { NewsGrid } from '@public/components/sections/LandingSections';

/**
 * NewsDetail — detail page untuk berita/pengumuman (/news/{pengumuman}).
 * 
 * Layout options:
 * - default: contained width (max-width 780px)
 * - wide: wider content (max-width 1024px)
 * - sidebar: content + sidebar (TOC, related, share)
 */
export default function NewsDetail() {
    const { site, announcement, related } = usePage().props;
    
    const layout = announcement?.content_layout || 'default';
    const contentWidth = announcement?.content_width || 'default';
    const contentBg = announcement?.content_bg || null;
    
    // Generate Table of Contents from headings
    const toc = useMemo(() => {
        if (!announcement?.content || layout !== 'sidebar') return [];
        const headings = [];
        const regex = /<h([2-3])[^>]*id="([^"]*)"[^>]*>(.*?)<\/h\1>/gi;
        let match;
        while ((match = regex.exec(announcement.content)) !== null) {
            headings.push({
                level: parseInt(match[1]),
                id: match[2],
                text: match[3].replace(/<[^>]+>/g, ''),
            });
        }
        return headings;
    }, [announcement?.content, layout]);
    
    // Share URLs
    const shareUrl = typeof window !== 'undefined' ? window.location.href : '';
    const shareText = encodeURIComponent(announcement?.title || '');
    
    const contentWidthClass = {
        narrow: 'shell--narrow',
        default: 'shell--article',
        wide: 'shell--wide',
    }[contentWidth] || 'shell--article';
    
    const layoutClass = `detail-layout detail-layout--${layout}`;
    
    return (
        <>
            <Head title={`${announcement.title} - ${site.name}`}>
                <meta head-key="description" name="description" content={announcement.excerpt || ''} />
                <meta head-key="og:title" property="og:title" content={`${announcement.title} - ${site.name}`} />
                {announcement.image && <meta head-key="og:image" property="og:image" content={announcement.image} />}
            </Head>
            
            <main className={`inner-page ${layoutClass}`} style={contentBg ? { background: contentBg } : undefined}>
                <div className="shell detail-layout__inner">
                    {/* Main Content */}
                    <article className={contentWidthClass}>
                        <Reveal>
                            {/* Article Meta */}
                            <div className="article-meta">
                                {announcement.type && (
                                    <span className="article-meta__type">{announcement.type}</span>
                                )}
                                {announcement.date && (
                                    <span className="article-meta__date">
                                        <Calendar size={14} />
                                        {announcement.date}
                                    </span>
                                )}
                                {announcement.author && (
                                    <span className="article-meta__author">
                                        <User size={14} />
                                        {announcement.author}
                                    </span>
                                )}
                                {announcement.reading_time && (
                                    <span className="article-meta__reading">
                                        <Clock size={14} />
                                        {announcement.reading_time} menit baca
                                    </span>
                                )}
                            </div>
                            
                            {/* Cover Image */}
                            {announcement.image && (
                                <img
                                    className="article-cover"
                                    src={announcement.image}
                                    alt={announcement.title}
                                    loading="eager"
                                />
                            )}
                            
                            {/* Content */}
                            <div
                                className="prose-content"
                                dangerouslySetInnerHTML={{ __html: announcement.content }}
                            />
                        </Reveal>
                        
                        {/* Navigation Footer */}
                        <div className="dynamic-page-footer">
                            <Button variant="outline" asChild>
                                <Link href={route('public.announcements.index')}>
                                    <ArrowLeft size={17} /> Kembali ke berita
                                </Link>
                            </Button>
                            <div className="article-share-inline">
                                <span className="article-share-inline__label">Bagikan:</span>
                                <a
                                    href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    title="Facebook"
                                >
                                    <FacebookIcon />
                                </a>
                                <a
                                    href={`https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${shareText}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    title="Twitter"
                                >
                                    <TwitterIcon />
                                </a>
                                <a
                                    href={`https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(shareUrl)}&title=${shareText}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    title="LinkedIn"
                                >
                                    <LinkedinIcon />
                                </a>
                            </div>
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
                            
                            {/* Related News */}
                            {related && related.length > 0 && (
                                <div className="sidebar-card">
                                    <h4 className="sidebar-card__title">Berita Lainnya</h4>
                                    <div className="sidebar-related">
                                        {related.map((item, i) => (
                                            <Link key={i} href={item.url} className="sidebar-related__item">
                                                {item.image && (
                                                    <img src={item.image} alt={item.title} className="sidebar-related__img" />
                                                )}
                                                <div className="sidebar-related__content">
                                                    <span className="sidebar-related__type">{item.type}</span>
                                                    <span className="sidebar-related__title">{item.title}</span>
                                                    <span className="sidebar-related__date">{item.date}</span>
                                                </div>
                                            </Link>
                                        ))}
                                    </div>
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
                        </aside>
                    )}
                </div>
            </main>
        </>
    );
}

NewsDetail.layout = PublicPageLayout;
