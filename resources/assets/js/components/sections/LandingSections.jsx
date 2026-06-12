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

export function sectionHeading(section, defaults = {}) {
    return {
        eyebrow: sectionText(section, 'pre_title', defaults.eyebrow || ''),
        title: sectionText(section, 'title', defaults.title || ''),
        text: sectionText(section, 'subtitle', sectionText(section, 'post_title', defaults.text || '')),
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
        subtitle: sectionText(section, 'subtitle', hero?.subtitle || ''),
        description: sectionText(section, 'post_title', hero?.description || site.tagline),
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

    return (
        <section id={id} className={classes}>
            <div className={`shell ${narrow ? 'shell--narrow' : ''}`}>
                <Reveal className={`section-heading section-heading--${heading.align}`}>
                    <span className="eyebrow">{heading.eyebrow}</span>
                    <h2>{heading.title}</h2>
                    {heading.text && <p>{heading.text}</p>}
                </Reveal>
                {children}
            </div>
        </section>
    );
}

export function PagesGrid({ pages }) {
    const icons = [Building2, GraduationCap, BookOpen, Users, ShieldCheck, CalendarDays];

    return (
        <Stagger className="feature-grid">
            {pages.slice(0, 6).map((page, index) => {
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

export function NewsGrid({ announcements, editorial = false }) {
    const gridClass = editorial ? 'news-grid news-grid--editorial' : 'news-grid';

    return (
        <Stagger className={gridClass}>
            {announcements.map((item, index) => (
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
        text: `${site.name} menghadirkan akses informasi yang cepat, rapi, dan mudah digunakan oleh mahasiswa, tenaga pendidik, serta masyarakat.`,
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
                    <Badge variant="secondary"><Sparkles size={14} /> {heading.eyebrow}</Badge>
                    <h2>{heading.title}</h2>
                    <p>{heading.text}</p>
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

export function PartnerCloud({ partners, section }) {
    if (!partners?.length) return null;
    const heading = sectionHeading(section, {
        eyebrow: 'Dipercaya dan berkolaborasi',
        title: 'Partner dalam ekosistem kami',
    });

    return (
        <section className="partner-section">
            <div className="shell">
                <Reveal className="partner-heading">
                    <span className="eyebrow">{heading.eyebrow}</span>
                    <h2>{heading.title}</h2>
                    {heading.text && <p>{heading.text}</p>}
                </Reveal>
                <div className="partner-cloud">
                    {partners.map(partner => {
                        const content = partner.logo
                            ? <img src={partner.logo} alt={partner.name} />
                            : <strong>{partner.name}</strong>;

                        return partner.url
                            ? <a key={partner.id} href={partner.url} target="_blank" rel="noreferrer" title={partner.name}>{content}</a>
                            : <div key={partner.id} title={partner.name}>{content}</div>;
                    })}
                </div>
            </div>
        </section>
    );
}

export function TestimonialSection({ testimonials, section }) {
    if (!testimonials?.length) return null;
    const heading = sectionHeading(section, {
        eyebrow: 'Cerita dari komunitas',
        title: 'Pengalaman nyata dalam ekosistem kami',
        text: 'Kepercayaan dibangun dari layanan yang konsisten dan pengalaman yang bermakna.',
    });

    return (
        <Section
            tint
            eyebrow={heading.eyebrow}
            title={heading.title}
            text={heading.text}
        >
            <Stagger className="testimonial-grid">
                {testimonials.slice(0, 6).map(testimonial => (
                    <SpotlightCard key={testimonial.id} className="testimonial-card">
                        <div className="testimonial-rating">
                            {Array.from({ length: testimonial.rating || 5 }).map((_, index) => (
                                <Star key={index} size={16} fill="currentColor" />
                            ))}
                        </div>
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
                        <span className="eyebrow">{heading.eyebrow}</span>
                        <h2>{heading.title}</h2>
                        {heading.text && <p>{heading.text}</p>}
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
