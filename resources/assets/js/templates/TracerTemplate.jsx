import { SectionVariantRenderer } from '@public/components/sections/renderer';
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
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
