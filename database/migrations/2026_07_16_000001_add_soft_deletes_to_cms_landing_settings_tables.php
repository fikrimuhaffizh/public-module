<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cms_landing_page_settings')
            && ! Schema::hasColumn('cms_landing_page_settings', 'deleted_at')) {
            Schema::table('cms_landing_page_settings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('cms_landing_sections')
            && ! Schema::hasColumn('cms_landing_sections', 'deleted_at')) {
            Schema::table('cms_landing_sections', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cms_landing_page_settings')
            && Schema::hasColumn('cms_landing_page_settings', 'deleted_at')) {
            Schema::table('cms_landing_page_settings', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('cms_landing_sections')
            && Schema::hasColumn('cms_landing_sections', 'deleted_at')) {
            Schema::table('cms_landing_sections', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
