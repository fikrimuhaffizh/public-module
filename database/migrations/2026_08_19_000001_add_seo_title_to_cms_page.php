<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_page', function (Blueprint $table) {
            if (! Schema::hasColumn('cms_page', 'seo_title')) {
                $table->string('seo_title', 255)->nullable()->after('meta_desc');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cms_page', function (Blueprint $table) {
            if (Schema::hasColumn('cms_page', 'seo_title')) {
                $table->dropColumn('seo_title');
            }
        });
    }
};
