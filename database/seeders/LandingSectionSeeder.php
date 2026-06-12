<?php

namespace Modules\Public\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Public\app\Models\LandingSection;

class LandingSectionSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;

        // Clean up existing sections for the tenant
        LandingSection::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();

        // Create sections with nice content
        $sections = [
            // TOP AREA
            [
                'section_key' => 'navbar',
                'section_name' => 'Navbar',
                'area' => 'top',
                'component_name' => 'NavbarSection',
                'variant' => 'navbar_1',
                'title' => null,
                'pre_title' => null,
                'post_title' => null,
                'subtitle' => 'Menu Navigasi Utama',
                'description' => null,
                'sort_order' => 1,
                'limit_data' => 6,
                'is_active' => true,
                'settings' => null,
            ],
            [
                'section_key' => 'hero',
                'section_name' => 'Hero',
                'area' => 'top',
                'component_name' => 'HeroSection',
                'variant' => 'hero_2',
                'pre_title' => 'Selamat Datang di',
                'title' => 'Platform Kampus Digital Terintegrasi',
                'post_title' => 'Ekosistem Pemutu',
                'subtitle' => 'Solusi all-in-one untuk mengelola akademik, administrasi, dan layanan kampus dengan mudah dan efisien.',
                'description' => null,
                'sort_order' => 2,
                'limit_data' => 6,
                'is_active' => true,
                'settings' => null,
            ],

            // MIDDLE AREA
            [
                'section_key' => 'products',
                'section_name' => 'Produk / Modul',
                'area' => 'middle',
                'component_name' => 'ProductSection',
                'variant' => 'product_1',
                'pre_title' => 'Kumpulan Layanan',
                'title' => 'Modul Terintegrasi',
                'post_title' => 'Kami',
                'subtitle' => 'Modul lengkap yang mencakup seluruh kebutuhan operasional kampus modern.',
                'description' => null,
                'sort_order' => 1,
                'limit_data' => 5,
                'is_active' => true,
                'settings' => null,
            ],
            [
                'section_key' => 'stats',
                'section_name' => 'Statistik',
                'area' => 'middle',
                'component_name' => 'StatsSection',
                'variant' => 'stats_1',
                'pre_title' => 'Angka yang Berbicara',
                'title' => 'Pencapaian Kami',
                'post_title' => 'Sejauh Ini',
                'subtitle' => 'Data nyata dari ribuan pengguna yang telah merasakan manfaat platform kami.',
                'description' => null,
                'sort_order' => 2,
                'limit_data' => 4,
                'is_active' => true,
                'settings' => null,
            ],
            [
                'section_key' => 'features',
                'section_name' => 'Fitur',
                'area' => 'middle',
                'component_name' => 'FeatureSection',
                'variant' => 'feature_1',
                'pre_title' => 'Apa yang Kami Tawarkan',
                'title' => 'Fitur Unggulan',
                'post_title' => 'Kami',
                'subtitle' => 'Fitur lengkap yang dirancang khusus untuk kebutuhan institusi pendidikan.',
                'description' => null,
                'sort_order' => 3,
                'limit_data' => 6,
                'is_active' => true,
                'settings' => null,
            ],
            [
                'section_key' => 'testimonials',
                'section_name' => 'Testimoni',
                'area' => 'middle',
                'component_name' => 'TestimonialSection',
                'variant' => 'testimonial_1',
                'pre_title' => 'Kata Mereka',
                'title' => 'Ulasan Pengguna',
                'post_title' => 'Yang Puas',
                'subtitle' => 'Pengalaman nyata dari kampus mitra yang telah menggunakan platform kami.',
                'description' => null,
                'sort_order' => 4,
                'limit_data' => 3,
                'is_active' => true,
                'settings' => null,
            ],
            [
                'section_key' => 'clients',
                'section_name' => 'Klien / Logo',
                'area' => 'middle',
                'component_name' => 'ClientSection',
                'variant' => 'logos_1',
                'pre_title' => 'Dipercaya Oleh',
                'title' => 'Institusi Mitra',
                'post_title' => 'Kami',
                'subtitle' => 'Ribuan institusi pendidikan telah mempercayakan manajemen kampusnya kepada kami.',
                'description' => null,
                'sort_order' => 5,
                'limit_data' => 8,
                'is_active' => true,
                'settings' => null,
            ],
            [
                'section_key' => 'faq',
                'section_name' => 'FAQ',
                'area' => 'middle',
                'component_name' => 'FAQSection',
                'variant' => 'faq_1',
                'pre_title' => 'Pertanyaan Umum',
                'title' => 'FAQ',
                'post_title' => null,
                'subtitle' => 'Jawaban atas pertanyaan yang sering ditanyakan oleh pengguna kami.',
                'description' => null,
                'sort_order' => 6,
                'limit_data' => 5,
                'is_active' => true,
                'settings' => null,
            ],
            [
                'section_key' => 'announcement',
                'section_name' => 'Pengumuman',
                'area' => 'middle',
                'component_name' => 'AnnouncementSection',
                'variant' => 'announcement_1',
                'pre_title' => 'Info Terbaru',
                'title' => 'Pengumuman',
                'post_title' => null,
                'subtitle' => 'Berita dan informasi penting terkait layanan dan pengembangan platform.',
                'description' => null,
                'sort_order' => 7,
                'limit_data' => 3,
                'is_active' => true,
                'settings' => null,
            ],

            // BOTTOM AREA
            [
                'section_key' => 'cta',
                'section_name' => 'Call to Action',
                'area' => 'bottom',
                'component_name' => 'CtaSection',
                'variant' => 'cta_1',
                'pre_title' => 'Tunggu Apa Lagi?',
                'title' => 'Siap Modernisasi Kampus Anda?',
                'post_title' => null,
                'subtitle' => 'Jadwalkan demo gratis dan temukan solusi terbaik untuk institusi Anda.',
                'description' => null,
                'sort_order' => 1,
                'limit_data' => 6,
                'is_active' => true,
                'settings' => null,
            ],
            [
                'section_key' => 'footer',
                'section_name' => 'Footer',
                'area' => 'bottom',
                'component_name' => 'FooterSection',
                'variant' => 'footer_2',
                'title' => null,
                'pre_title' => null,
                'post_title' => null,
                'subtitle' => null,
                'description' => null,
                'sort_order' => 2,
                'limit_data' => 6,
                'is_active' => true,
                'settings' => null,
            ],
        ];

        foreach ($sections as $section) {
            LandingSection::create(array_merge($section, ['tenant_id' => $tenantId]));
        }
    }
}
