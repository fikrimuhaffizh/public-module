import { SectionVariantRenderer } from '@public/components/sections/renderer';
import React from 'react';
import { ArrowRight, ExternalLink, Sparkles, Zap } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { BackgroundBeams, Marquee, Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';
import { FaqSection, NewsGrid, Section, SectionHeader, TestimonialSection, combinedText, heroCopy, sectionKey } from '@public/components/sections/LandingSections';

function IconOrImage({ icon, image, className = '' }) {
    if (image) return <img src={image} alt="" className={className} />;
    if (icon) return <i className={`${icon} ${className}`} aria-hidden="true" />;
    return <Sparkles className={className} size={22} />;
}

export default function LaunchTemplate({ data }) {
    const sections = data.sections || [];
    const { landing, site } = data;
    const hero = landing?.hero;
    const heroStyle = hero?.image
        ? { backgroundImage: `linear-gradient(135deg, rgba(5,8,20,.88), rgba(5,8,20,.55)), url("${hero.image}")` }
        : undefined;

        const renderSection = (section) => {
        if (!section.is_active) return null;
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
