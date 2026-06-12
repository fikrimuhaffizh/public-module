import React from 'react';
import { ArrowRight, CheckCircle2, Sparkles } from 'lucide-react';
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

const trustItems = [
    ['Program Terpadu', 'Pembelajaran relevan dengan kebutuhan masa depan.'],
    ['Ekosistem Digital', 'Layanan kampus terhubung dalam satu platform.'],
    ['Mutu Terjamin', 'Peningkatan berkelanjutan berbasis data.'],
];

export default function InstitutionalTemplate({ data }) {
    const sections = data.sections || [];
    const hero = data.landing?.hero;
    const heroStyle = hero?.image
        ? { backgroundImage: `linear-gradient(90deg, rgba(7,34,65,.96), rgba(7,34,65,.42)), url("${hero.image}")` }
        : undefined;

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;
        const copy = heroCopy(section, hero, data.site);

        switch (sectionKey(section)) {
            case 'hero':
                return (
                    <React.Fragment key={key}>
                        <section className="hero hero--institutional" style={heroStyle}>
                            <BackgroundBeams />
                            <Reveal className={`shell hero-content ${copy.alignClass}`}>
                                <Badge><Sparkles size={14} /> {section.pre_title || 'Institusi modern, layanan terintegrasi'}</Badge>
                                <h1>{copy.title}</h1>
                                {copy.subtitle && <p className="hero-subtitle">{copy.subtitle}</p>}
                                <p>{copy.description}</p>
                                <div className="hero-actions">
                                    <Button asChild size="lg">
                                        <a href={hero?.buttonPrimary?.link || '#informasi'}>
                                            {hero?.buttonPrimary?.text || 'Jelajahi platform'} <ArrowRight size={18} />
                                        </a>
                                    </Button>
                                    <Button variant="outline" asChild size="lg">
                                        <a href={hero?.buttonSecondary?.link || '#berita'}>{hero?.buttonSecondary?.text || 'Lihat kabar terbaru'}</a>
                                    </Button>
                                </div>
                            </Reveal>
                        </section>
                        <ValueStrip />
                        <Marquee items={[
                            'Pembelajaran relevan',
                            'Layanan kampus terintegrasi',
                            'Mutu berbasis data',
                            'Kolaborasi industri',
                            'Inovasi berkelanjutan',
                        ]} />
                    </React.Fragment>
                );

            case 'feature':
                return (
                    <React.Fragment key={key}>
                        <section className="trust-strip">
                            <div className="shell trust-grid">
                                {trustItems.map(([title, text]) => (
                                    <div key={title}>
                                        <CheckCircle2 />
                                        <div><strong>{title}</strong><span>{text}</span></div>
                                    </div>
                                ))}
                            </div>
                        </section>
                        <PlatformOverview
                            site={data.site}
                            image={hero?.image}
                            pageCount={data.pages.length}
                            section={section}
                        />
                    </React.Fragment>
                );

            case 'client':
                return <PartnerCloud key={key} partners={data.partners} section={section} />;

            case 'product':
                return (
                    <Section key={key} section={section}
                        id="informasi"
                        eyebrow={section.pre_title || 'Kapabilitas institusi'}
                        title={section.title || 'Informasi utama dalam satu ekosistem'}
                        text={section.subtitle || section.post_title || 'Akses profil, layanan, program, dan informasi penting melalui pengalaman digital yang konsisten.'}
                    >
                        <PagesGrid pages={data.pages} />
                    </Section>
                );

            case 'testimonial':
                return <TestimonialSection key={key} testimonials={data.testimonials} section={section} />;

            case 'pengumuman':
                return (
                    <Section key={key} section={section} id="berita" dark eyebrow={section.pre_title || 'Informasi terkini'} title={section.title || 'Berita dan pengumuman'}>
                        <NewsGrid announcements={data.announcements} />
                    </Section>
                );

            case 'faq':
                return (
                    <Section key={key} section={section} eyebrow={section.pre_title || 'Pusat bantuan'} title={section.title || 'Pertanyaan yang sering diajukan'} narrow>
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
