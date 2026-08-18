import { SectionVariantRenderer } from '@public/components/sections/renderer';
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
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
