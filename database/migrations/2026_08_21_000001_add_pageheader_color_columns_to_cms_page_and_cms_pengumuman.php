<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom warna page header per-element:
 *   - pretitle_color: warna breadcrumb / eyebrow / badge
 *   - title_color: warna heading (h1)
 *   - subtitle_color: warna excerpt / deskripsi
 *
 * Berlaku untuk dua tabel:
 *   1. cms_page — halaman statis (mode template)
 *   2. cms_pengumuman — berita / pengumuman
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── cms_page ──────────────────────────────────────────────
        if (Schema::hasTable('cms_page') && ! Schema::hasColumn('cms_page', 'pretitle_color')) {
            Schema::table('cms_page', function (Blueprint $table) {
                $table->string('pretitle_color', 7)->nullable()->after('seo_title')
                    ->comment('Warna breadcrumb/eyebrow (hex, #RRGGBB)');
                $table->string('title_color', 7)->nullable()->after('pretitle_color')
                    ->comment('Warna heading h1 (hex, #RRGGBB)');
                $table->string('subtitle_color', 7)->nullable()->after('title_color')
                    ->comment('Warna excerpt/deskripsi (hex, #RRGGBB)');
            });
        }

        // ── cms_pengumuman ────────────────────────────────────────
        if (Schema::hasTable('cms_pengumuman') && ! Schema::hasColumn('cms_pengumuman', 'pretitle_color')) {
            Schema::table('cms_pengumuman', function (Blueprint $table) {
                $table->string('pretitle_color', 7)->nullable()->after('published_at')
                    ->comment('Warna breadcrumb/eyebrow (hex, #RRGGBB)');
                $table->string('title_color', 7)->nullable()->after('pretitle_color')
                    ->comment('Warna heading h1 (hex, #RRGGBB)');
                $table->string('subtitle_color', 7)->nullable()->after('title_color')
                    ->comment('Warna excerpt/deskripsi (hex, #RRGGBB)');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cms_page') && Schema::hasColumn('cms_page', 'pretitle_color')) {
            Schema::table('cms_page', function (Blueprint $table) {
                $table->dropColumn(['pretitle_color', 'title_color', 'subtitle_color']);
            });
        }

        if (Schema::hasTable('cms_pengumuman') && Schema::hasColumn('cms_pengumuman', 'pretitle_color')) {
            Schema::table('cms_pengumuman', function (Blueprint $table) {
                $table->dropColumn(['pretitle_color', 'title_color', 'subtitle_color']);
            });
        }
    }
};
