<?php

namespace Modules\Public\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Public\Models\Client;
use Modules\Public\Models\Cta;
use Modules\Public\Models\Feature;
use Modules\Public\Models\HeroSection;
use Modules\Public\Models\LandingPageSetting;
use Modules\Public\Models\LandingSection;
use Modules\Public\Models\Product;
use Modules\Public\Models\Statistic;

class RefSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;
        $this->seedLandingContent($tenantId);
        $this->seedLandingSections($tenantId);
        $this->command->info('✅ RefSeeder (Public) completed.');
    }

    // ─── LandingContentSeeder ─────────────────────────────────

    private function seedLandingContent(int $tenantId): void
    {
        HeroSection::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Feature::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Product::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Statistic::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Client::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Cta::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();

        HeroSection::create([
            'tenant_id' => $tenantId,
            'title' => 'Transformasi Digital Kampus Terintegrasi',
            'subtitle' => 'Ekosistem Pemutu',
            'description' => 'Kelola akademik, administrasi, dan layanan kampus dalam satu platform yang modern, aman, dan mudah digunakan.',
            'button_primary_text' => 'Mulai Sekarang',
            'button_primary_link' => '/login',
            'button_secondary_text' => 'Hubungi Kami',
            'button_secondary_link' => '/public/contact-us',
            'is_active' => true,
        ]);

        collect([
            ['Integrasi Penuh', 'Semua modul terhubung dalam satu ekosistem data.', 'ti ti-plug-connected'],
            ['Aman & Terpercaya', 'Keamanan berlapis dengan kontrol akses per tenant.', 'ti ti-shield-check'],
            ['Mudah Dikelola', 'CMS landing page untuk konten tanpa coding.', 'ti ti-adjustments'],
            ['Skalabel', 'Siap tumbuh dari satu kampus hingga multi-institusi.', 'ti ti-chart-arrows-vertical'],
        ])->each(fn (array $item, int $index) => Feature::create([
            'tenant_id' => $tenantId, 'title' => $item[0], 'description' => $item[1],
            'icon' => $item[2], 'sort_order' => $index + 1, 'is_active' => true,
        ]));

        collect([
            ['E-Office', 'e-office', 'Manajemen surat dan dokumen elektronik.'],
            ['SPMI', 'spmi', 'Sistem penjaminan mutu internal terintegrasi.'],
            ['HR Core', 'hr-core', 'Pengelolaan SDM dan kepegawaian.'],
            ['Inventaris', 'inventaris', 'Pelacakan aset dan inventaris kampus.'],
            ['PMB', 'pmb', 'Penerimaan mahasiswa baru online.'],
        ])->each(fn (array $item, int $index) => Product::create([
            'tenant_id' => $tenantId, 'name' => $item[0], 'slug' => $item[1],
            'short_description' => $item[2], 'description' => $item[2],
            'demo_url' => 'https://example.com', 'sort_order' => $index + 1, 'is_active' => true,
        ]));

        collect([
            ['Kampus Mitra', '25', 'ti ti-building-community'],
            ['Pengguna Aktif', '15.000+', 'ti ti-users'],
            ['Surat Diproses', '120.000+', 'ti ti-mail-forward'],
            ['Modul Terintegrasi', '12+', 'ti ti-apps'],
        ])->each(fn (array $item, int $index) => Statistic::create([
            'tenant_id' => $tenantId, 'label' => $item[0], 'value' => $item[1],
            'icon' => $item[2], 'sort_order' => $index + 1, 'is_active' => true,
        ]));

        collect([
            ['Universitas Demo A', 'https://example.com'],
            ['Institut Teknologi B', 'https://example.com'],
            ['Politeknik C', 'https://example.com'],
            ['Sekolah Tinggi D', 'https://example.com'],
        ])->each(fn (array $item, int $index) => Client::create([
            'tenant_id' => $tenantId, 'name' => $item[0], 'website' => $item[1],
            'sort_order' => $index + 1, 'is_active' => true,
        ]));

        Cta::create([
            'tenant_id' => $tenantId,
            'title' => 'Siap modernisasi kampus Anda?',
            'description' => 'Jadwalkan demo gratis dan lihat bagaimana Pemutu dapat menyederhanakan operasional institusi.',
            'button_text' => 'Jadwalkan Demo',
            'button_link' => '/public/contact-us',
            'is_active' => true,
        ]);

        LandingPageSetting::updateOrCreate(['tenant_id' => $tenantId], [
            'site_title' => 'Pemutu — Platform Kampus Digital',
            'site_description' => 'Ekosistem digital terintegrasi untuk institusi pendidikan.',
            'meta_title' => 'Pemutu | Platform Kampus Digital',
            'meta_description' => 'Kelola akademik, administrasi, dan layanan kampus dalam satu platform.',
            'meta_keywords' => 'kampus digital, pemutu, e-office, spmi, pmb',
        ]);
    }

    // ─── LandingSectionSeeder ─────────────────────────────────

    private function seedLandingSections(int $tenantId): void
    {
        LandingSection::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();

        $sections = [
            ['section_key' => 'navbar', 'section_name' => 'Navbar', 'area' => 'top', 'component_name' => 'NavbarSection', 'variant' => 'navbar_1', 'sort_order' => 1, 'limit_data' => 6],
            ['section_key' => 'hero', 'section_name' => 'Hero', 'area' => 'top', 'component_name' => 'HeroSection', 'variant' => 'hero_2', 'pre_title' => 'Selamat Datang di', 'title' => 'Platform Kampus Digital Terintegrasi', 'post_title' => 'Ekosistem Pemutu', 'subtitle' => 'Solusi all-in-one untuk mengelola akademik, administrasi, dan layanan kampus.', 'sort_order' => 2, 'limit_data' => 6],
            ['section_key' => 'products', 'section_name' => 'Produk / Modul', 'area' => 'middle', 'component_name' => 'ProductSection', 'variant' => 'product_1', 'pre_title' => 'Kumpulan Layanan', 'title' => 'Modul Terintegrasi', 'subtitle' => 'Modul lengkap yang mencakup seluruh kebutuhan operasional kampus modern.', 'sort_order' => 1, 'limit_data' => 5],
            ['section_key' => 'stats', 'section_name' => 'Statistik', 'area' => 'middle', 'component_name' => 'StatsSection', 'variant' => 'stats_1', 'pre_title' => 'Angka yang Berbicara', 'title' => 'Pencapaian Kami', 'subtitle' => 'Data nyata dari ribuan pengguna.', 'sort_order' => 2, 'limit_data' => 4],
            ['section_key' => 'features', 'section_name' => 'Fitur', 'area' => 'middle', 'component_name' => 'FeatureSection', 'variant' => 'feature_1', 'pre_title' => 'Apa yang Kami Tawarkan', 'title' => 'Fitur Unggulan', 'subtitle' => 'Fitur lengkap untuk kebutuhan institusi pendidikan.', 'sort_order' => 3, 'limit_data' => 6],
            ['section_key' => 'testimonials', 'section_name' => 'Testimoni', 'area' => 'middle', 'component_name' => 'TestimonialSection', 'variant' => 'testimonial_1', 'pre_title' => 'Kata Mereka', 'title' => 'Ulasan Pengguna', 'subtitle' => 'Pengalaman nyata dari kampus mitra.', 'sort_order' => 4, 'limit_data' => 3],
            ['section_key' => 'clients', 'section_name' => 'Klien / Logo', 'area' => 'middle', 'component_name' => 'ClientSection', 'variant' => 'logos_1', 'pre_title' => 'Dipercaya Oleh', 'title' => 'Institusi Mitra', 'subtitle' => 'Ribuan institusi telah mempercayakan manajemennya kepada kami.', 'sort_order' => 5, 'limit_data' => 8],
            ['section_key' => 'faq', 'section_name' => 'FAQ', 'area' => 'middle', 'component_name' => 'FAQSection', 'variant' => 'faq_1', 'pre_title' => 'Pertanyaan Umum', 'title' => 'FAQ', 'subtitle' => 'Jawaban atas pertanyaan yang sering ditanyakan.', 'sort_order' => 6, 'limit_data' => 5],
            ['section_key' => 'announcement', 'section_name' => 'Pengumuman', 'area' => 'middle', 'component_name' => 'AnnouncementSection', 'variant' => 'announcement_1', 'pre_title' => 'Info Terbaru', 'title' => 'Pengumuman', 'subtitle' => 'Berita dan informasi penting terkait layanan.', 'sort_order' => 7, 'limit_data' => 3],
            ['section_key' => 'cta', 'section_name' => 'Call to Action', 'area' => 'bottom', 'component_name' => 'CtaSection', 'variant' => 'cta_1', 'pre_title' => 'Tunggu Apa Lagi?', 'title' => 'Siap Modernisasi Kampus Anda?', 'subtitle' => 'Jadwalkan demo gratis.', 'sort_order' => 1, 'limit_data' => 6],
            ['section_key' => 'footer', 'section_name' => 'Footer', 'area' => 'bottom', 'component_name' => 'FooterSection', 'variant' => 'footer_2', 'sort_order' => 2, 'limit_data' => 6],
        ];

        foreach ($sections as $section) {
            $data = ['tenant_id' => $tenantId, 'is_active' => true];
            foreach (['section_key', 'section_name', 'area', 'component_name', 'variant', 'pre_title', 'title', 'post_title', 'subtitle', 'description', 'sort_order', 'limit_data'] as $field) {
                if (isset($section[$field])) $data[$field] = $section[$field];
            }
            LandingSection::create($data);
        }
    }
}
