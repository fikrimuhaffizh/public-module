import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    BarChart3,
    BookOpen,
    Building2,
    CalendarDays,
    Check,
    GraduationCap,
    LayoutDashboard,
    ShieldCheck,
    Sparkles,
    Star,
    Users,
    Workflow,
} from 'lucide-react';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@public/components/ui/accordion';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';

export function sectionKey(section) {
    const aliases = {
        products: 'product',
        stats: 'statistic',
        features: 'feature',
        testimonials: 'testimonial',
        clients: 'client',
        announcement: 'pengumuman',
    };

    return aliases[section?.section_key] || section?.section_key;
}

export function sectionText(section, field, fallback = '') {
    return section?.[field] || fallback;
}

/** Demo paket harga — dipakai bila section price belum punya data di settings. */
const DEFAULT_PRICE_PACKAGES = [
    {
        name: 'Starter',
        description: 'Untuk memulai kehadiran digital.',
        price: '99.000',
        period: 'bulan',
        features: ['Halaman profil bisnis', 'Kontak & lokasi', 'Hingga 3 produk'],
        ctaText: 'Pilih Starter',
        ctaLink: '#kontak',
    },
    {
        name: 'Bisnis',
        description: 'Paling populer untuk usaha berkembang.',
        price: '249.000',
        period: 'bulan',
        features: ['Semua fitur Starter', 'Daftar harga & paket', 'Foto produk tanpa batas', 'Dukungan prioritas'],
        ctaText: 'Pilih Bisnis',
        ctaLink: '#kontak',
        highlight: true,
    },
    {
        name: 'Premium',
        description: 'Untuk brand yang ingin tampil penuh.',
        price: '499.000',
        period: 'bulan',
        features: ['Semua fitur Bisnis', 'Navigasi & halaman custom', 'WhatsApp terintegrasi', 'Pendampingan desain'],
        ctaText: 'Pilih Premium',
        ctaLink: '#kontak',
    },
];




/**
 * Baca daftar paket harga dari section.settings.packages (array JSON yang
 * dikelola lewat CMS Section → Harga), fallback ke demo bila kosong.
 * Normalisasi: harga bisa string ("99.000") atau angka (99000).
 */
export function pricePackages(section, pricingData) {
    //优先使用 Pricing model data dari CMS CRUD
    if (Array.isArray(pricingData) && pricingData.length) {
        return pricingData.map(pkg => ({
            name: pkg.name || 'Paket',
            description: pkg.description || '',
            price: pkg.price == null ? '0' : String(pkg.price),
            period: pkg.period || '',
            features: Array.isArray(pkg.features) ? pkg.features : [],
            highlight: Boolean(pkg.highlight),
            ctaText: 'Pilih paket',
            ctaLink: '#kontak',
        }));
    }
    // Fallback ke JSON settings
    const raw = section?.settings?.packages;
    if (Array.isArray(raw) && raw.length) {
        return raw.map(pkg => ({
            name: pkg.name || 'Paket',
            description: pkg.description || '',
            price: pkg.price == null ? '0' : String(pkg.price),
            period: pkg.period || '',
            features: Array.isArray(pkg.features) ? pkg.features : [],
            highlight: Boolean(pkg.highlight),
            ctaText: pkg.ctaText || 'Pilih paket',
            ctaLink: pkg.ctaLink || '#kontak',
        }));
    }
    return DEFAULT_PRICE_PACKAGES;
}
export function combinedText(section, fallback = '') {
    const subtitle = section?.subtitle?.trim();
    const postTitle = section?.post_title?.trim();
    if (subtitle && postTitle) return `${subtitle} — ${postTitle}`;
    return subtitle || postTitle || fallback;
}

export function sectionHeading(section, defaults = {}) {
    return {
        eyebrow: sectionText(section, 'pre_title', defaults.eyebrow || ''),
        title: sectionText(section, 'title', defaults.title || ''),
        text: combinedText(section, defaults.text || ''),
        align: section?.settings?.text_align || defaults.align || 'left',
    };
}

export function headingAlignClass(section, defaults = {}) {
    const align = sectionHeading(section, defaults).align;
    return ['left', 'center', 'right'].includes(align) ? `section-heading--${align}` : 'section-heading--left';
}

export function heroCopy(section, hero, site) {
    const heading = sectionHeading(section, {
        title: hero?.title || site.name,
        text: hero?.description || site.tagline,
    });

    return {
        title: heading.title,
        eyebrow: heading.eyebrow,
        subtitle: combinedText(section, hero?.subtitle || hero?.description || site.tagline || ''),
        imageAlt: heading.title,
        align: heading.align,
        alignClass: headingAlignClass(section),
    };
}

export function Section({
    section,
    id,
    eyebrow,
    title,
    text,
    children,
    dark = false,
    tint = false,
    narrow = false,
    align = 'left',
}) {
    const heading = sectionHeading(section, { eyebrow, title, text, align });
    const classes = [
        'section',
        dark ? 'section--dark' : '',
        tint ? 'section--tint' : '',
    ].filter(Boolean).join(' ');

    // Per-element color: --sec-pretext, --sec-title, --sec-posttext
    // (di-set oleh sectionColorStyle di renderer.jsx)
    return (
        <section id={id} className={classes}>
            <div className={`shell ${narrow ? 'shell--narrow' : ''}`}>
                <Reveal className={`section-heading section-heading--${heading.align}`}>
                    {heading.eyebrow && <span className="eyebrow" style={{ color: 'var(--sec-pretext, inherit)' }}>{heading.eyebrow}</span>}
                    {heading.title && <h2 style={{ color: 'var(--sec-title, inherit)' }}>{heading.title}</h2>}
                    {heading.text && <p style={{ color: 'var(--sec-posttext, inherit)' }}>{heading.text}</p>}
                </Reveal>
                {children}
            </div>
        </section>
    );
}

export function SectionHeader({ section, className = '' }) {
    const heading = sectionHeading(section);
    if (!heading.eyebrow && !heading.title && !heading.text) return null;

    return (
        <Reveal className={`section-heading section-heading--${heading.align} ${className}`}>
            {heading.eyebrow && <span className="eyebrow" style={{ color: 'var(--sec-pretext, inherit)' }}>{heading.eyebrow}</span>}
            {heading.title && <h2 style={{ color: 'var(--sec-title, inherit)' }}>{heading.title}</h2>}
            {heading.text && <p style={{ color: 'var(--sec-posttext, inherit)' }}>{heading.text}</p>}
        </Reveal>
    );
}

export function PagesGrid({ pages, section }) {
    const icons = [Building2, GraduationCap, BookOpen, Users, ShieldCheck, CalendarDays];
    const limit = section?.limit_data || 6;

    return (
        <Stagger className="feature-grid">
            {pages.slice(0, limit).map((page, index) => {
                const Icon = icons[index % icons.length];

                return (
                    <SpotlightCard key={page.id} className="feature-card">
                        <span className="feature-icon"><Icon /></span>
                        <h3>{page.title}</h3>
                        <p>{page.excerpt}</p>
                        <Link className="text-link" href={page.url}>
                            Pelajari <ArrowRight size={16} />
                        </Link>
                    </SpotlightCard>
                );
            })}
        </Stagger>
    );
}

export function NewsGrid({ announcements, section, editorial = false }) {
    const gridClass = editorial ? 'news-grid news-grid--editorial' : 'news-grid';
    const limit = section?.limit_data || 6;

    return (
        <Stagger className={gridClass}>
            {announcements.slice(0, limit).map((item, index) => (
                <SpotlightCard
                    key={item.id}
                    className={index === 0 && editorial ? 'news-card news-card--lead' : 'news-card'}
                >
                    <Link href={item.url}>
                        <img src={item.image} alt={item.title} className="news-image" />
                    </Link>
                    <div className="news-content">
                        <div className="news-meta">
                            <Badge>{item.type}</Badge>
                            <span>{item.date}</span>
                        </div>
                        <h3><Link href={item.url}>{item.title}</Link></h3>
                        <p>{item.excerpt}</p>
                        <Link className="text-link" href={item.url}>
                            Baca selengkapnya <ArrowRight size={16} />
                        </Link>
                    </div>
                </SpotlightCard>
            ))}
        </Stagger>
    );
}

export function PlatformOverview({ site, image, pageCount = 0, section }) {
    const heading = sectionHeading(section, {
        eyebrow: 'Platform institusi digital',
        title: 'Satu pengalaman digital untuk seluruh ekosistem kampus',
        text: combinedText(section, `${site.name} menghadirkan akses informasi yang cepat, rapi, dan mudah digunakan oleh mahasiswa, tenaga pendidik, serta masyarakat.`),
    });
    const capabilities = [
        [LayoutDashboard, 'Satu ruang kerja', 'Informasi dan layanan kampus tersaji dalam pengalaman yang konsisten.'],
        [Workflow, 'Alur terhubung', 'Konten publik terhubung langsung dengan pengelolaan data institusi.'],
        [BarChart3, 'Berbasis data', 'Bangun kepercayaan melalui informasi yang terukur dan selalu relevan.'],
    ];

    return (
        <section className="saas-overview">
            <div className="shell saas-overview-grid">
                <Reveal className="saas-copy">
                    <Badge variant="secondary" style={{ color: 'var(--sec-pretext, inherit)' }}><Sparkles size={14} /> {heading.eyebrow}</Badge>
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{heading.title}</h2>
                    <p style={{ color: 'var(--sec-posttext, inherit)' }}>{heading.text}</p>
                    <div className="capability-list">
                        {capabilities.map(([Icon, title, description]) => (
                            <div className="capability-item" key={title}>
                                <span><Icon size={20} /></span>
                                <div>
                                    <strong>{title}</strong>
                                    <p>{description}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </Reveal>
                <Reveal className="product-preview" delay={0.12}>
                    <div className="product-preview-bar">
                        <i /><i /><i />
                        <span>{site.name}</span>
                    </div>
                    <div className="product-preview-body">
                        <div className="product-preview-sidebar">
                            {Array.from({ length: 5 }).map((_, index) => <span key={index} />)}
                        </div>
                        <div className="product-preview-content">
                            <div className="product-preview-heading">
                                <div><span /><strong /></div>
                                <Badge>{pageCount}+ informasi</Badge>
                            </div>
                            {image && <img src={image} alt={`Pratinjau ${site.name}`} />}
                            <div className="product-preview-stats">
                                <span /><span /><span />
                            </div>
                        </div>
                    </div>
                </Reveal>
            </div>
        </section>
    );
}

export function ValueStrip() {
    const values = [
        'Implementasi cepat',
        'Responsif di semua perangkat',
        'Konten dikelola dari CMS',
        'Pengalaman pengguna konsisten',
    ];

    return (
        <div className="value-strip">
            <div className="shell value-strip-inner">
                {values.map(value => (
                    <span key={value}><Check size={16} /> {value}</span>
                ))}
            </div>
        </div>
    );
}


export function TestimonialRating({ rating, size = 16 }) {
    const count = Math.min(Math.max(parseInt(rating, 10) || 5, 1), 5);

    return (
        <div className="testimonial-rating" aria-label={`${count} dari 5 bintang`}>
            {Array.from({ length: count }).map((_, index) => (
                <Star key={index} size={size} fill="currentColor" aria-hidden="true" />
            ))}
        </div>
    );
}
export function TestimonialSection({ testimonials, section }) {
    if (!testimonials?.length) return null;
    const limit = section?.limit_data || 6;
    const heading = sectionHeading(section, {
        eyebrow: 'Cerita dari komunitas',
        title: 'Pengalaman nyata dalam ekosistem kami',
        text: 'Kepercayaan dibangun dari layanan yang konsisten dan pengalaman yang bermakna.',
    });

    return (
        <Section
            section={section}
            tint
            eyebrow={heading.eyebrow}
            title={heading.title}
            text={heading.text}
        >
            <Stagger className="testimonial-grid">
                {testimonials.slice(0, limit).map(testimonial => (
                    <SpotlightCard key={testimonial.id} className="testimonial-card">
                        <TestimonialRating rating={testimonial.rating} />
                        <blockquote>“{testimonial.quote}”</blockquote>
                        <div className="testimonial-person">
                            {testimonial.photo
                                ? <img src={testimonial.photo} alt={testimonial.name} />
                                : <span>{testimonial.name.slice(0, 2).toUpperCase()}</span>}
                            <div>
                                <strong>{testimonial.name}</strong>
                                <small>{[testimonial.position, testimonial.organization].filter(Boolean).join(' · ')}</small>
                            </div>
                        </div>
                    </SpotlightCard>
                ))}
            </Stagger>
        </Section>
    );
}

export function CtaSection({ site, section }) {
    const heading = sectionHeading(section, {
        eyebrow: 'Mulai terhubung',
        title: `Temukan informasi dan layanan ${site.name} dalam satu tempat.`,
    });

    return (
        <section className="saas-cta">
            <div className="shell">
                <Reveal className="saas-cta-card">
                    <div>
                        {heading.eyebrow && <span className="eyebrow" style={{ color: 'var(--sec-pretext, inherit)' }}>{heading.eyebrow}</span>}
                        <h2 style={{ color: 'var(--sec-title, inherit)' }}>{heading.title}</h2>
                        {heading.text && <p style={{ color: 'var(--sec-posttext, inherit)' }}>{heading.text}</p>}
                    </div>
                    <Button asChild size="lg">
                        <Link href={site.contactUrl}>
                            Hubungi kami <ArrowRight size={18} />
                        </Link>
                    </Button>
                </Reveal>
            </div>
        </section>
    );
}

export function FaqSection({ faqs }) {
    return (
        <Accordion type="single" collapsible className="faq-list">
            {faqs.map(faq => (
                <AccordionItem key={faq.id} value={`faq-${faq.id}`}>
                    <AccordionTrigger>{faq.question}</AccordionTrigger>
                    <AccordionContent>
                        <div dangerouslySetInnerHTML={{ __html: faq.answer }} />
                    </AccordionContent>
                </AccordionItem>
            ))}
        </Accordion>
    );
}
