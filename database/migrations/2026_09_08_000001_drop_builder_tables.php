<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menghapus artefak fitur Website Builder (GrapesJS):
 * - tabel cms_page_builder_data & cms_page_templates
 * - kolom render_mode & template_key di cms_page
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('cms_page_builder_data');
        Schema::dropIfExists('cms_page_templates');

        Schema::table('cms_page', function (Blueprint $table) {
            foreach (['render_mode', 'template_key'] as $column) {
                if (Schema::hasColumn('cms_page', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('cms_page', function (Blueprint $table) {
            if (! Schema::hasColumn('cms_page', 'render_mode')) {
                $table->string('render_mode', 20)->default('template')->after('title');
            }
            if (! Schema::hasColumn('cms_page', 'template_key')) {
                $table->string('template_key', 60)->nullable()->after('render_mode');
            }
        });

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
};
