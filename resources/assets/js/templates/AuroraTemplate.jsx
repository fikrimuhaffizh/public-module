import React, { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    BookOpen,
    CalendarDays,
    CheckCircle2,
    GraduationCap,
    Layers3,
    Moon,
    Quote,
    ShieldCheck,
    Sparkles,
    Star,
    Sun,
    Users,
    Zap,
} from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@public/components/ui/accordion';
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';
import { FaqSection, NewsGrid, Section, SectionHeader, combinedText, heroCopy, sectionKey } from '@public/components/sections/LandingSections';

const icons = [BookOpen, GraduationCap, Users, ShieldCheck, Layers3, CalendarDays];

export default function AuroraTemplate({ data }) {
    const sections = data.sections || [];
    const { landing, site } = data;
    const hero = landing?.hero;

    const [isDark, setIsDark] = useState(() => {
        if (typeof window === 'undefined') return true;
        return localStorage.getItem('aurora-theme') !== 'light';
    });

    useEffect(() => {
        const wrapper = document.querySelector('.theme-aurora');
        if (wrapper) {
            wrapper.setAttribute('data-theme', isDark ? 'dark' : 'light');
        }
        localStorage.setItem('aurora-theme', isDark ? 'dark' : 'light');
    }, [isDark]);

    const toggleTheme = () => setIsDark(prev => !prev);

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;
        const copy = heroCopy(section, hero, site);

        switch (sectionKey(section)) {
            case 'hero':
                return (
                    <React.Fragment key={key}>
                        <section className="aurora-hero">
                            <button
                                className="aurora-theme-toggle"
                                onClick={toggleTheme}
                                aria-label={isDark ? 'Ganti ke mode terang' : 'Ganti ke mode gelap'}
                                type="button"
                            >
                                {isDark ? <Sun size={18} /> : <Moon size={18} />}
                            </button>
                            <div className="aurora-blob aurora-blob--one" />
                            <div className="aurora-blob aurora-blob--two" />
                            <div className="shell aurora-hero-grid">
                                <Reveal className="aurora-hero-copy">
                                    <Badge className="aurora-badge"><Zap size={14} /> {section.pre_title || 'Platform kampus digital'}</Badge>
                                    <h1 className="aurora-gradient-text">{copy.title}</h1>
                                    <p>{copy.subtitle}</p>
                                    <div className="hero-actions">
                                        {hero?.buttonPrimary?.text && (
                                            <Button asChild size="lg" className="aurora-btn-primary">
                                                <a href={hero.buttonPrimary.link || site.loginUrl}>
                                                    {hero.buttonPrimary.text} <ArrowRight size={18} />
                                                </a>
                                            </Button>
                                        )}
                                        {hero?.buttonSecondary?.text && (
                                            <Button variant="outline" asChild size="lg" className="aurora-btn-outline">
                                                <a href={hero.buttonSecondary.link || site.contactUrl}>
                                                    {hero.buttonSecondary.text}
                                                </a>
                                            </Button>
                                        )}
                                    </div>
                                </Reveal>
                                <Reveal className="aurora-hero-visual" delay={0.12}>
                                    <div className="aurora-mockup">
                                        <div className="aurora-mockup-header">
                                            <span /><span /><span />
                                            <span>{site.name}</span>
                                        </div>
                                        <div className="aurora-mockup-body">
                                            <div className="aurora-mockup-sidebar">
                                                {Array.from({ length: 5 }).map((_, i) => <span key={i} />)}
                                            </div>
                                            <div className="aurora-mockup-content">
                                                {hero?.image
                                                    ? <img src={hero.image} alt={copy.imageAlt} />
                                                    : <div className="aurora-mockup-placeholder"><Sparkles size={40} /></div>}
                                            </div>
                                        </div>
                                    </div>
                                </Reveal>
                            </div>
                        </section>
                        <div className="aurora-trust-strip">
                            <div className="shell aurora-trust-inner">
                                {[
                                    'Implementasi cepat',
                                    'Responsif semua perangkat',
                                    'Konten CMS terkelola',
                                    'Pengalaman konsisten',
                                ].map((text) => (
                                    <span key={text}><CheckCircle2 size={16} /> {text}</span>
                                ))}
                            </div>
                        </div>
                    </React.Fragment>
                );

            case 'statistic':
                return landing?.statistics?.length > 0 ? (
                    <section key={key} className="aurora-section aurora-stats-section">
                        <div className="shell">
                            <SectionHeader section={section} />
                            <Stagger className="aurora-stats-grid">
                                {landing.statistics.slice(0, section.limit_data || 4).map((stat, index) => {
                                    const Icon = icons[index % icons.length];
                                    return (
                                        <SpotlightCard key={stat.id} className="aurora-stat-card">
                                            <Icon size={22} className="aurora-stat-icon" />
                                            <strong>{stat.value}</strong>
                                            <span>{stat.label}</span>
                                        </SpotlightCard>
                                    );
                                })}
                            </Stagger>
                        </div>
                    </section>
                ) : null;

            case 'feature':
                return landing?.features?.length > 0 ? (
                    <Section key={key} section={section} eyebrow={section.pre_title || 'Fitur unggulan'} title={section.title || 'Semua yang dibutuhkan institusi'} text={combinedText(section, 'Solusi lengkap untuk transformasi digital kampus.')}>
                        <Stagger className="aurora-bento-grid">
                            {landing.features.slice(0, section.limit_data || 6).map((feature, index) => (
                                <SpotlightCard key={feature.id} className={`aurora-bento-item ${index === 0 ? 'aurora-bento-item--lead' : ''}`}>
                                    {feature.image
                                        ? <img src={feature.image} alt={feature.title} className="aurora-bento-image" />
                                        : feature.icon && <i className={`${feature.icon} aurora-bento-icon`} />}
                                    <h3>{feature.title}</h3>
                                    <p>{feature.description}</p>
                                </SpotlightCard>
                            ))}
                        </Stagger>
                    </Section>
                ) : null;

            case 'product':
                return landing?.products?.length > 0 ? (
                    <Section key={key} section={section} eyebrow={section.pre_title || 'Ekosistem modul'} title={section.title || 'Solusi modular siap pakai'} text={combinedText(section)}>
                        <Stagger className="aurora-products-grid">
                            {landing.products.slice(0, section.limit_data || 6).map((product) => (
                                <SpotlightCard key={product.id} className="aurora-product-card">
                                    {product.image && <img src={product.image} alt={product.name} className="aurora-product-image" />}
                                    <h3>{product.name}</h3>
                                    <p>{product.shortDescription || product.description}</p>
                                    {product.demoUrl && (
                                        <a href={product.demoUrl} target="_blank" rel="noreferrer" className="aurora-link">
                                            Lihat demo <ArrowRight size={15} />
                                        </a>
                                    )}
                                </SpotlightCard>
                            ))}
                        </Stagger>
                    </Section>
                ) : null;

            case 'client':
                return landing?.clients?.length > 0 ? (
                    <React.Fragment key={key}>
                        <div className="aurora-marquee">
                            <div className="aurora-marquee-track">
                                {[...landing.clients, ...landing.clients].map((client, i) => (
                                    <span key={`${client.id}-${i}`} className="aurora-marquee-item">
                                        {client.logo
                                            ? <img src={client.logo} alt={client.name} />
                                            : <span>{client.name}</span>}
                                    </span>
                                ))}
                            </div>
                        </div>
                        <section className="aurora-section aurora-section--tint">
                            <div className="shell">
                                <SectionHeader section={section} />
                            </div>
                        </section>
                    </React.Fragment>
                ) : null;

            case 'pengumuman':
                return (
                    <Section key={key} section={section} id="berita" eyebrow={section.pre_title || 'Informasi terkini'} title={section.title || 'Berita dan pengumuman kampus'} text={combinedText(section)}>
                        <NewsGrid announcements={data.announcements} section={section} />
                    </Section>
                );

            case 'testimonial':
                return data.testimonials?.length > 0 ? (
                    <section key={key} className="aurora-section aurora-section--tint">
                        <div className="shell">
                            <SectionHeader section={section} />
                            <Stagger className="aurora-testimonials-grid">
                                {data.testimonials.slice(0, section.limit_data || 6).map((t) => (
                                    <SpotlightCard key={t.id} className="aurora-testimonial-card">
                                        <Quote className="aurora-quote-icon" />
                                        <blockquote>"{t.quote}"</blockquote>
                                        <div className="aurora-testimonial-author">
                                            {t.photo
                                                ? <img src={t.photo} alt={t.name} />
                                                : <span>{t.name.slice(0, 2).toUpperCase()}</span>}
                                            <div>
                                                <strong>{t.name}</strong>
                                                <small>{[t.position, t.organization].filter(Boolean).join(' · ')}</small>
                                            </div>
                                        </div>
                                        <div className="aurora-stars">
                                            {Array.from({ length: t.rating || 5 }).map((_, i) => (
                                                <Star key={i} size={14} fill="currentColor" />
                                            ))}
                                        </div>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                ) : null;

            case 'faq':
                return (
                    <Section key={key} section={section} eyebrow={section.pre_title || 'FAQ'} title={section.title || 'Pertanyaan yang sering diajukan'} text={combinedText(section)} narrow>
                        <Accordion type="single" collapsible className="aurora-faq-list">
                            {data.faqs.map(faq => (
                                <AccordionItem key={faq.id} value={`faq-${faq.id}`}>
                                    <AccordionTrigger>{faq.question}</AccordionTrigger>
                                    <AccordionContent>
                                        <div dangerouslySetInnerHTML={{ __html: faq.answer }} />
                                    </AccordionContent>
                                </AccordionItem>
                            ))}
                        </Accordion>
                    </Section>
                );

            case 'cta':
                return (
                    <section key={key} className="aurora-cta">
                        <div className="aurora-cta-blob aurora-cta-blob--one" />
                        <div className="aurora-cta-blob aurora-cta-blob--two" />
                        <div className="shell aurora-cta-inner">
                            <Reveal>
                                <span className="eyebrow">{section.pre_title || 'Mulai sekarang'}</span>
                                <h2 className="aurora-gradient-text">{section.title || 'Siap memulai transformasi digital?'}</h2>
                                <p>{combinedText(section, 'Hubungi tim kami untuk demo dan konsultasi implementasi.')}</p>
                                <div className="hero-actions">
                                    <Button asChild size="lg" className="aurora-btn-primary">
                                        <Link href={site.contactUrl}>Hubungi Kami <ArrowRight size={18} /></Link>
                                    </Button>
                                    {hero?.buttonPrimary?.text && (
                                        <Button variant="outline" asChild size="lg" className="aurora-btn-outline">
                                            <a href={hero.buttonPrimary.link || site.loginUrl}>
                                                {hero.buttonPrimary.text}
                                            </a>
                                        </Button>
                                    )}
                                </div>
                            </Reveal>
                        </div>
                    </section>
                );

            default:
                return null;
        }
    };

    return <>{sections.map(renderSection)}</>;
}
