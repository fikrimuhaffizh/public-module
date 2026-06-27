import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    BarChart3,
    CheckCircle2,
    ExternalLink,
    Globe2,
    Layers3,
    Quote,
    ShieldCheck,
    Users2,
    Workflow,
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
    CtaSection,
    FaqSection,
    NewsGrid,
    PartnerCloud,
    PagesGrid,
    Section,
    SectionHeader,
    TestimonialSection,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

const capabilities = [
    {
        icon: Layers3,
        title: 'Platform terpadu',
        text: 'Sistem informasi akademik, kepegawaian, dan kemahasiswaan dalam satu ekosistem digital.',
    },
    {
        icon: ShieldCheck,
        title: 'Keamanan & kepatuhan',
        text: 'Standar keamanan data institusi dengan akses berbasis peran dan audit trail.',
    },
    {
        icon: BarChart3,
        title: 'Analitik real-time',
        text: 'Dashboard dan laporan untuk memantau kinerja institusi secara langsung.',
    },
    {
        icon: Users2,
        title: 'Kolaborasi lintas unit',
        text: 'Alur kerja terhubung antara akademik, keuangan, dan kemahasiswaan.',
    },
    {
        icon: Globe2,
        title: 'Akses di mana saja',
        text: 'Responsif di semua perangkat, siap melayani sivitas akademika kapan pun.',
    },
    {
        icon: Workflow,
        title: 'Otomatisasi proses',
        text: 'Kurangi beban administrasi dengan alur persetujuan dan notifikasi otomatis.',
    },
];

export default function EnterpriseTemplate({ data }) {
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
                        <section className="enterprise-hero">
                            <div className="enterprise-grid-bg" aria-hidden="true" />
                            <div className="shell enterprise-hero-grid">
                                <Reveal className="enterprise-hero-copy">
                                    <Badge className="enterprise-badge">
                                        <CheckCircle2 size={14} /> {section.pre_title || 'Enterprise platform'}
                                    </Badge>
                                    <h1>{copy.title}</h1>
                                    <p>{copy.subtitle}</p>
                                    <div className="hero-actions">
                                        <Button asChild size="lg">
                                            <a href={hero?.buttonPrimary?.link || '#fitur'}>
                                                {hero?.buttonPrimary?.text || 'Jelajahi platform'} <ArrowRight />
                                            </a>
                                        </Button>
                                        <Button variant="outline" asChild size="lg">
                                            <Link href={hero?.buttonSecondary?.link || data.site.contactUrl}>
                                                {hero?.buttonSecondary?.text || 'Jadwalkan demo'}
                                            </Link>
                                        </Button>
                                    </div>
                                    <div className="enterprise-metrics">
                                        <div>
                                            <strong>{data.pages.length}+</strong>
                                            <span>Modul terintegrasi</span>
                                        </div>
                                        <div>
                                            <strong>{data.partners.length}+</strong>
                                            <span>Institusi mitra</span>
                                        </div>
                                        <div>
                                            <strong>24/7</strong>
                                            <span>Dukungan teknis</span>
                                        </div>
                                    </div>
                                </Reveal>
                                <Reveal className="enterprise-visual" delay={0.12}>
                                    <div className="enterprise-mockup">
                                        <div className="enterprise-mockup-header">
                                            <span /><span /><span />
                                            <span>{data.site.name}</span>
                                        </div>
                                        <div className="enterprise-mockup-body">
                                            <div className="enterprise-mockup-sidebar">
                                                {Array.from({ length: 5 }).map((_, i) => <span key={i} />)}
                                            </div>
                                            <div className="enterprise-mockup-main">
                                                <div className="enterprise-mockup-toolbar"><span /><span /></div>
                                                <div className="enterprise-mockup-content">
                                                    <div><span /><span /><span /><span /></div>
                                                    <div><span /><span /><span /></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="enterprise-badge-inline">
                                        <ShieldCheck size={16} /> Terverifikasi & aman
                                    </div>
                                </Reveal>
                            </div>
                        </section>
                        <div className="enterprise-trust-strip">
                            <div className="shell enterprise-trust-inner">
                                <span>Dipercaya oleh institusi pendidikan</span>
                                <span>Standar keamanan enterprise</span>
                                <span>Implementasi <strong>14 hari</strong></span>
                            </div>
                        </div>
                    </React.Fragment>
                );

            case 'feature':
                return (
                    <section key={key} id="fitur" className="enterprise-features">
                        <div className="shell">
                            <Reveal className="enterprise-section-heading">
                                <span className="eyebrow">{section.pre_title || 'Kapabilitas platform'}</span>
                                <h2>{section.title || 'Fondasi digital untuk institusi yang terus bergerak maju'}</h2>
                                <p>{combinedText(section, 'Dirancang untuk membangun kepercayaan, memperkuat komunikasi, dan menghadirkan layanan informasi yang berkelas.')}</p>
                            </Reveal>
                            <Stagger className="enterprise-cap-grid">
                                {capabilities.map(({ icon: Icon, title, text }, index) => (
                                    <SpotlightCard key={title} className="enterprise-cap-card">
                                        <div className="enterprise-cap-icon"><Icon /></div>
                                        <CardHeader>
                                            <CardTitle>{title}</CardTitle>
                                            <CardDescription>{text}</CardDescription>
                                        </CardHeader>
                                        <CardFooter>
                                            <span>0{index + 1}</span>
                                            <ArrowRight size={16} />
                                        </CardFooter>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                );

            case 'statistic':
                return landing?.statistics?.length > 0 ? (
                    <section key={key} className="enterprise-stats">
                        <div className="shell">
                            <SectionHeader section={section} />
                            <Stagger className="enterprise-stats-grid">
                                {landing.statistics.slice(0, section.limit_data || 4).map((stat) => (
                                    <SpotlightCard key={stat.id} className="enterprise-stat-card">
                                        <strong>{stat.value}</strong>
                                        <span>{stat.label}</span>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                ) : null;

            case 'product':
                return landing?.products?.length > 0 ? (
                    <Section key={key} section={section} tint
                        eyebrow={section.pre_title || 'Solusi enterprise'}
                        title={section.title || 'Produk dan layanan institusi'}
                        text={combinedText(section, 'Jelajahi modul dan solusi yang dirancang untuk kebutuhan kampus.')}
                    >
                        <Stagger className="enterprise-product-grid">
                            {landing.products.slice(0, section.limit_data || 6).map((product) => (
                                <SpotlightCard key={product.id} className="enterprise-product-card">
                                    {product.image && <img src={product.image} alt={product.name} />}
                                    <div className="enterprise-product-body">
                                        <h3>{product.name}</h3>
                                        <p>{product.shortDescription || product.description}</p>
                                        {product.demoUrl && (
                                            <a href={product.demoUrl} target="_blank" rel="noreferrer" className="text-link">
                                                Lihat demo <ExternalLink size={14} />
                                            </a>
                                        )}
                                    </div>
                                </SpotlightCard>
                            ))}
                        </Stagger>
                    </Section>
                ) : (
                    <Section key={key} section={section} tint
                        eyebrow={section.pre_title || 'Solusi enterprise'}
                        title={section.title || 'Produk dan layanan institusi'}
                        text={combinedText(section, 'Jelajahi modul dan solusi yang dirancang untuk kebutuhan kampus.')}
                    >
                        <PagesGrid pages={data.pages} section={section} />
                    </Section>
                );

            case 'client':
                return <PartnerCloud key={key} partners={data.partners} section={section} />;

            case 'testimonial':
                return <TestimonialSection key={key} testimonials={data.testimonials} section={section} />;

            case 'pengumuman':
                return (
                    <Section key={key} section={section}
                        eyebrow={section.pre_title || 'Enterprise insights'}
                        title={section.title || 'Informasi dan perkembangan terbaru'}
                        text={combinedText(section, 'Ikuti agenda, pencapaian, dan kabar penting dari ekosistem institusi.')}
                    >
                        <NewsGrid announcements={data.announcements} section={section} />
                    </Section>
                );

            case 'faq':
                return (
                    <Section key={key} section={section} dark
                        eyebrow={section.pre_title || 'Informasi praktis'}
                        title={section.title || 'Pertanyaan yang sering diajukan'}
                        text={combinedText(section)}
                        narrow
                    >
                        <FaqSection faqs={data.faqs} />
                    </Section>
                );

            case 'cta':
                return <CtaSection key={key} site={data.site} section={section} />;

            default:
                return null;
        }
    };

    return <>{sections.map(renderSection)}</>;
}
