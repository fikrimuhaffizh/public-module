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

export function Section({
    id,
    eyebrow,
    title,
    text,
    children,
    dark = false,
    tint = false,
    narrow = false,
}) {
    const classes = [
        'section',
        dark ? 'section--dark' : '',
        tint ? 'section--tint' : '',
    ].filter(Boolean).join(' ');

    return (
        <section id={id} className={classes}>
            <div className={`shell ${narrow ? 'shell--narrow' : ''}`}>
                <Reveal className="section-heading">
                    <span className="eyebrow">{eyebrow}</span>
                    <h2>{title}</h2>
                    {text && <p>{text}</p>}
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

export function PlatformOverview({ site, image, pageCount = 0 }) {
    const capabilities = [
        [LayoutDashboard, 'Satu ruang kerja', 'Informasi dan layanan kampus tersaji dalam pengalaman yang konsisten.'],
        [Workflow, 'Alur terhubung', 'Konten publik terhubung langsung dengan pengelolaan data institusi.'],
        [BarChart3, 'Berbasis data', 'Bangun kepercayaan melalui informasi yang terukur dan selalu relevan.'],
    ];

    return (
        <section className="saas-overview">
            <div className="shell saas-overview-grid">
                <Reveal className="saas-copy">
                    <Badge variant="secondary"><Sparkles size={14} /> Platform institusi digital</Badge>
                    <h2>Satu pengalaman digital untuk seluruh ekosistem kampus</h2>
                    <p>
                        {site.name} menghadirkan akses informasi yang cepat, rapi, dan mudah
                        digunakan oleh mahasiswa, tenaga pendidik, serta masyarakat.
                    </p>
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

export function CtaSection({ site }) {
    return (
        <section className="saas-cta">
            <div className="shell">
                <Reveal className="saas-cta-card">
                    <div>
                        <span className="eyebrow">Mulai terhubung</span>
                        <h2>Temukan informasi dan layanan {site.name} dalam satu tempat.</h2>
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
