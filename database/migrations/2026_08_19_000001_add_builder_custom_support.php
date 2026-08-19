<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. Generalisasi cms_page -> render_mode ('template' | 'custom')
        //    'template' = React/Inertia existing (tidak berubah), 'custom' = GrapesJS freeform.
        // =====================================================================
        Schema::table('cms_page', function (Blueprint $table) {
            if (! Schema::hasColumn('cms_page', 'render_mode')) {
                $table->string('render_mode', 20)->default('template')->after('title');
            }
            if (! Schema::hasColumn('cms_page', 'template_key')) {
                $table->string('template_key', 60)->nullable()->after('render_mode');
            }
            if (! Schema::hasColumn('cms_page', 'seo_title')) {
                $table->string('seo_title', 255)->nullable()->after('meta_desc');
            }
        });

        // =====================================================================
        // 2. cms_page_builder_data — 1:1, khusus render_mode='custom'
        //    gjs_project  = output editor.getProjectData() (sumber kebenaran, utk reload editor)
        //    html/css     = hasil compile editor (sanitasi server-side), utk publish cepat.
        // =====================================================================
        if (! Schema::hasTable('cms_page_builder_data')) {
            Schema::create('cms_page_builder_data', function (Blueprint $table) {
                $table->unsignedBigInteger('page_id')->primary();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->json('gjs_project')->nullable();
                $table->longText('html_compiled')->nullable();
                $table->longText('css_compiled')->nullable();
                $table->timestamp('compiled_at')->nullable();
                $table->timestamps();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();

                $table->foreign('page_id')->references('page_id')->on('cms_page')->onDelete('cascade');
            });
        }

        // =====================================================================
        // 3. cms_page_templates — katalog starter (Elementor-style "pick a template")
        // =====================================================================
        if (! Schema::hasTable('cms_page_templates')) {
            Schema::create('cms_page_templates', function (Blueprint $table) {
                $table->id('template_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('key', 60);
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('thumbnail_url')->nullable();
                $table->string('category', 40)->default('marketing');
                $table->json('gjs_project')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();

                $table->unique(['tenant_id', 'key']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_page_templates');
        Schema::dropIfExists('cms_page_builder_data');

        Schema::table('cms_page', function (Blueprint $table) {
            if (Schema::hasColumn('cms_page', 'render_mode')) {
                $table->dropColumn('render_mode');
            }
            if (Schema::hasColumn('cms_page', 'template_key')) {
                $table->dropColumn('template_key');
            }
            if (Schema::hasColumn('cms_page', 'seo_title')) {
                $table->dropColumn('seo_title');
            }
        });
    }
};