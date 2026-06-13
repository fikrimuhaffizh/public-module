import React from 'react';
import { ArrowRight } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { Reveal } from '@public/components/motion/effects';
import {
    CtaSection,
    FaqSection,
    NewsGrid,
    PartnerCloud,
    PagesGrid,
    PlatformOverview,
    Section,
    SectionHeader,
    TestimonialSection,
    ValueStrip,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

export default function EditorialTemplate({ data }) {
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
                        <Reveal className="editorial-hero shell">
                            <div className="editorial-kicker">
                                <span>{section.pre_title || 'Wawasan'}</span>
                                <span>Inovasi</span>
                                <span>Kolaborasi</span>
                                <span>Transformasi digital</span>
                            </div>
                            <h1>{copy.title}</h1>
                            <div className="editorial-hero-grid">
                                <div className="editorial-image-frame">
                                    {hero?.image && <img src={hero.image} alt={copy.imageAlt} />}
                                    <span>{section.post_title || 'Platform informasi institusi'}</span>
                                </div>
                                <div className="editorial-summary">
                                    <p>{copy.subtitle}</p>
                                    <Button asChild size="lg">
                                        <a href={hero?.buttonPrimary?.link || '#berita'}>
                                            {hero?.buttonPrimary?.text || 'Temukan lebih jauh'} <ArrowRight size={18} />
                                        </a>
                                    </Button>
                                </div>
                            </div>
                        </Reveal>
                        <ValueStrip />
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

            case 'pengumuman':
                return (
                    <Section key={key} section={section} id="berita" eyebrow={section.pre_title || 'Sorotan utama'} title={section.title || 'Cerita dan perkembangan terbaru'} text={combinedText(section)}>
                        <NewsGrid announcements={data.announcements} section={section} editorial />
                    </Section>
                );

            case 'product':
                return (
                    <section key={key} className="editorial-pages shell">
                        <div>
                            <span className="eyebrow">{section.pre_title || 'Jelajahi institusi'}</span>
                            <h2>{section.title || 'Informasi yang membentuk pengalaman akademik'}</h2>
                            <p>{combinedText(section, 'Akses cepat menuju profil, layanan, program, dan sumber informasi penting.')}</p>
                        </div>
                        <PagesGrid pages={data.pages} section={section} />
                    </section>
                );

            case 'statistic':
                return landing?.statistics?.length > 0 ? (
                    <section key={key} className="editorial-stats shell">
                        <SectionHeader section={section} />
                        <div className="editorial-stats-grid">
                            {landing.statistics.slice(0, section.limit_data || 4).map((stat) => (
                                <div key={stat.id} className="editorial-stat">
                                    <strong>{stat.value}</strong>
                                    <span>{stat.label}</span>
                                </div>
                            ))}
                        </div>
                    </section>
                ) : null;

            case 'testimonial':
                return <TestimonialSection key={key} testimonials={data.testimonials} section={section} />;

            case 'faq':
                return (
                    <Section key={key} section={section} dark eyebrow={section.pre_title || 'Informasi praktis'} title={section.title || 'Pertanyaan umum'} text={combinedText(section)} narrow>
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
