import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    BarChart3,
    LineChart,
    Target,
    TrendingUp,
    Users,
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

const reasons = [
    { icon: Target, title: 'Evaluasi kurikulum', text: 'Data lulusan menjadi dasar penyempurnaan kurikulum yang relevan.' },
    { icon: BarChart3, title: 'Dasar akreditasi', text: 'Capaian lulusan mendukung peringkat dan akreditasi prodi.' },
    { icon: Users, title: 'Jaringan alumni', text: 'Memetakan sebaran alumni untuk memperkuat relasi dan kerja sama.' },
    { icon: TrendingUp, title: 'Kebijakan strategis', text: 'Menjadi rujukan pengambilan keputusan institusi.' },
];

export default function TracerTemplate({ data }) {
    const sections = data.sections || [];
    const { landing, site } = data;
    const hero = landing?.hero;
    const stats = landing?.statistics || [];
    const fallbackMetrics = [
        { value: '87%', label: 'Terserap dunia kerja' },
        { value: '3.2 bln', label: 'Masa tunggu rata-rata' },
        { value: '4.5jt', label: 'Gaji rata-rata' },
    ];
    const metrics = stats.slice(0, 3).length ? stats.slice(0, 3) : fallbackMetrics;

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;
        const copy = heroCopy(section, hero, site);

        switch (sectionKey(section)) {
            case 'hero':
                return (
                    <React.Fragment key={key}>
                        <section className="tracer-hero">
                            <div className="tracer-grid-bg" aria-hidden="true" />
                            <div className="shell tracer-hero-grid">
                                <Reveal className="tracer-hero-copy">
                                    <Badge className="tracer-badge">
                                        <LineChart size={14} /> {section.pre_title || 'Tracer Study'}
                                    </Badge>
                                    <h1>{copy.title}</h1>
                                    <p>{copy.subtitle}</p>
                                    <div className="hero-actions">
                                        <Button asChild size="lg">
                                            <a href={hero?.buttonPrimary?.link || site.loginUrl}>
                                                {hero?.buttonPrimary?.text || 'Isi kuesioner'} <ArrowRight size={18} />
                                            </a>
                                        </Button>
                                        <Button variant="outline" asChild size="lg">
                                            <Link href={hero?.buttonSecondary?.link || site.contactUrl}>
                                                {hero?.buttonSecondary?.text || 'Lihat laporan'}
                                            </Link>
                                        </Button>
                                    </div>
                                </Reveal>
                                <Reveal className="tracer-hero-visual" delay={0.15}>
                                    <div className="tracer-dashboard">
                                        <div className="tracer-dash-bar"><i /><i /><i /><span>{site.name} · Tracer Study</span></div>
                                        <div className="tracer-dash-body">
                                            <div className="tracer-metric-grid">
                                                {metrics.map((m, i) => (
                                                    <div className="tracer-metric" key={i}>
                                                        <strong>{m.value}</strong>
                                                        <span>{m.label}</span>
                                                    </div>
                                                ))}
                                            </div>
                                            <div className="tracer-chart">
                                                <div className="tracer-bar" style={{ height: '42%' }} />
                                                <div className="tracer-bar" style={{ height: '70%' }} />
                                                <div className="tracer-bar" style={{ height: '55%' }} />
                                                <div className="tracer-bar" style={{ height: '88%' }} />
                                                <div className="tracer-bar" style={{ height: '64%' }} />
                                                <div className="tracer-bar" style={{ height: '76%' }} />
                                            </div>
                                            <div className="tracer-response">
                                                <div className="tracer-response-label"><span>Tingkat respons</span><strong>72%</strong></div>
                                                <div className="tracer-response-track"><i style={{ width: '72%' }} /></div>
                                            </div>
                                        </div>
                                    </div>
                                </Reveal>
                            </div>
                        </section>
                    </React.Fragment>
                );

            case 'statistic':
                return stats.length > 0 ? (
                    <section key={key} className="tracer-stat-band">
                        <div className="shell">
                            <SectionHeader section={section} />
                            <Stagger className="tracer-stat-grid">
                                {stats.slice(0, section.limit_data || 4).map(s => (
                                    <SpotlightCard key={s.id} className="tracer-stat-card">
                                        <strong>{s.value}</strong>
                                        <span>{s.label}</span>
                                    </SpotlightCard>
                                ))}
                            </Stagger>
                        </div>
                    </section>
                ) : null;

            case 'feature':
                return (
                    <Section key={key} section={section} tint
                        eyebrow={section.pre_title || 'Mengapa tracer study'}
                        title={section.title || 'Jejak lulusan untuk masa depan kampus'}
                        text={combinedText(section, 'Setiap respons alumni memperkuat kualitas pendidikan dan keberlanjutan institusi.')}
                    >
                        <Stagger className="tracer-reason-grid">
                            {(landing?.features || []).slice(0, section.limit_data || 4).map((f, i) => {
                                const Icon = (reasons[i % reasons.length] || reasons[0]).icon;
                                return (
                                    <SpotlightCard key={f.id} className="tracer-reason-card">
                                        <span className="tracer-reason-icon"><Icon /></span>
                                        <h3>{f.title}</h3>
                                        <p>{f.description}</p>
                                    </SpotlightCard>
                                );
                            })}
                            {(landing?.features || []).length === 0 && reasons.map(({ icon: Icon, title, text }) => (
                                <SpotlightCard key={title} className="tracer-reason-card">
                                    <span className="tracer-reason-icon"><Icon /></span>
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
                        eyebrow={section.pre_title || 'Laporan & layanan'}
                        title={section.title || 'Akses laporan tracer dan layanan alumni'}
                        text={combinedText(section)}
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
                        eyebrow={section.pre_title || 'Rilis & laporan'}
                        title={section.title || 'Publikasi tracer study terbaru'}
                        text={combinedText(section)}
                    >
                        <NewsGrid announcements={data.announcements} section={section} />
                    </Section>
                );

            case 'faq':
                return (
                    <Section key={key} section={section} narrow
                        eyebrow={section.pre_title || 'Tentang tracer study'}
                        title={section.title || 'Pertanyaan yang sering diajukan'}
                        text={combinedText(section)}
                    >
                        <FaqSection faqs={data.faqs} />
                    </Section>
                );

            case 'cta':
                return (
                    <section key={key} className="tracer-cta">
                        <div className="shell tracer-cta-card">
                            <Reveal>
                                <span className="eyebrow">{section.pre_title || 'Kontribusi datamu'}</span>
                                <h2>{section.title || 'Ceritakan perjalananmu setelah lulus.'}</h2>
                                {(section.subtitle || section.post_title) && <p>{combinedText(section)}</p>}
                                <Button asChild size="lg">
                                    <Link href={site.loginUrl}>Isi kuesioner <ArrowRight size={18} /></Link>
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
