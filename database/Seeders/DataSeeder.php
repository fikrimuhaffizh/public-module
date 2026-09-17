<?php

namespace Modules\Public\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Public\Models\LandingSection;

/**
 * DataSeeder - data default tenant baru untuk Public (landing page).
 *
 * Isi: 14 section landing page default (topbar, navbar, hero, produk, dst).
 * PERHATIAN: forceDelete dulu lalu recreate - bukan idempotent murni,
 * perubahan manual di DB akan tertimpa saat seeder dijalankan ulang.
 */
class DataSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = (int) getPermissionsTeamId() ?: 1;
        $this->seedLandingSections($tenantId);
        $this->command->info('✅ DataSeeder (Public) completed.');
    }

    // LANDING PAGE SECTIONS
    // Struktur & konten awal landing page. area: top => middle => bottom,
    // urut berdasarkan sort_order per area. Ubah teks default di sini.
    private function seedLandingSections(int $tenantId): void
    {
        LandingSection::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();

        $sections = [
            ['section_key' => 'topbar', 'section_name' => 'Top Bar', 'area' => 'top', 'component_name' => 'TopBarSection', 'variant' => 'topbar_1', 'sort_order' => 0, 'limit_data' => 6, 'settings' => json_encode(['topbar_hours' => 'Senin–Jumat 08.00–17.00']), 'is_active' => 0],
            ['section_key' => 'navbar', 'section_name' => 'Navbar', 'area' => 'top', 'component_name' => 'NavbarSection', 'variant' => 'navbar_1', 'sort_order' => 1, 'limit_data' => 6],
            ['section_key' => 'pageheader', 'section_name' => 'Page Header', 'area' => 'top', 'component_name' => 'PageHeaderSection', 'variant' => 'pageheader_1', 'sort_order' => 2, 'limit_data' => 6, 'is_active' => true],
            ['section_key' => 'hero', 'section_name' => 'Hero', 'area' => 'top', 'component_name' => 'HeroSection', 'variant' => 'hero_2', 'pre_title' => 'Selamat Datang di', 'title' => 'Platform Kampus Digital Terintegrasi', 'post_title' => 'Ekosistem Pemutu', 'subtitle' => 'Solusi all-in-one untuk mengelola akademik, administrasi, dan layanan kampus.', 'sort_order' => 3, 'limit_data' => 6],
            ['section_key' => 'product', 'section_name' => 'Produk / Modul', 'area' => 'middle', 'component_name' => 'ProductSection', 'variant' => 'product_1', 'pre_title' => 'Kumpulan Layanan', 'title' => 'Modul Terintegrasi', 'subtitle' => 'Modul lengkap yang mencakup seluruh kebutuhan operasional kampus modern.', 'sort_order' => 1, 'limit_data' => 5],
            ['section_key' => 'statistic', 'section_name' => 'Statistik', 'area' => 'middle', 'component_name' => 'StatsSection', 'variant' => 'statistic_1', 'pre_title' => 'Angka yang Berbicara', 'title' => 'Pencapaian Kami', 'subtitle' => 'Data nyata dari ribuan pengguna.', 'sort_order' => 2, 'limit_data' => 4],
            ['section_key' => 'feature', 'section_name' => 'Fitur', 'area' => 'middle', 'component_name' => 'FeatureSection', 'variant' => 'feature_1', 'pre_title' => 'Apa yang Kami Tawarkan', 'title' => 'Fitur Unggulan', 'subtitle' => 'Fitur lengkap untuk kebutuhan institusi pendidikan.', 'sort_order' => 3, 'limit_data' => 6],
            ['section_key' => 'testimonial', 'section_name' => 'Testimoni', 'area' => 'middle', 'component_name' => 'TestimonialSection', 'variant' => 'testimonial_1', 'pre_title' => 'Kata Mereka', 'title' => 'Ulasan Pengguna', 'subtitle' => 'Pengalaman nyata dari kampus mitra.', 'sort_order' => 4, 'limit_data' => 3],
            ['section_key' => 'client', 'section_name' => 'Klien / Logo', 'area' => 'middle', 'component_name' => 'ClientSection', 'variant' => 'client_1', 'pre_title' => 'Dipercaya Oleh', 'title' => 'Institusi Mitra', 'subtitle' => 'Ribuan institusi telah mempercayakan manajemennya kepada kami.', 'sort_order' => 5, 'limit_data' => 8],
            ['section_key' => 'faq', 'section_name' => 'FAQ', 'area' => 'middle', 'component_name' => 'FAQSection', 'variant' => 'faq_1', 'pre_title' => 'Pertanyaan Umum', 'title' => 'FAQ', 'subtitle' => 'Jawaban atas pertanyaan yang sering ditanyakan.', 'sort_order' => 6, 'limit_data' => 5],
            ['section_key' => 'pengumuman', 'section_name' => 'Pengumuman', 'area' => 'middle', 'component_name' => 'AnnouncementSection', 'variant' => 'pengumuman_1', 'pre_title' => 'Info Terbaru', 'title' => 'Pengumuman', 'subtitle' => 'Berita dan informasi penting terkait layanan.', 'sort_order' => 7, 'limit_data' => 3],
            ['section_key' => 'price', 'section_name' => 'Harga / Paket', 'area' => 'middle', 'component_name' => 'PriceSection', 'variant' => 'price_1', 'pre_title' => 'Paket & Harga', 'title' => 'Pilih paket yang sesuai', 'subtitle' => 'Harga transparan, tanpa biaya tersembunyi.', 'sort_order' => 8, 'limit_data' => 3],
            ['section_key' => 'cta', 'section_name' => 'Call to Action', 'area' => 'bottom', 'component_name' => 'CtaSection', 'variant' => 'cta_1', 'pre_title' => 'Tunggu Apa Lagi?', 'title' => 'Siap Modernisasi Kampus Anda?', 'subtitle' => 'Jadwalkan demo gratis.', 'sort_order' => 1, 'limit_data' => 6],
            ['section_key' => 'footer', 'section_name' => 'Footer', 'area' => 'bottom', 'component_name' => 'FooterSection', 'variant' => 'footer_2', 'sort_order' => 2, 'limit_data' => 6],
        ];

        foreach ($sections as $section) {
            $data = ['tenant_id' => $tenantId, 'is_active' => true];
            foreach (['section_key', 'section_name', 'area', 'component_name', 'variant', 'pre_title', 'title', 'post_title', 'subtitle', 'description', 'sort_order', 'limit_data'] as $field) {
                if (isset($section[$field])) {
                    $data[$field] = $section[$field];
                }
            }
            LandingSection::create($data);
        }
    }
}
