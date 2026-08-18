import { SectionVariantRenderer } from '@public/components/sections/renderer';
import React from 'react';
import { ArrowRight } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { Reveal } from '@public/components/motion/effects';
import {
    CtaSection,
    FaqSection,
    NewsGrid,
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
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
