<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_landing_sections', function (Blueprint $table) {
            $table->string('pre_title')->nullable()->after('title');
            $table->string('post_title')->nullable()->after('pre_title');
        });
    }

    public function down(): void
    {
        Schema::table('cms_landing_sections', function (Blueprint $table) {
            $table->dropColumn(['pre_title', 'post_title']);
        });
    }
};
