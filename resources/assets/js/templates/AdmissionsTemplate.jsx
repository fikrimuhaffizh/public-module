import { SectionVariantRenderer } from '@public/components/sections/renderer';
import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    CalendarDays,
    CheckCircle2,
    Clock,
    Download,
    FileText,
    GraduationCap,
    ShieldCheck,
    Sparkles,
    Upload,
    Wallet,
} from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';
import {
    FaqSection,
    NewsGrid,
    PagesGrid,
    Section,
    TestimonialSection,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

const steps = [
    { icon: FileText, title: 'Buat akun', text: 'Daftarkan diri pada portal penerimaan mahasiswa baru.' },
    { icon: Upload, title: 'Isi formulir', text: 'Lengkapi data diri dan unggah berkas persyaratan.' },
    { icon: CheckCircle2, title: 'Verifikasi', text: 'Tim penerimaan memverifikasi berkas Anda.' },
    { icon: GraduationCap, title: 'Diterima', text: 'Terima hasil seleksi dan lakukan daftar ulang.' },
];

const why = [
    { icon: Clock, title: 'Proses cepat', text: 'Pendaftaran online tanpa antre, hasil dalam 1×24 jam.' },
    { icon: ShieldCheck, title: 'Aman & transparan', text: 'Data terlindungi dan seleksi berjalan adil.' },
    { icon: Wallet, title: 'Biaya jelas', text: 'Informasi biaya dan beasiswa tersedia di muka.' },
    { icon: Sparkles, title: 'Pendampingan', text: 'Tim PMB membantu setiap tahap pendaftaran.' },
];

export default function AdmissionsTemplate({ data }) {
    const sections = data.sections || [];
    const { landing, site } = data;
    const hero = landing?.hero;
    const heroImage = hero?.image || data.slides?.[0]?.image;
    const stats = landing?.statistics || [];

        const renderSection = (section) => {
        if (!section.is_active) return null;
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
