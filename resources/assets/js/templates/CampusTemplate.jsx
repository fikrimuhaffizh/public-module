import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Award,
    FlaskConical,
    Globe2,
    GraduationCap,
    ShieldCheck,
    Sparkles,
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
    SectionHeader,
    TestimonialSection,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

const highlights = [
    { icon: GraduationCap, title: 'Pendidikan berkualitas', text: 'Kurikulum relevan industri dengan pengajar berpengalaman dan berprestasi.' },
    { icon: FlaskConical, title: 'Riset & inovasi', text: 'Pusat penelitian aktif yang melahirkan karya nyata bagi masyarakat.' },
    { icon: Globe2, title: 'Kemitraan global', text: 'Kesempatan pertukaran dan kolaborasi lintas negara yang membuka wawasan.' },
    { icon: ShieldCheck, title: 'Akreditasi unggul', text: 'Program studi terakreditasi dengan standar mutu nasional terbaik.' },
];

export default function CampusTemplate({ data }) {
    const sections = data.sections || [];
    const { landing, site } = data;
    const hero = landing?.hero;
    const heroImage = hero?.image || data.slides?.[0]?.image;
    const stats = landing?.statistics || [];
    const hasStatsSection = sections.some(s => s.is_active && sectionKey(s) === 'statistic');

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;
        const copy = heroCopy(section, hero, site);

        switch (sectionKey(section)) {
            case 'hero':
                return (
                    <React.Fragment key={key}>
                        <section className="campus-hero">
                            <div className="campus-grid-bg" aria-hidden="true" />
                            <div className="campus-blob campus-blob--one" />
                            <div className="campus-blob campus-blob--two" />
                            <div className="shell campus-hero-grid">
                                <Reveal className="campus-hero-copy">
                                    <Badge className="campus-badge">
                                        <Sparkles size={14} /> {section.pre_title || 'Universitas Digital'}
                                    </Badge>
                                    <h1>{copy.title}</h1>
                                    <p>{copy.subtitle}</p>
                                    <div className="hero-actions">
                                        <Button asChild size="lg">
                                            <a href={hero?.buttonPrimary?.link || site.loginUrl}>
                                                {hero?.buttonPrimary?.text || 'Masuk Portal'} <ArrowRight size={18} />
                                            </a>
                                        </Button>
                                        <Button variant="outline" asChild size="lg">
                                            <Link href={hero?.buttonSecondary?.link || site.contactUrl}>
                                                {hero?.buttonSecondary?.text || 'Kunjungi kampus'}
                                            </Link>
                                        </Button>
                                    </div>
                                    {!hasStatsSection && stats.length > 0 && (
                                        <div className="campus-hero-stats">
                                            {stats.slice(0, 3).map(s => (
                                                <div key={s.id}>
                                                    <strong>{s.value}</strong>
                                                    <span>{s.label}</span>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </Reveal>
                                <Reveal className="campus-hero-visual" delay={0.15}>
                                    <div className="campus-photo">
                                        {heroImage
                                            ? <img src={heroImage} alt={copy.imageAlt} />
                                            : <div className="campus-photo-fallback"><GraduationCap size={64} /></div>}
                                        <div className="campus-photo-badge">
                                            <Award size={18} /> Akreditasi A
                                        </div>
                                    </div>
                                </Reveal>
                            </div>
                        </section>
                    </React.Fragment>
                );

            case 'feature':
                return (
                    <Section key={key} section={section}
                        eyebrow={section.pre_title || 'Mengapa kampus kami'}
                        title={section.title || 'Ruang tumbuh untuk calon pemimpin masa depan'}
                        text={combinedText(section, 'Kami menggabungkan akademik, riset, dan pengalaman nyata dalam satu ekosistem.')}
                    >
                        <Stagger className="campus-highlight-grid">
                            {(landing?.features || []).slice(0, section.limit_data || 4).map((f, i) => {
                                const Icon = highlights[i % highlights.length]?.icon;
                                return (
                                    <SpotlightCard key={f.id} className="campus-highlight-card">
                                        <span className="campus-highlight-icon">
                                            {f.icon ? <i className={f.icon} /> : <Icon />}
                                        </span>
                                        <h3>{f.title}</h3>
                                        <p>{f.description}</p>
                                    </SpotlightCard>
                                );
                            })}
                            {(landing?.features || []).length === 0 && highlights.map(({ icon: Icon, title, text }) => (
                                <SpotlightCard key={title} className="campus-highlight-card">
                                    <span className="campus-highlight-icon"><Icon /></span>
                                    <h3>{title}</h3>
                                    <p>{text}</p>
                                </SpotlightCard>
                            ))}
                        </Stagger>
                    </Section>
                );

            case 'product':
                return (
                    <Section key={key} section={section} tint
                        eyebrow={section.pre_title || 'Fakultas & Program'}
                        title={section.title || 'Temukan program studi yang sesuai dengan minatmu'}
                        text={combinedText(section, 'Pilihan program akademik yang dirancang untuk siap kerja dan berdaya saing.')}
                    >
                        <PagesGrid pages={data.pages} section={section} />
                    </Section>
                );

            case 'statistic':
                return stats.length > 0 ? (
                    <section key={key} className="campus-stat-band">
                        <div className="shell campus-stat-grid">
                            {stats.slice(0, section.limit_data || 4).map(s => (
                                <Reveal key={s.id} className="campus-stat">
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
                    <Section key={key} section={section} id="berita"
                        eyebrow={section.pre_title || 'Kabar kampus'}
                        title={section.title || 'Berita dan pengumuman terbaru'}
                        text={combinedText(section)}
                    >
                        <NewsGrid announcements={data.announcements} section={section} />
                    </Section>
                );

            case 'faq':
                return (
                    <Section key={key} section={section} narrow
                        eyebrow={section.pre_title || 'Informasi akademik'}
                        title={section.title || 'Pertanyaan yang sering diajukan'}
                        text={combinedText(section)}
                    >
                        <FaqSection faqs={data.faqs} />
                    </Section>
                );

            case 'cta':
                return (
                    <section key={key} className="campus-cta">
                        <div className="shell campus-cta-card">
                            <Reveal>
                                <span className="eyebrow">{section.pre_title || 'Gabung bersama kami'}</span>
                                <h2>{section.title || `Jadi bagian dari ${site.name}`}</h2>
                                {(section.subtitle || section.post_title) && <p>{combinedText(section)}</p>}
                                <Button asChild size="lg">
                                    <Link href={site.contactUrl}>Hubungi kami <ArrowRight size={18} /></Link>
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
