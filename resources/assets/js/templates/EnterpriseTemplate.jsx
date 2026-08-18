import { SectionVariantRenderer } from '@public/components/sections/renderer';
import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    BarChart3,
    CheckCircle2,
    ExternalLink,
    Globe2,
    Layers3,
    Quote,
    ShieldCheck,
    Users2,
    Workflow,
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
    CtaSection,
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

const capabilities = [
    {
        icon: Layers3,
        title: 'Platform terpadu',
        text: 'Sistem informasi akademik, kepegawaian, dan kemahasiswaan dalam satu ekosistem digital.',
    },
    {
        icon: ShieldCheck,
        title: 'Keamanan & kepatuhan',
        text: 'Standar keamanan data institusi dengan akses berbasis peran dan audit trail.',
    },
    {
        icon: BarChart3,
        title: 'Analitik real-time',
        text: 'Dashboard dan laporan untuk memantau kinerja institusi secara langsung.',
    },
    {
        icon: Users2,
        title: 'Kolaborasi lintas unit',
        text: 'Alur kerja terhubung antara akademik, keuangan, dan kemahasiswaan.',
    },
    {
        icon: Globe2,
        title: 'Akses di mana saja',
        text: 'Responsif di semua perangkat, siap melayani sivitas akademika kapan pun.',
    },
    {
        icon: Workflow,
        title: 'Otomatisasi proses',
        text: 'Kurangi beban administrasi dengan alur persetujuan dan notifikasi otomatis.',
    },
];

export default function EnterpriseTemplate({ data }) {
    const sections = data.sections || [];
    const { landing } = data;
    const hero = landing?.hero;

        const renderSection = (section) => {
        if (!section.is_active) return null;
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
