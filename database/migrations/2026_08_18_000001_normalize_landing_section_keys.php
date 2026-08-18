<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Normalisasi key section & variant ke bentuk canonical.
 *
 * Sebelumnya baris cms_landing_sections memakai alias/prefix lama:
 *   section_key: products, stats, features, testimonials, clients, announcement
 *   variant:     stats_N, logos_N, announcement_N
 *
 * Sementara config (landing_sections.php, themes.php) dan registry frontend
 * memakai key canonical (product, statistic, feature, testimonial, client,
 * pengumuman) dengan variant statistic_N / client_N / pengumuman_N.
 * Mismatch ini membuat variant legacy diam-diam jatuh ke Mode 1 dan lookup
 * meta section di admin (registry[section_key]) gagal.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_landing_sections')) {
            return;
        }

        // 1) section_key alias → canonical.
        DB::table('cms_landing_sections')
            ->whereIn('section_key', ['products', 'stats', 'features', 'testimonials', 'clients', 'announcement'])
            ->update([
                'section_key' => DB::raw("CASE section_key
                    WHEN 'products' THEN 'product'
                    WHEN 'stats' THEN 'statistic'
                    WHEN 'features' THEN 'feature'
                    WHEN 'testimonials' THEN 'testimonial'
                    WHEN 'clients' THEN 'client'
                    WHEN 'announcement' THEN 'pengumuman'
                END"),
            ]);

        // 2) variant prefix legacy → canonical.
        DB::table('cms_landing_sections')
            ->where('variant', 'like', 'stats\_%')
            ->update(['variant' => DB::raw("REPLACE(variant, 'stats_', 'statistic_')")]);

        DB::table('cms_landing_sections')
            ->where('variant', 'like', 'logos\_%')
            ->update(['variant' => DB::raw("REPLACE(variant, 'logos_', 'client_')")]);

        DB::table('cms_landing_sections')
            ->where('variant', 'like', 'announcement\_%')
            ->update(['variant' => DB::raw("REPLACE(variant, 'announcement_', 'pengumuman_')")]);
    }

    public function down(): void
    {
        // Migrasi data tidak bisa dibalik dengan aman (nilai lama sudah
        // digabung). Biarkan — alias di frontend/backend tetap menerima
        // kedua bentuk, jadi tidak ada yang rusak bila dibiarkan.
    }
};
