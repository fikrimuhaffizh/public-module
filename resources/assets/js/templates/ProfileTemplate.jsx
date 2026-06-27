import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Award,
    Banknote,
    BarChart3,
    BookOpen,
    Building2,
    CheckCircle2,
    ChevronRight,
    Clock,
    Compass,
    ExternalLink,
    GraduationCap,
    HeartHandshake,
    Lightbulb,
    MapPin,
    MessageSquare,
    Phone,
    Quote,
    Shield,
    Target,
    TrendingUp,
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
    CtaSection,
    FaqSection,
    NewsGrid,
    PartnerCloud,
    Section,
    SectionHeader,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

// ─── Premium milestones (company journey) ─────────────────────────────────
const milestones = [
    {
        year: '2018',
        icon: Compass,
        title: 'Perencanaan & Fondasi',
        text: 'Menyusun arsitektur sistem dan kebutuhan teknis bersama tim ahli multidisiplin.',
    },
    {
        year: '2020',
        icon: Building2,
        title: 'Pengembangan & Integrasi',
        text: 'Membangun dan mengintegrasikan modul-modul inti sistem informasi terpadu.',
    },
    {
        year: '2022',
        icon: TrendingUp,
        title: 'Implementasi & Skala',
        text: 'Meluncurkan platform dan mengoptimalkan berdasarkan umpan balik pengguna di 50+ institusi.',
    },
    {
        year: '2024',
        icon: Award,
        title: 'Ekspansi & Inovasi',
        text: 'Menjangkau lebih banyak institusi dengan solusi AI dan analitik cerdas.',
    },
];

// ─── Core values (used as feature section content) ─────────────────────────
const values = [
    {
        icon: Target,
        title: 'Visi Strategis',
        text: 'Kami percaya teknologi adalah katalis untuk transformasi pendidikan yang lebih inklusif dan berdaya saing.',
    },
    {
        icon: HeartHandshake,
        title: 'Kolaborasi Erat',
        text: 'Bekerja bersama institusi untuk menghadirkan solusi yang tepat sasaran dan berkelanjutan.',
    },
    {
        icon: Lightbulb,
        title: 'Inovasi Berkelanjutan',
        text: 'Terus mengembangkan sistem mengikuti kebutuhan dan perkembangan zaman dengan riset mendalam.',
    },
];

// ─── Contact info sidebar items ───────────────────────────────────────────
const contactItems = [
    { icon: MapPin, label: 'Kantor Pusat', value: 'Jl. Pendidikan No. 123, Jakarta' },
    { icon: Phone, label: 'Telepon', value: '+62 21 1234 5678' },
    { icon: MessageSquare, label: 'Email', value: 'hello@perusahaan.co.id' },
    { icon: Clock, label: 'Jam Kerja', value: 'Sen–Jum, 08:00–17:00 WIB' },
];

// ─── Stat cards data ──────────────────────────────────────────────────────
const statCards = [
    { icon: Building2, value: '50+', label: 'Institusi Mitra' },
    { icon: Users, value: '10.000+', label: 'Pengguna Aktif' },
    { icon: BarChart3, value: '98%', label: 'Kepuasan Klien' },
    { icon: Award, value: '5', label: 'Penghargaan' },
];

// ─── Service cards (shown when no products) ────────────────────────────────
const serviceCards = [
    {
        icon: GraduationCap,
        title: 'Sistem Akademik',
        text: 'Platform manajemen akademikmenyeluruh yang mencakup kurikulum, KRS, nilai, dan wisuda.',
    },
    {
        icon: Banknote,
        title: 'Manajemen Keuangan',
        text: 'Modul keuangan terintegrasi untuk UKT, beasiswa, gaji, dan pelaporan real-time.',
    },
    {
        icon: Shield,
        title: 'Keamanan Data',
        text: 'Sistem enkripsi multi-layer dan kepatuhan terhadap standar perlindungan data nasional.',
    },
    {
        icon: BookOpen,
        title: 'E-Learning',
        text: 'LMS modern dengan dukungan konten interaktif, forum diskusi, dan penilaian otomatis.',
    },
    {
        icon: BarChart3,
        title: 'Analitik & AI',
        text: 'Dashboard prediktif berbasis AI untuk mengidentifikasi tren dan risiko akademik.',
    },
    {
        icon: Users,
        title: 'SDM & Organisasi',
        text: 'Manajemen sumber daya manusia dari rekrutmen hingga pengembangan karir.',
    },
];

export default function ProfileTemplate({ data }) {
    const sections = data.sections || [];
    const { landing } = data;
    const hero = landing?.hero;

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;
        const copy = heroCopy(section, hero, data.site);

        switch (sectionKey(section)) {
            // ── HERO ─────────────────────────────────────────────────
            case 'hero':
                return (
                    <React.Fragment key={key}>
                        {/* Hero Banner */}
                        <section className="profile-hero">
                            <div className="shell">
                                <div className="profile-hero-grid">
                                    <Reveal className="profile-hero-copy">
                                        <Badge className="profile-hero-badge">
                                            <Award size={14} /> {section.pre_title || 'Tentang Kami'}
                                        </Badge>
                                        <h1 className="profile-hero-title">{copy.title}</h1>
                                        <p className="profile-hero-sub">{copy.subtitle}</p>
                                        <div className="profile-hero-actions">
                                            <Button asChild size="lg">
                                                <a href={hero?.buttonPrimary?.link || '#perjalanan'}>
                                                    {hero?.buttonPrimary?.text || 'Jelajahi Perjalanan'} <ArrowRight />
                                                </a>
                                            </Button>
                                            <Button variant="outline" asChild size="lg" className="profile-hero-btn-outline">
                                                <Link href={hero?.buttonSecondary?.link || data.site.contactUrl}>
                                                    {hero?.buttonSecondary?.text || 'Hubungi Tim'}
                                                </Link>
                                            </Button>
                                        </div>

                                        {/* Trusted badge */}
                                        <div className="profile-hero-trusted">
                                            <span className="profile-hero-trusted-label">Dipercaya oleh</span>
                                            <div className="profile-hero-trusted-logos">
                                                <span>Universitas Indonesia</span>
                                                <span>ITB</span>
                                                <span>UGM</span>
                                                <span>+47 institusi</span>
                                            </div>
                                        </div>
                                    </Reveal>

                                    <Reveal className="profile-hero-visual" delay={0.15}>
                                        <div className="profile-hero-image-frame">
                                            {hero?.image
                                                ? <img src={hero.image} alt={copy.imageAlt} className="profile-hero-image" />
                                                : (
                                                    <div className="profile-hero-image-placeholder">
                                                        <Building2 size={80} />
                                                        <span>Visual Perusahaan</span>
                                                    </div>
                                                )
                                            }
                                            {/* Floating stat cards */}
                                            <div className="profile-hero-float-card profile-hero-float-card--1">
                                                <Shield size={18} />
                                                <div>
                                                    <strong>ISO 27001</strong>
                                                    <span>Certified</span>
                                                </div>
                                            </div>
                                            <div className="profile-hero-float-card profile-hero-float-card--2">
                                                <Award size={18} />
                                                <div>
                                                    <strong>TOP 10</strong>
                                                    <span>EdTech 2024</span>
                                                </div>
                                            </div>
                                        </div>
                                    </Reveal>
                                </div>
                            </div>
                        </section>

                        {/* Stats Strip */}
                        <section className="profile-stats-strip">
                            <div className="shell">
                                <div className="profile-stats-strip-grid">
                                    {statCards.map(({ icon: Icon, value, label }) => (
                                        <div key={label} className="profile-stats-strip-item">
                                            <Icon size={22} />
                                            <div>
                                                <strong>{value}</strong>
                                                <span>{label}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </section>
                    </React.Fragment>
                );

            // ── FEATURES / VALUES ────────────────────────────────────
            case 'feature':
                return (
                    <section key={key} className="profile-values">
                        <div className="shell">
                            <Reveal className="profile-section-heading">
                                <span className="eyebrow">{section.pre_title || 'Nilai-Nilai Kami'}</span>
                                <h2>{section.title || 'Prinsip yang Menuntun Setiap Langkah'}</h2>
                                <p>{combinedText(section, 'Setiap keputusan dan inovasi lahir dari nilai-nilai yang kami pegang teguh.')}</p>
                            </Reveal>
                            <Stagger className="profile-values-grid">
                                {values.map(({ icon: Icon, title, text }) => (
                                    <SpotlightCard key={title} className="profile-value-card">
                                        <div className="profile-value-icon"><Icon size={22} /></div>
                                        <h3>{title}</h3>
                                        <p>{text}</p>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                );

            // ── STATISTICS ────────────────────────────────────────────
            case 'statistic':
                return landing?.statistics?.length > 0 ? (
                    <section key={key} className="profile-stats-section">
                        <div className="shell">
                            <Reveal className="profile-section-heading profile-section-heading--center">
                                <span className="eyebrow">{section.pre_title || 'Capaian'}</span>
                                <h2>{section.title || 'Dampak yang Telah Kami Berikan'}</h2>
                                <p>{combinedText(section, 'Angka berbicara lebih keras — berikut capaian kami dalam mendigitalisasi pendidikan.')}</p>
                            </Reveal>
                            <Stagger className="profile-stats-grid">
                                {landing.statistics.slice(0, section.limit_data || 4).map((stat) => (
                                    <SpotlightCard key={stat.id} className="profile-stat-card">
                                        <strong>{stat.value}</strong>
                                        <span>{stat.label}</span>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                ) : null;

            // ── PRODUCTS / JOURNEY ───────────────────────────────────
            case 'product':
                if (landing?.products?.length > 0) {
                    return (
                        <section key={key} className="profile-products">
                            <div className="shell">
                                <Reveal className="profile-section-heading">
                                    <span className="eyebrow">{section.pre_title || 'Layanan Kami'}</span>
                                    <h2>{section.title || 'Solusi untuk Institusi Pendidikan'}</h2>
                                    <p>{combinedText(section, 'Kami menghadirkan layanan yang dirancang untuk menjawab tantangan digital kampus.')}</p>
                                </Reveal>
                                <Stagger className="profile-products-grid">
                                    {landing.products.slice(0, section.limit_data || 6).map((product) => (
                                        <SpotlightCard key={product.id} className="profile-product-card">
                                            <div className="profile-product-img">
                                                {product.image
                                                    ? <img src={product.image} alt={product.name} />
                                                    : <div className="profile-product-img-fallback"><Building2 size={28} /></div>
                                                }
                                            </div>
                                            <div className="profile-product-body">
                                                <h3>{product.name}</h3>
                                                <p>{product.shortDescription || product.description}</p>
                                                {product.demoUrl && (
                                                    <a href={product.demoUrl} target="_blank" rel="noreferrer" className="profile-text-link">
                                                        Pelajari <ChevronRight size={14} />
                                                    </a>
                                                )}
                                            </div>
                                        </SpotlightCard>
                                    ))}
                                </Stagger>
                            </div>
                        </section>
                    );
                }

                // Fallback: company journey timeline
                return (
                    <section key={key} className="profile-journey">
                        <div className="shell">
                            <Reveal className="profile-section-heading profile-section-heading--center">
                                <span className="eyebrow">{section.pre_title || 'Perjalanan'}</span>
                                <h2>{section.title || 'Perjalanan Kami'}</h2>
                                <p>{combinedText(section, 'Dari awal yang sederhana hingga menjadi mitra terpercaya institusi pendidikan.')}</p>
                            </Reveal>
                            <div className="profile-journey-timeline">
                                {milestones.map(({ icon: Icon, year, title, text }, idx) => (
                                    <Reveal key={title} className="profile-journey-item" delay={idx * 0.08}>
                                        <div className="profile-journey-marker">
                                            <Icon size={18} />
                                        </div>
                                        <div className="profile-journey-card">
                                            <span className="profile-journey-year">{year}</span>
                                            <h3>{title}</h3>
                                            <p>{text}</p>
                                        </div>
                                    </Reveal>
                                ))}
                            </div>
                        </div>
                    </section>
                );

            // ── CLIENTS / PARTNERS ───────────────────────────────────
            case 'client':
                return <PartnerCloud key={key} partners={data.partners} section={section} />;

            // ── TESTIMONIALS ──────────────────────────────────────────
            case 'testimonial':
                return data.testimonials?.length > 0 ? (
                    <section key={key} className="profile-testimonials">
                        <div className="shell">
                            <Reveal className="profile-section-heading profile-section-heading--center">
                                <span className="eyebrow">{section.pre_title || 'Testimonial'}</span>
                                <h2>{section.title || 'Apa Kata Mitra Kami'}</h2>
                                <p>{combinedText(section, 'Kepercayaan dan pengalaman mitra adalah bukti nyata komitmen kami.')}</p>
                            </Reveal>
                            <Stagger className="profile-testimonial-grid">
                                {data.testimonials.slice(0, section.limit_data || 6).map((testimonial) => (
                                    <SpotlightCard key={testimonial.id} className="profile-testimonial-card">
                                        <Quote className="profile-testimonial-quote" />
                                        <blockquote>"{testimonial.quote}"</blockquote>
                                        <div className="profile-testimonial-author">
                                            {testimonial.photo
                                                ? <img src={testimonial.photo} alt={testimonial.name} />
                                                : <span className="profile-avatar">{testimonial.name.slice(0, 2).toUpperCase()}</span>
                                            }
                                            <div>
                                                <strong>{testimonial.name}</strong>
                                                <span>{testimonial.position}{testimonial.organization ? ` · ${testimonial.organization}` : ''}</span>
                                            </div>
                                        </div>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                ) : null;

            // ── NEWS / PENGUMUMAN ────────────────────────────────────
            case 'pengumuman':
                return (
                    <Section key={key} section={section}
                        eyebrow={section.pre_title || 'Kabar Terbaru'}
                        title={section.title || 'Perkembangan dan Cerita Kami'}
                        text={combinedText(section, 'Ikuti perjalanan kami dalam menghadirkan solusi digital untuk pendidikan.')}
                    >
                        <NewsGrid announcements={data.announcements} section={section} editorial />
                    </Section>
                );

            // ── FAQ ───────────────────────────────────────────────────
            case 'faq':
                return (
                    <section key={key} className="profile-faq">
                        <div className="shell">
                            <Reveal className="profile-section-heading profile-section-heading--center">
                                <span className="eyebrow">{section.pre_title || 'FAQ'}</span>
                                <h2>{section.title || 'Informasi yang Sering Dicari'}</h2>
                                <p>{combinedText(section)}</p>
                            </Reveal>
                            <div className="profile-faq-list">
                                <FaqSection faqs={data.faqs} />
                            </div>
                        </div>
                    </section>
                );

            // ── CTA ───────────────────────────────────────────────────
            case 'cta':
                return (
                    <section key={key} className="profile-cta">
                        <div className="shell">
                            <Reveal className="profile-cta-inner">
                                <span className="profile-cta-eyebrow">{section.pre_title || 'Mulai Kolaborasi'}</span>
                                <h2>{section.title || 'Mari Bersama Membangun Ekosistem Digital yang Lebih Baik'}</h2>
                                {section.subtitle && <p>{section.subtitle}</p>}
                                <div className="profile-cta-contact">
                                    <Button asChild size="lg">
                                        <Link href={data.site.contactUrl}>
                                            Hubungi Kami <ArrowRight />
                                        </Link>
                                    </Button>
                                    <div className="profile-cta-contact-info">
                                        {contactItems.slice(0, 2).map(({ icon: Icon, value }) => (
                                            <span key={value}><Icon size={14} /> {value}</span>
                                        ))}
                                    </div>
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