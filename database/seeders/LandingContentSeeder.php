<?php

namespace Modules\Public\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Public\app\Models\Client;
use Modules\Public\app\Models\Cta;
use Modules\Public\app\Models\Feature;
use Modules\Public\app\Models\HeroSection;
use Modules\Public\app\Models\LandingPageSetting;
use Modules\Public\app\Models\Product;
use Modules\Public\app\Models\Statistic;

class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;

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
        ])->each(function (array $item, int $index) use ($tenantId) {
            Feature::create([
                'tenant_id' => $tenantId,
                'title' => $item[0],
                'description' => $item[1],
                'icon' => $item[2],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        });

        collect([
            ['E-Office', 'e-office', 'Manajemen surat dan dokumen elektronik.'],
            ['SPMI', 'spmi', 'Sistem penjaminan mutu internal terintegrasi.'],
            ['HR Core', 'hr-core', 'Pengelolaan SDM dan kepegawaian.'],
            ['Inventaris', 'inventaris', 'Pelacakan aset dan inventaris kampus.'],
            ['PMB', 'pmb', 'Penerimaan mahasiswa baru online.'],
        ])->each(function (array $item, int $index) use ($tenantId) {
            Product::create([
                'tenant_id' => $tenantId,
                'name' => $item[0],
                'slug' => $item[1],
                'short_description' => $item[2],
                'description' => $item[2],
                'demo_url' => 'https://example.com',
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        });

        collect([
            ['Kampus Mitra', '25', 'ti ti-building-community'],
            ['Pengguna Aktif', '15.000+', 'ti ti-users'],
            ['Surat Diproses', '120.000+', 'ti ti-mail-forward'],
            ['Modul Terintegrasi', '12+', 'ti ti-apps'],
        ])->each(function (array $item, int $index) use ($tenantId) {
            Statistic::create([
                'tenant_id' => $tenantId,
                'label' => $item[0],
                'value' => $item[1],
                'icon' => $item[2],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        });

        collect([
            ['Universitas Demo A', 'https://example.com'],
            ['Institut Teknologi B', 'https://example.com'],
            ['Politeknik C', 'https://example.com'],
            ['Sekolah Tinggi D', 'https://example.com'],
        ])->each(function (array $item, int $index) use ($tenantId) {
            Client::create([
                'tenant_id' => $tenantId,
                'name' => $item[0],
                'website' => $item[1],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        });

        Cta::create([
            'tenant_id' => $tenantId,
            'title' => 'Siap modernisasi kampus Anda?',
            'description' => 'Jadwalkan demo gratis dan lihat bagaimana Pemutu dapat menyederhanakan operasional institusi.',
            'button_text' => 'Jadwalkan Demo',
            'button_link' => '/public/contact-us',
            'is_active' => true,
        ]);

        LandingPageSetting::updateOrCreate(
            ['tenant_id' => $tenantId],
            [
                'site_title' => 'Pemutu — Platform Kampus Digital',
                'site_description' => 'Ekosistem digital terintegrasi untuk institusi pendidikan.',
                'meta_title' => 'Pemutu | Platform Kampus Digital',
                'meta_description' => 'Kelola akademik, administrasi, dan layanan kampus dalam satu platform.',
                'meta_keywords' => 'kampus digital, pemutu, e-office, spmi, pmb',
            ]
        );
    }
}
