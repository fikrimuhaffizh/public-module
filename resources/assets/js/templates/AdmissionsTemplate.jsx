import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    CalendarDays,
    CheckCircle2,
    Clock,
    Download,
    FileText,
    GraduationCap,
    ShieldCheck,
    Sparkles,
    Upload,
    Wallet,
} from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';
import {
    FaqSection,
    NewsGrid,
    PartnerCloud,
    PagesGrid,
    Section,
    TestimonialSection,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

const steps = [
    { icon: FileText, title: 'Buat akun', text: 'Daftarkan diri pada portal penerimaan mahasiswa baru.' },
    { icon: Upload, title: 'Isi formulir', text: 'Lengkapi data diri dan unggah berkas persyaratan.' },
    { icon: CheckCircle2, title: 'Verifikasi', text: 'Tim penerimaan memverifikasi berkas Anda.' },
    { icon: GraduationCap, title: 'Diterima', text: 'Terima hasil seleksi dan lakukan daftar ulang.' },
];

const why = [
    { icon: Clock, title: 'Proses cepat', text: 'Pendaftaran online tanpa antre, hasil dalam 1×24 jam.' },
    { icon: ShieldCheck, title: 'Aman & transparan', text: 'Data terlindungi dan seleksi berjalan adil.' },
    { icon: Wallet, title: 'Biaya jelas', text: 'Informasi biaya dan beasiswa tersedia di muka.' },
    { icon: Sparkles, title: 'Pendampingan', text: 'Tim PMB membantu setiap tahap pendaftaran.' },
];

export default function AdmissionsTemplate({ data }) {
    const sections = data.sections || [];
    const { landing, site } = data;
    const hero = landing?.hero;
    const heroImage = hero?.image || data.slides?.[0]?.image;
    const stats = landing?.statistics || [];

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;
        const copy = heroCopy(section, hero, site);

        switch (sectionKey(section)) {
            case 'hero':
                return (
                    <React.Fragment key={key}>
                        <section className="admissions-hero">
                            <div className="admissions-hero-grid">
                                <Reveal className="admissions-hero-copy">
                                    <Badge className="admissions-badge">
                                        <CalendarDays size={14} /> {section.pre_title || 'Pendaftaran dibuka'}
                                    </Badge>
                                    <h1>{copy.title}</h1>
                                    <p>{copy.subtitle}</p>
                                    <div className="hero-actions">
                                        <Button asChild size="lg">
                                            <a href={hero?.buttonPrimary?.link || site.loginUrl}>
                                                {hero?.buttonPrimary?.text || 'Daftar sekarang'} <ArrowRight size={18} />
                                            </a>
                                        </Button>
                                        <Button variant="outline" asChild size="lg">
                                            <Link href={hero?.buttonSecondary?.link || site.contactUrl}>
                                                <Download size={16} /> {hero?.buttonSecondary?.text || 'Unduh brosur'}
                                            </Link>
                                        </Button>
                                    </div>
                                </Reveal>
                                <Reveal className="admissions-hero-visual" delay={0.15}>
                                    <div className="admissions-photo">
                                        {heroImage
                                            ? <img src={heroImage} alt={copy.imageAlt} />
                                            : <div className="admissions-photo-fallback"><GraduationCap size={64} /></div>}
                                        <div className="admissions-photo-card">
                                            <strong>Gelombang II</strong>
                                            <span>Ditutup 30 Agustus</span>
                                        </div>
                                    </div>
                                </Reveal>
                            </div>
                        </section>
                        <section className="admissions-steps">
                            <div className="shell">
                                <Reveal className="admissions-steps-head">
                                    <span className="eyebrow">Alur pendaftaran</span>
                                    <h2>Empat langkah menuju kampus</h2>
                                </Reveal>
                                <Stagger className="admissions-steps-grid">
                                    {steps.map((s, i) => (
                                        <SpotlightCard key={s.title} className="admissions-step">
                                            <span className="admissions-step-num">{i + 1}</span>
                                            <span className="admissions-step-icon"><s.icon size={22} /></span>
                                            <h3>{s.title}</h3>
                                            <p>{s.text}</p>
                                        </SpotlightCard>
                                    ))}
                                </Stagger>
                            </div>
                        </section>
                    </React.Fragment>
                );

            case 'feature':
                return (
                    <Section key={key} section={section} tint
                        eyebrow={section.pre_title || 'Keunggulan pendaftaran'}
                        title={section.title || 'Pendaftaran yang mudah, transparan, dan terbimbing'}
                        text={combinedText(section, 'Kami hadirkan pengalaman pendaftaran modern yang mengutamakan kenyamanan calon mahasiswa.')}
                    >
                        <Stagger className="admissions-why-grid">
                            {(landing?.features || []).slice(0, section.limit_data || 4).map((f, i) => {
                                const Icon = (why[i % why.length] || why[0]).icon;
                                return (
                                    <SpotlightCard key={f.id} className="admissions-why-card">
                                        <span className="admissions-why-icon"><Icon /></span>
                                        <h3>{f.title}</h3>
                                        <p>{f.description}</p>
                                    </SpotlightCard>
                                );
                            })}
                            {(landing?.features || []).length === 0 && why.map(({ icon: Icon, title, text }) => (
                                <SpotlightCard key={title} className="admissions-why-card">
                                    <span className="admissions-why-icon"><Icon /></span>
                                    <h3>{title}</h3>
                                    <p>{text}</p>
                                </SpotlightCard>
                            ))}
                        </Stagger>
                    </Section>
                );

            case 'product':
                return (
                    <Section key={key} section={section}
                        eyebrow={section.pre_title || 'Jalur & Program'}
                        title={section.title || 'Pilih jalur masuk yang sesuai untukmu'}
                        text={combinedText(section, 'Beragam jalur seleksi dan program studi siap menampung calon mahasiswa terbaik.')}
                    >
                        <PagesGrid pages={data.pages} section={section} />
                    </Section>
                );

            case 'statistic':
                return stats.length > 0 ? (
                    <section key={key} className="admissions-stat-band">
                        <div className="shell admissions-stat-grid">
                            {stats.slice(0, section.limit_data || 4).map(s => (
                                <Reveal key={s.id} className="admissions-stat">
                                    <strong>{s.value}</strong>
                                    <span>{s.label}</span>
                                </Reveal>
                            ))}
                        </div>
                    </section>
                ) : null;

            case 'client':
                return <PartnerCloud key={key} partners={data.partners} section={section} />;

            case 'testimonial':
                return <TestimonialSection key={key} testimonials={data.testimonials} section={section} />;

            case 'pengumuman':
                return (
                    <Section key={key} section={section}
                        eyebrow={section.pre_title || 'Informasi pendaftaran'}
                        title={section.title || 'Pengumuman dan jadwal terbaru'}
                        text={combinedText(section)}
                    >
                        <NewsGrid announcements={data.announcements} section={section} />
                    </Section>
                );

            case 'faq':
                return (
                    <Section key={key} section={section} narrow
                        eyebrow={section.pre_title || 'Punya pertanyaan?'}
                        title={section.title || 'Hal-hal yang sering ditanyakan'}
                        text={combinedText(section)}
                    >
                        <FaqSection faqs={data.faqs} />
                    </Section>
                );

            case 'cta':
                return (
                    <section key={key} className="admissions-cta">
                        <div className="shell admissions-cta-card">
                            <Reveal>
                                <span className="eyebrow">{section.pre_title || 'Mulai perjalananmu'}</span>
                                <h2>{section.title || 'Wujudkan cita-cita, mulai dari sini.'}</h2>
                                {(section.subtitle || section.post_title) && <p>{combinedText(section)}</p>}
                                <Button asChild size="lg">
                                    <Link href={site.loginUrl}>Daftar sekarang <ArrowRight size={18} /></Link>
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
