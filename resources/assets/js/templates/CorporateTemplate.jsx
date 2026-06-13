import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Building2,
    ChartNoAxesCombined,
    CheckCircle2,
    Globe2,
    Layers3,
    Quote,
    ShieldCheck,
} from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@public/components/ui/card';
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';
import {
    FaqSection,
    NewsGrid,
    PartnerCloud,
    PagesGrid,
    Section,
    SectionHeader,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

const pillars = [
    {
        icon: Layers3,
        title: 'Ekosistem terintegrasi',
        text: 'Informasi, layanan, dan komunikasi institusi hadir dalam satu pengalaman digital.',
    },
    {
        icon: ShieldCheck,
        title: 'Tata kelola terpercaya',
        text: 'Konten terstruktur membantu institusi menjaga akurasi dan konsistensi informasi.',
    },
    {
        icon: ChartNoAxesCombined,
        title: 'Pertumbuhan berkelanjutan',
        text: 'Fondasi digital yang siap berkembang mengikuti kebutuhan dan strategi institusi.',
    },
];

export default function CorporateTemplate({ data }) {
    const sections = data.sections || [];
    const { landing } = data;
    const hero = landing?.hero;

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;
        const copy = heroCopy(section, hero, data.site);

        switch (sectionKey(section)) {
            case 'hero':
                return (
                    <React.Fragment key={key}>
                        <section className="corporate-hero">
                            <div className="corporate-grid-lines" aria-hidden="true" />
                            <div className="shell corporate-hero-grid">
                                <Reveal className="corporate-hero-copy">
                                    <Badge variant="outline">
                                        <Building2 size={14} /> {section.pre_title || 'Digital excellence'}
                                    </Badge>
                                    <h1>{copy.title}</h1>
                                    <p>{copy.subtitle}</p>
                                    <div className="hero-actions">
                                        <Button asChild size="lg">
                                            <a href={hero?.buttonPrimary?.link || '#kapabilitas'}>
                                                {hero?.buttonPrimary?.text || 'Jelajahi kapabilitas'} <ArrowRight />
                                            </a>
                                        </Button>
                                        <Button variant="outline" asChild size="lg">
                                            <Link href={hero?.buttonSecondary?.link || data.site.contactUrl}>{hero?.buttonSecondary?.text || 'Hubungi institusi'}</Link>
                                        </Button>
                                    </div>
                                    <div className="corporate-assurance">
                                        <span><CheckCircle2 /> Informasi terkelola</span>
                                        <span><CheckCircle2 /> Pengalaman responsif</span>
                                        <span><CheckCircle2 /> Siap berkembang</span>
                                    </div>
                                </Reveal>

                                <Reveal className="corporate-visual" delay={0.12}>
                                    {hero?.image && <img src={hero.image} alt={copy.imageAlt} />}
                                    <div className="corporate-visual-overlay">
                                        <span>Digital presence</span>
                                        <strong>{data.site.name}</strong>
                                    </div>
                                    <Card className="corporate-floating-card">
                                        <CardContent>
                                            <Globe2 />
                                            <div>
                                                <strong>{data.pages.length}+</strong>
                                                <span>Informasi terhubung</span>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </Reveal>
                            </div>
                        </section>

                        <section className="corporate-metrics">
                            <div className="shell corporate-metrics-grid">
                                {[
                                    [`${data.pages.length}+`, 'Halaman informasi'],
                                    [`${data.partners.length}+`, 'Partner kolaborasi'],
                                    [`${data.announcements.length}+`, 'Informasi terkini'],
                                    ['24/7', 'Akses digital'],
                                ].map(([value, label]) => (
                                    <div key={label}><strong>{value}</strong><span>{label}</span></div>
                                ))}
                            </div>
                        </section>
                    </React.Fragment>
                );

            case 'client':
                return <PartnerCloud key={key} partners={data.partners} section={section} />;

            case 'statistic':
                return landing?.statistics?.length > 0 ? (
                    <section key={key} className="corporate-metrics">
                        <div className="shell">
                            <SectionHeader section={section} />
                            <div className="corporate-metrics-grid">
                                {landing.statistics.slice(0, section.limit_data || 4).map((stat) => (
                                    <div key={stat.id}>
                                        <strong>{stat.value}</strong>
                                        <span>{stat.label}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                ) : null;

            case 'product':
                if (landing?.products?.length > 0) {
                    return (
                        <Section key={key} section={section} tint
                            eyebrow={section.pre_title || 'Solusi kami'}
                            title={section.title || 'Produk dan layanan institusi'}
                            text={combinedText(section, 'Jelajahi modul dan solusi yang dirancang untuk kebutuhan kampus.')}
                        >
                            <Stagger className="corporate-product-grid">
                                {landing.products.slice(0, section.limit_data || 6).map((product) => (
                                    <SpotlightCard key={product.id} className="corporate-product-card">
                                        {product.image && <img src={product.image} alt={product.name} className="corporate-product-image" />}
                                        <div className="corporate-product-body">
                                            <h3>{product.name}</h3>
                                            <p>{product.shortDescription || product.description}</p>
                                            {product.demoUrl && (
                                                <a href={product.demoUrl} target="_blank" rel="noreferrer" className="text-link">
                                                    Lihat demo <ArrowRight size={16} />
                                                </a>
                                            )}
                                        </div>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </Section>
                    );
                }
                return (
                    <Section key={key} section={section} tint
                        eyebrow={section.pre_title || 'Solusi kami'}
                        title={section.title || 'Produk dan layanan institusi'}
                        text={combinedText(section, 'Jelajahi modul dan solusi yang dirancang untuk kebutuhan kampus.')}
                    >
                        <PagesGrid pages={data.pages} section={section} />
                    </Section>
                );

            case 'feature': {
                const cmsFeatures = landing?.features || [];
                if (cmsFeatures.length > 0) {
                    return (
                        <section key={key} id="kapabilitas" className="corporate-capabilities">
                            <div className="shell">
                                <Reveal className="corporate-section-heading">
                                    <div>
                                        <span className="eyebrow">{section.pre_title || 'Kapabilitas utama'}</span>
                                        <h2>{section.title || 'Fondasi digital untuk institusi yang terus bergerak maju'}</h2>
                                    </div>
                                    <p>{combinedText(section, 'Dirancang untuk membangun kepercayaan, memperkuat komunikasi, dan menghadirkan layanan informasi yang berkelas.')}</p>
                                </Reveal>
                                <Stagger className="corporate-bento">
                                    {cmsFeatures.slice(0, section.limit_data || 6).map((feature, index) => (
                                        <SpotlightCard key={feature.id} className={`corporate-pillar ${index === 0 ? 'corporate-pillar--lead' : ''}`}>
                                            {feature.image
                                                ? <img src={feature.image} alt={feature.title} className="corporate-feature-image" />
                                                : feature.icon && <span className="corporate-pillar-icon"><i className={feature.icon} /></span>}
                                            <CardHeader>
                                                <CardTitle>{feature.title}</CardTitle>
                                                <CardDescription>{feature.description}</CardDescription>
                                            </CardHeader>
                                            <CardFooter>
                                                <span>0{index + 1}</span>
                                                <ArrowRight />
                                            </CardFooter>
                                        </SpotlightCard>
                                    ))}
                                </Stagger>
                            </div>
                        </section>
                    );
                }
                return (
                    <section key={key} id="kapabilitas" className="corporate-capabilities">
                        <div className="shell">
                            <Reveal className="corporate-section-heading">
                                <div>
                                    <span className="eyebrow">{section.pre_title || 'Kapabilitas utama'}</span>
                                    <h2>{section.title || 'Fondasi digital untuk institusi yang terus bergerak maju'}</h2>
                                </div>
                                <p>{combinedText(section, 'Dirancang untuk membangun kepercayaan, memperkuat komunikasi, dan menghadirkan layanan informasi yang berkelas.')}</p>
                            </Reveal>
                            <Stagger className="corporate-bento">
                                {pillars.map(({ icon: Icon, title, text }, index) => (
                                    <Card className={index === 0 ? 'corporate-pillar corporate-pillar--lead' : 'corporate-pillar'} key={title}>
                                        <CardHeader>
                                            <span className="corporate-pillar-icon"><Icon /></span>
                                            <CardTitle>{title}</CardTitle>
                                            <CardDescription>{text}</CardDescription>
                                        </CardHeader>
                                        <CardFooter>
                                            <span>0{index + 1}</span>
                                            <ArrowRight />
                                        </CardFooter>
                                    </Card>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                );
            }

            case 'testimonial':
                return data.testimonials?.length > 0 ? (
                    <section key={key} className="corporate-testimonials">
                        <div className="shell">
                            <SectionHeader section={section} />
                            <Stagger className="corporate-testimonials-grid">
                                {data.testimonials.slice(0, section.limit_data || 6).map((testimonial) => (
                                    <SpotlightCard key={testimonial.id} className="corporate-testimonial-card">
                                        <Quote className="corporate-testimonial-quote" />
                                        <blockquote>"{testimonial.quote}"</blockquote>
                                        <div className="corporate-testimonial-person">
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
                        </div>
                    </section>
                ) : null;

            case 'pengumuman':
                return (
                    <Section key={key} section={section}
                        eyebrow={section.pre_title || 'Corporate insights'}
                        title={section.title || 'Informasi dan perkembangan terbaru'}
                        text={combinedText(section, 'Ikuti agenda, pencapaian, dan kabar penting dari ekosistem institusi.')}
                    >
                        <NewsGrid announcements={data.announcements} section={section} />
                    </Section>
                );

            case 'faq':
                return (
                    <Section key={key} section={section} dark eyebrow={section.pre_title || 'Informasi praktis'} title={section.title || 'Pertanyaan yang sering diajukan'} text={combinedText(section)} narrow>
                        <FaqSection faqs={data.faqs} />
                    </Section>
                );

            case 'cta':
                return (
                    <section key={key} className="corporate-closing">
                        <div className="shell">
                            <Reveal className="corporate-closing-inner">
                                <div>
                                    <span className="eyebrow">{section.pre_title || 'Bangun koneksi bernilai'}</span>
                                    <h2>{section.title || 'Mari membuka peluang kolaborasi berikutnya.'}</h2>
                                    {(section.subtitle || section.post_title) && <p>{combinedText(section)}</p>}
                                </div>
                                <Button asChild size="lg">
                                    <Link href={data.site.contactUrl}>Mulai percakapan <ArrowRight /></Link>
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
