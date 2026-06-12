import React from 'react';
import { ArrowRight, Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { BackgroundBeams, Marquee, Reveal } from '@public/components/motion/effects';
import {
    CtaSection,
    FaqSection,
    NewsGrid,
    PartnerCloud,
    PagesGrid,
    PlatformOverview,
    Section,
    TestimonialSection,
    ValueStrip,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

export default function ModernTemplate({ data }) {
    const sections = data.sections || [];
    const hero = data.landing?.hero;

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;
        const copy = heroCopy(section, hero, data.site);

        switch (sectionKey(section)) {
            case 'hero':
                return (
                    <React.Fragment key={key}>
                        <section className="hero hero--modern">
                            <BackgroundBeams />
                            <div className="hero-orb hero-orb--one" />
                            <div className="hero-orb hero-orb--two" />
                            <div className="shell modern-hero-grid">
                                <Reveal className="hero-content">
                                    <Badge><Sparkles size={14} /> {section.pre_title || 'Digital campus platform'}</Badge>
                                    <h1>{copy.title}</h1>
                                    {copy.subtitle && <p className="hero-subtitle">{copy.subtitle}</p>}
                                    <p>{copy.description}</p>
                                    <div className="hero-actions">
                                        <Button asChild size="lg">
                                            <a href={hero?.buttonPrimary?.link || '#informasi'}>
                                                {hero?.buttonPrimary?.text || 'Mulai menjelajah'} <ArrowRight size={18} />
                                            </a>
                                        </Button>
                                        <Button variant="outline" asChild size="lg">
                                            <a href={hero?.buttonSecondary?.link || '#berita'}>{hero?.buttonSecondary?.text || 'Kabar terbaru'}</a>
                                        </Button>
                                    </div>
                                </Reveal>
                                <Reveal className="modern-visual" delay={0.15}>
                                    <div className="modern-browser">
                                        <div className="modern-browser-bar"><i /><i /><i /></div>
                                        {hero?.image && <img src={hero.image} alt={copy.imageAlt} />}
                                    </div>
                                    <div className="floating-stat">
                                        <strong>{data.pages.length}+</strong>
                                        <span>Layanan informasi</span>
                                    </div>
                                    <div className="floating-status">
                                        <span className="status-dot" />
                                        Sistem informasi aktif
                                    </div>
                                </Reveal>
                            </div>
                        </section>
                        <ValueStrip />
                        <Marquee items={[
                            'Kampus digital',
                            'Akses inklusif',
                            'Informasi real-time',
                            'Pengalaman modern',
                            'Ekosistem terhubung',
                        ]} />
                    </React.Fragment>
                );

            case 'feature':
                return (
                    <PlatformOverview key={key}
                        site={data.site}
                        image={hero?.image}
                        pageCount={data.pages.length}
                        section={section}
                    />
                );

            case 'client':
                return <PartnerCloud key={key} partners={data.partners} section={section} />;

            case 'product':
                return (
                    <Section key={key} section={section}
                        id="informasi"
                        eyebrow={section.pre_title || 'Satu ekosistem'}
                        title={section.title || 'Semua yang dibutuhkan sivitas akademika'}
                        text={section.subtitle || section.post_title || 'Pengalaman digital yang sederhana di depan, dengan pengelolaan konten yang terstruktur di belakang.'}
                    >
                        <PagesGrid pages={data.pages} />
                    </Section>
                );

            case 'testimonial':
                return <TestimonialSection key={key} testimonials={data.testimonials} section={section} />;

            case 'pengumuman':
                return (
                    <Section key={key} section={section} id="berita" tint eyebrow={section.pre_title || 'Tetap terhubung'} title={section.title || 'Kabar terbaru kampus'} text={section.subtitle || section.post_title}>
                        <NewsGrid announcements={data.announcements} />
                    </Section>
                );

            case 'faq':
                return (
                    <Section key={key} section={section} eyebrow={section.pre_title || 'Butuh jawaban?'} title={section.title || 'Temukan informasi dengan lebih cepat'} text={section.subtitle || section.post_title} narrow>
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
