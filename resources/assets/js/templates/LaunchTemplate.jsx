import React from 'react';
import { ArrowRight, ExternalLink, Sparkles, Zap } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { BackgroundBeams, Marquee, Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';
import { FaqSection, NewsGrid, Section, TestimonialSection } from '@public/components/sections/LandingSections';

function IconOrImage({ icon, image, className = '' }) {
    if (image) return <img src={image} alt="" className={className} />;
    if (icon) return <i className={`${icon} ${className}`} aria-hidden="true" />;
    return <Sparkles className={className} size={22} />;
}

export default function LaunchTemplate({ data }) {
    const sections = data.sections || [];
    const { landing, site } = data;
    const hero = landing?.hero;
    const heroStyle = hero?.image
        ? { backgroundImage: `linear-gradient(135deg, rgba(5,8,20,.88), rgba(5,8,20,.55)), url("${hero.image}")` }
        : undefined;

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;

        switch (section.section_key) {
            case 'hero':
                return (
                    <section key={key} className="launch-hero" style={heroStyle}>
                        <BackgroundBeams />
                        <div className="launch-hero-glow launch-hero-glow--one" />
                        <div className="launch-hero-glow launch-hero-glow--two" />
                        <div className="shell launch-hero-grid">
                            <Reveal className="launch-hero-copy">
                                <Badge className="launch-badge"><Zap size={14} /> {section.pre_title || 'Platform kampus terintegrasi'}</Badge>
                                {hero?.subtitle && <p className="launch-eyebrow">{hero.subtitle}</p>}
                                <h1>{hero?.title || site.name}</h1>
                                <p>{hero?.description || site.tagline}</p>
                                <div className="hero-actions">
                                    {(hero?.buttonPrimary?.text) && (
                                        <Button asChild size="lg" className="launch-btn-primary">
                                            <a href={hero.buttonPrimary.link || site.loginUrl}>
                                                {hero.buttonPrimary.text} <ArrowRight size={18} />
                                            </a>
                                        </Button>
                                    )}
                                    {(hero?.buttonSecondary?.text) && (
                                        <Button variant="outline" asChild size="lg" className="launch-btn-outline">
                                            <a href={hero.buttonSecondary.link || site.contactUrl}>{hero.buttonSecondary.text}</a>
                                        </Button>
                                    )}
                                </div>
                            </Reveal>
                            <Reveal className="launch-hero-visual" delay={0.12}>
                                <div className="launch-mockup">
                                    <div className="launch-mockup-bar"><span /><span /><span /></div>
                                    <div className="launch-mockup-body">
                                        {hero?.image
                                            ? <img src={hero.image} alt={hero.title || site.name} />
                                            : <div className="launch-mockup-placeholder"><Sparkles size={48} /></div>}
                                    </div>
                                </div>
                            </Reveal>
                        </div>
                    </section>
                );

            case 'statistic':
                return landing?.statistics?.length > 0 ? (
                    <section key={key} className="launch-stats">
                        <div className="shell">
                            <Stagger className="launch-stats-grid">
                                {landing.statistics.map((stat) => (
                                    <SpotlightCard key={stat.id} className="launch-stat-card">
                                        <IconOrImage icon={stat.icon} className="launch-stat-icon" />
                                        <strong>{stat.value}</strong>
                                        <span>{stat.label}</span>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                ) : null;

            case 'feature':
                return landing?.features?.length > 0 ? (
                    <Section key={key} eyebrow={section.pre_title || 'Keunggulan'} title={section.title || 'Semua yang dibutuhkan institusi'} text={section.subtitle || 'Fitur dirancang untuk mendukung transformasi digital kampus secara menyeluruh.'}>
                        <Stagger className="launch-bento">
                            {landing.features.map((feature, index) => (
                                <SpotlightCard key={feature.id} className={`launch-bento-item ${index === 0 ? 'launch-bento-item--lead' : ''}`}>
                                    <IconOrImage icon={feature.icon} image={feature.image} className="launch-feature-icon" />
                                    <h3>{feature.title}</h3>
                                    <p>{feature.description}</p>
                                </SpotlightCard>
                            ))}
                        </Stagger>
                    </Section>
                ) : null;

            case 'product':
                return landing?.products?.length > 0 ? (
                    <Section key={key} id="modul" tint eyebrow={section.pre_title || 'Ekosistem modul'} title={section.title || 'Solusi modular siap pakai'}>
                        <Stagger className="launch-products">
                            {landing.products.map((product) => (
                                <SpotlightCard key={product.id} className="launch-product-card">
                                    {product.image && <img src={product.image} alt={product.name} className="launch-product-image" />}
                                    <div className="launch-product-body">
                                        <h3>{product.name}</h3>
                                        <p>{product.shortDescription || product.description}</p>
                                        {product.demoUrl && (
                                            <a href={product.demoUrl} target="_blank" rel="noreferrer" className="text-link">
                                                Lihat demo <ExternalLink size={15} />
                                            </a>
                                        )}
                                    </div>
                                </SpotlightCard>
                            ))}
                        </Stagger>
                    </Section>
                ) : null;

            case 'client':
                return landing?.clients?.length > 0 ? (
                    <React.Fragment key={key}>
                        <Marquee items={landing.clients.map((c) => c.name)} />
                        <section className="launch-clients">
                            <div className="shell">
                                <Reveal className="section-heading section-heading--center">
                                    <span className="eyebrow">Dipercaya</span>
                                    <h2>{section.title || 'Institusi yang memakai platform kami'}</h2>
                                </Reveal>
                                <div className="launch-clients-grid">
                                    {landing.clients.map((client) => (
                                        client.website
                                            ? <a key={client.id} href={client.website} target="_blank" rel="noreferrer" className="launch-client-logo">
                                                {client.logo ? <img src={client.logo} alt={client.name} /> : <span>{client.name}</span>}
                                            </a>
                                            : <div key={client.id} className="launch-client-logo">
                                                {client.logo ? <img src={client.logo} alt={client.name} /> : <span>{client.name}</span>}
                                            </div>
                                    ))}
                                </div>
                            </div>
                        </section>
                    </React.Fragment>
                ) : null;

            case 'testimonial':
                return <TestimonialSection key={key} testimonials={data.testimonials} />;

            case 'pengumuman':
                return (
                    <Section key={key} id="berita" eyebrow={section.pre_title || 'Kabar terbaru'} title={section.title || 'Informasi dan pengumuman kampus'}>
                        <NewsGrid announcements={data.announcements} />
                    </Section>
                );

            case 'faq':
                return (
                    <Section key={key} eyebrow={section.pre_title || 'FAQ'} title={section.title || 'Pertanyaan yang sering diajukan'} narrow>
                        <FaqSection faqs={data.faqs} />
                    </Section>
                );

            case 'cta':
                return landing?.cta ? (
                    <section key={key}
                        className="launch-cta"
                        style={landing.cta.backgroundImage
                            ? { backgroundImage: `linear-gradient(135deg, rgba(8,12,28,.92), rgba(8,12,28,.72)), url("${landing.cta.backgroundImage}")` }
                            : undefined}
                    >
                        <div className="shell launch-cta-inner">
                            <Reveal>
                                <h2>{landing.cta.title}</h2>
                                {landing.cta.description && <p>{landing.cta.description}</p>}
                                {landing.cta.buttonText && (
                                    <Button asChild size="lg" className="launch-btn-primary">
                                        <a href={landing.cta.buttonLink || site.contactUrl}>
                                            {landing.cta.buttonText} <ArrowRight size={18} />
                                        </a>
                                    </Button>
                                )}
                            </Reveal>
                        </div>
                    </section>
                ) : (
                    <section key={key} className="launch-cta launch-cta--fallback">
                        <div className="shell launch-cta-inner">
                            <Reveal>
                                <h2>Siap mulai transformasi digital kampus?</h2>
                                <p>Hubungi tim kami untuk demo dan konsultasi implementasi.</p>
                                <Button asChild size="lg" className="launch-btn-primary">
                                    <Link href={site.contactUrl}>Hubungi Kami <ArrowRight size={18} /></Link>
                                </Button>
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
