<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cms_landing_page_settings')
            && ! Schema::hasColumn('cms_landing_page_settings', 'design')) {
            Schema::table('cms_landing_page_settings', function (Blueprint $table) {
                $table->json('design')->nullable()->after('youtube_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cms_landing_page_settings')
            && Schema::hasColumn('cms_landing_page_settings', 'design')) {
            Schema::table('cms_landing_page_settings', function (Blueprint $table) {
                $table->dropColumn('design');
            });
        }
    }
};
