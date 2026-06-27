import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Award,
    BookOpen,
    CalendarCheck,
    CheckCircle2,
    ChevronRight,
    FileText,
    GraduationCap,
    MapPin,
    Quote,
    Sparkles,
    Star,
    Upload,
    Users,
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
    Section,
    SectionHeader,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

const steps = [
    {
        icon: FileText,
        title: 'Isi data diri',
        text: 'Lengkapi informasi pribadi dan pilih program yang sesuai.',
    },
    {
        icon: Upload,
        title: 'Unggah dokumen',
        text: 'Lampirkan berkas persyaratan secara digital.',
    },
    {
        icon: CheckCircle2,
        title: 'Verifikasi & konfirmasi',
        text: 'Tim kami akan memverifikasi dan mengonfirmasi pendaftaran Anda.',
    },
];

const benefits = [
    'Proses pendaftaran online, tanpa datang ke kampus',
    'Konfirmasi cepat dalam 1x24 jam',
    'Panduan lengkap setiap tahap pendaftaran',
    'Akses informasi biaya dan beasiswa',
];

export default function RegistrationTemplate({ data }) {
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
                        <section className="registration-hero">
                            <div className="shell registration-hero-grid">
                                <Reveal className="registration-hero-copy">
                                    <Badge className="registration-badge">
                                        <Sparkles size={14} /> {section.pre_title || 'Pendaftaran dibuka'}
                                    </Badge>
                                    <h1>{copy.title}</h1>
                                    <p>{copy.subtitle}</p>
                                    <div className="hero-actions">
                                        <Button asChild size="lg">
                                            <a href={hero?.buttonPrimary?.link || '#daftar'}>
                                                {hero?.buttonPrimary?.text || 'Daftar sekarang'} <ArrowRight />
                                            </a>
                                        </Button>
                                        <Button variant="outline" asChild size="lg">
                                            <Link href={hero?.buttonSecondary?.link || '#informasi'}>
                                                {hero?.buttonSecondary?.text || 'Pelajari program'}
                                            </Link>
                                        </Button>
                                    </div>
                                    <div className="registration-benefits">
                                        {benefits.map((benefit) => (
                                            <span key={benefit}><CheckCircle2 size={16} /> {benefit}</span>
                                        ))}
                                    </div>
                                </Reveal>
                                <Reveal className="registration-hero-visual" delay={0.12}>
                                    <Card className="registration-quick-card">
                                        <CardHeader>
                                            <GraduationCap />
                                            <CardTitle>Mulai pendaftaran</CardTitle>
                                            <CardDescription>Proses cepat, tanpa ribet</CardDescription>
                                        </CardHeader>
                                        <CardContent>
                                            <div className="registration-steps-preview">
                                                {steps.map(({ icon: Icon, title, text }, index) => (
                                                    <div key={title} className="registration-step-item">
                                                        <div className="registration-step-num">{index + 1}</div>
                                                        <div>
                                                            <strong>{title}</strong>
                                                            <p>{text}</p>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </CardContent>
                                        <CardFooter>
                                            <Button asChild className="w-full">
                                                <a href={hero?.buttonPrimary?.link || '#daftar'}>
                                                   Mulai sekarang <ChevronRight />
                                                </a>
                                            </Button>
                                        </CardFooter>
                                    </Card>
                                </Reveal>
                            </div>
                        </section>
                        <div className="registration-trust-strip">
                            <div className="shell registration-trust-inner">
                                <span><Users size={16} /> Ribuan pendaftar setiap tahun</span>
                                <span><Award size={16} /> Terakreditasi unggul</span>
                                <span><CalendarCheck size={16} /> Gelombang pendaftaran sedang dibuka</span>
                            </div>
                        </div>
                    </React.Fragment>
                );

            case 'feature':
                return (
                    <section key={key} id="alur" className="registration-steps-section">
                        <div className="shell">
                            <Reveal className="registration-section-heading">
                                <span className="eyebrow">{section.pre_title || 'Cara mendaftar'}</span>
                                <h2>{section.title || 'Langkah mudah menuju masa depan'}</h2>
                                <p>{combinedText(section, 'Ikuti tiga langkah sederhana untuk memulai perjalanan akademik Anda.')}</p>
                            </Reveal>
                            <Stagger className="registration-steps-grid">
                                {steps.map(({ icon: Icon, title, text }, index) => (
                                    <SpotlightCard key={title} className="registration-step-card">
                                        <div className="registration-step-header">
                                            <div className="registration-step-icon"><Icon /></div>
                                            <span className="registration-step-label">Langkah {index + 1}</span>
                                        </div>
                                        <CardHeader>
                                            <CardTitle>{title}</CardTitle>
                                            <CardDescription>{text}</CardDescription>
                                        </CardHeader>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                );

            case 'product':
                return (
                    <Section key={key} section={section} tint
                        eyebrow={section.pre_title || 'Program kami'}
                        title={section.title || 'Pilih program yang sesuai'}
                        text={combinedText(section, 'Berbagai pilihan program pendidikan untuk mengembangkan potensi Anda.')}
                    >
                        <Stagger className="registration-program-grid">
                            {landing?.products?.length > 0
                                ? landing.products.slice(0, section.limit_data || 6).map((product) => (
                                    <SpotlightCard key={product.id} className="registration-program-card">
                                        {product.image && <img src={product.image} alt={product.name} />}
                                        <CardHeader>
                                            <CardTitle>{product.name}</CardTitle>
                                            {product.shortDescription && <CardDescription>{product.shortDescription}</CardDescription>}
                                        </CardHeader>
                                        <CardFooter>
                                            <Button asChild variant="outline" size="sm">
                                                <a href={product.demoUrl || '#'}>Detail program <ArrowRight size={14} /></a>
                                            </Button>
                                        </CardFooter>
                                    </SpotlightCard>
                                ))
                                : <p className="text-muted">Belum ada data program.</p>}
                        </Stagger>
                    </Section>
                );

            case 'statistic':
                return landing?.statistics?.length > 0 ? (
                    <section key={key} className="registration-stats">
                        <div className="shell">
                            <SectionHeader section={section} />
                            <Stagger className="registration-stats-grid">
                                {landing.statistics.slice(0, section.limit_data || 4).map((stat) => (
                                    <SpotlightCard key={stat.id} className="registration-stat-card">
                                        <strong>{stat.value}</strong>
                                        <span>{stat.label}</span>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                ) : null;

            case 'client':
                return <PartnerCloud key={key} partners={data.partners} section={section} />;

            case 'testimonial':
                return data.testimonials?.length > 0 ? (
                    <section key={key} className="registration-testimonials">
                        <div className="shell">
                            <SectionHeader section={section} />
                            <Stagger className="registration-testimonial-grid">
                                {data.testimonials.slice(0, section.limit_data || 6).map((testimonial) => (
                                    <SpotlightCard key={testimonial.id} className="registration-testimonial-card">
                                        <Quote className="registration-testimonial-quote" />
                                        <blockquote>"{testimonial.quote}"</blockquote>
                                        <div className="registration-testimonial-person">
                                            {testimonial.photo
                                                ? <img src={testimonial.photo} alt={testimonial.name} />
                                                : <span className="avatar-fallback">{testimonial.name.slice(0, 2).toUpperCase()}</span>}
                                            <div>
                                                <strong>{testimonial.name}</strong>
                                                <small>{testimonial.position}{testimonial.organization ? ` · ${testimonial.organization}` : ''}</small>
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
                        eyebrow={section.pre_title || 'Informasi terbaru'}
                        title={section.title || 'Pengumuman pendaftaran'}
                        text={combinedText(section, 'Ikuti informasi terbaru seputar jadwal, persyaratan, dan kegiatan.')}
                    >
                        <NewsGrid announcements={data.announcements} section={section} />
                    </Section>
                );

            case 'faq':
                return (
                    <Section key={key} section={section} dark
                        eyebrow={section.pre_title || 'Punya pertanyaan?'}
                        title={section.title || 'Hal-hal yang sering ditanyakan'}
                        text={combinedText(section)}
                        narrow
                    >
                        <FaqSection faqs={data.faqs} />
                    </Section>
                );

            case 'cta':
                return (
                    <section key={key} className="registration-cta">
                        <div className="shell">
                            <Reveal className="registration-cta-card">
                                <div>
                                    <span className="eyebrow">{section.pre_title || 'Mulai perjalanan Anda'}</span>
                                    <h2>{section.title || 'Bergabunglah dengan ribuan mahasiswa lainnya.'}</h2>
                                    {section.subtitle && <p>{section.subtitle}</p>}
                                </div>
                                <Button asChild size="lg">
                                    <Link href={data.site.contactUrl}>
                                        Daftar sekarang <ArrowRight />
                                    </Link>
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
