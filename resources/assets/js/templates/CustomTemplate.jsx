import React from 'react';
import {
  NavbarSection,
  HeroSection,
  StatsSection,
  FeatureSection,
  ProductSection,
  TestimonialSection,
  ClientSection,
  FaqSection,
  AnnouncementSection,
  CtaSection,
  FooterSection,
} from '@public/components/sections/CustomSections';

export default function CustomTemplate({ data }) {
  const sections = data.sections || [];

  const renderSection = (section) => {
    if (!section.is_active) return null;

    const sectionProps = {
      section,
      data,
    };

    switch (section.section_key) {
      case 'navbar':
        return <NavbarSection key={section.landing_section_id} {...sectionProps} />;
      case 'hero':
        return <HeroSection key={section.landing_section_id} {...sectionProps} />;
      case 'statistic':
        return <StatsSection key={section.landing_section_id} {...sectionProps} />;
      case 'feature':
        return <FeatureSection key={section.landing_section_id} {...sectionProps} />;
      case 'product':
        return <ProductSection key={section.landing_section_id} {...sectionProps} />;
      case 'testimonial':
        return <TestimonialSection key={section.landing_section_id} {...sectionProps} />;
      case 'client':
        return <ClientSection key={section.landing_section_id} {...sectionProps} />;
      case 'faq':
        return <FaqSection key={section.landing_section_id} {...sectionProps} />;
      case 'pengumuman':
        return <AnnouncementSection key={section.landing_section_id} {...sectionProps} />;
      case 'cta':
        return <CtaSection key={section.landing_section_id} {...sectionProps} />;
      case 'footer':
        return <FooterSection key={section.landing_section_id} {...sectionProps} />;
      default:
        return null;
    }
  };

  return (
    <>
      {sections.map(renderSection)}
    </>
  );
}
