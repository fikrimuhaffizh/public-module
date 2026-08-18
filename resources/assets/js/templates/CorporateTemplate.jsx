import { SectionVariantRenderer } from '@public/components/sections/renderer';
import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Building2,
    ChartNoAxesCombined,
    CheckCircle2,
    Globe2,
    Layers3,
    Quote,
    ShieldCheck,
} from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@public/components/ui/card';
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';
import {
    FaqSection,
    NewsGrid,
    PagesGrid,
    Section,
    SectionHeader,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

const pillars = [
    {
        icon: Layers3,
        title: 'Ekosistem terintegrasi',
        text: 'Informasi, layanan, dan komunikasi institusi hadir dalam satu pengalaman digital.',
    },
    {
        icon: ShieldCheck,
        title: 'Tata kelola terpercaya',
        text: 'Konten terstruktur membantu institusi menjaga akurasi dan konsistensi informasi.',
    },
    {
        icon: ChartNoAxesCombined,
        title: 'Pertumbuhan berkelanjutan',
        text: 'Fondasi digital yang siap berkembang mengikuti kebutuhan dan strategi institusi.',
    },
];

export default function CorporateTemplate({ data }) {
    const sections = data.sections || [];
    const { landing } = data;
    const hero = landing?.hero;

        const renderSection = (section) => {
        if (!section.is_active) return null;
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
