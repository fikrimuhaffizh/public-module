<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for CMS (Content Management System) module.
     * Consolidated from all subsequent 2026_06_11 migrations.
     */
    public function up(): void
    {
        // =====================================================================
        // 1. Pages
        // =====================================================================
        if (!Schema::hasTable('cms_page')) {
            Schema::create('cms_page', function (Blueprint $table) {
                $table->id('page_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('title');
                $table->string('slug')->unique();
                $table->longText('content')->nullable();
                $table->text('meta_desc')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
            });
        }

        // =====================================================================
        // 2. Menus
        // =====================================================================
        if (!Schema::hasTable('cms_menu')) {
            Schema::create('cms_menu', function (Blueprint $table) {
                $table->id('menu_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('title');
                $table->string('type')->default('link'); // link, page, route
                $table->string('url')->nullable();
                $table->string('route')->nullable();
                $table->unsignedBigInteger('page_id')->nullable();
                $table->string('position')->default('header'); // header, footer, etc.
                $table->string('target')->default('_self');
                $table->integer('sequence')->default(0);
                $table->boolean('is_active')->default(true);

                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();

                $table->foreign('parent_id')->references('menu_id')->on('cms_menu')->onDelete('cascade');
                $table->foreign('page_id')->references('page_id')->on('cms_page')->onDelete('set null');
            });
        }

        // =====================================================================
        // 3. Pengumuman
        // =====================================================================
        if (!Schema::hasTable('cms_pengumuman')) {
            Schema::create('cms_pengumuman', function (Blueprint $table) {
                $table->id('pengumuman_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('penulis_id');
                $table->string('judul', 191);
                $table->text('isi');
                $table->string('jenis', 50);
                $table->boolean('is_published')->default(false);
                $table->string('image_url')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Blameable
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();

                $table->foreign('penulis_id')->references('id')->on('users');
                $table->index(['jenis', 'is_published', 'published_at'], 'idx_pengumuman_main');
            });
        }

        // =====================================================================
        // 4. Slideshows
        // =====================================================================
        if (!Schema::hasTable('cms_slideshow')) {
            Schema::create('cms_slideshow', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('image_url');
                $table->string('title')->nullable();
                $table->string('caption')->nullable();
                $table->string('link')->nullable();
                $table->integer('seq')->default(0);
                $table->boolean('is_active')->default(true);

                // Blameable
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        // =====================================================================
        // 5. FAQ
        // =====================================================================
        if (!Schema::hasTable('cms_faq')) {
            Schema::create('cms_faq', function (Blueprint $table) {
                $table->id('faq_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->nullable();
                $table->integer('seq')->default(0);
                $table->boolean('is_active')->default(true);

                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
            });
        }

        // =====================================================================
        // 6. Testimonials
        // =====================================================================
        if (! Schema::hasTable('cms_testimonial')) {
            Schema::create('cms_testimonial', function (Blueprint $table) {
                $table->id('testimonial_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('name');
                $table->string('position')->nullable();
                $table->string('organization')->nullable();
                $table->text('quote');
                $table->unsignedTinyInteger('rating')->default(5);
                $table->unsignedInteger('seq')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
            });
        }

        // =====================================================================
        // 7. Partners
        // =====================================================================
        if (! Schema::hasTable('cms_partner')) {
            Schema::create('cms_partner', function (Blueprint $table) {
                $table->id('partner_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('name');
                $table->string('category')->nullable();
                $table->string('website_url')->nullable();
                $table->unsignedInteger('seq')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
            });
        }

        // =====================================================================
        // 8. Landing CMS Tables (with blameable_id columns)
        // =====================================================================
        if (! Schema::hasTable('cms_hero_sections')) {
            Schema::create('cms_hero_sections', function (Blueprint $table) {
                $table->id('hero_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->text('description')->nullable();
                $table->string('button_primary_text')->nullable();
                $table->string('button_primary_link')->nullable();
                $table->string('button_secondary_text')->nullable();
                $table->string('button_secondary_link')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->string('updated_by')->nullable();
                $table->unsignedBigInteger('updated_by_id')->nullable();
                $table->string('deleted_by')->nullable();
                $table->unsignedBigInteger('deleted_by_id')->nullable();
            });
        }

        if (! Schema::hasTable('cms_features')) {
            Schema::create('cms_features', function (Blueprint $table) {
                $table->id('feature_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->string('updated_by')->nullable();
                $table->unsignedBigInteger('updated_by_id')->nullable();
                $table->string('deleted_by')->nullable();
                $table->unsignedBigInteger('deleted_by_id')->nullable();
            });
        }

        if (! Schema::hasTable('cms_products')) {
            Schema::create('cms_products', function (Blueprint $table) {
                $table->id('product_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('name');
                $table->string('slug')->index();
                $table->string('short_description')->nullable();
                $table->text('description')->nullable();
                $table->string('demo_url')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->string('updated_by')->nullable();
                $table->unsignedBigInteger('updated_by_id')->nullable();
                $table->string('deleted_by')->nullable();
                $table->unsignedBigInteger('deleted_by_id')->nullable();
                $table->unique(['tenant_id', 'slug']);
            });
        }

        if (! Schema::hasTable('cms_statistics')) {
            Schema::create('cms_statistics', function (Blueprint $table) {
                $table->id('statistic_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('label');
                $table->string('value');
                $table->string('icon')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->string('updated_by')->nullable();
                $table->unsignedBigInteger('updated_by_id')->nullable();
                $table->string('deleted_by')->nullable();
                $table->unsignedBigInteger('deleted_by_id')->nullable();
            });
        }

        if (! Schema::hasTable('cms_clients')) {
            Schema::create('cms_clients', function (Blueprint $table) {
                $table->id('client_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('name');
                $table->string('website')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->string('updated_by')->nullable();
                $table->unsignedBigInteger('updated_by_id')->nullable();
                $table->string('deleted_by')->nullable();
                $table->unsignedBigInteger('deleted_by_id')->nullable();
            });
        }

        if (! Schema::hasTable('cms_ctas')) {
            Schema::create('cms_ctas', function (Blueprint $table) {
                $table->id('cta_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('button_text')->nullable();
                $table->string('button_link')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->string('updated_by')->nullable();
                $table->unsignedBigInteger('updated_by_id')->nullable();
                $table->string('deleted_by')->nullable();
                $table->unsignedBigInteger('deleted_by_id')->nullable();
            });
        }

        if (! Schema::hasTable('cms_landing_page_settings')) {
            Schema::create('cms_landing_page_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->default(1)->unique();
                $table->string('site_title')->nullable();
                $table->text('site_description')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('whatsapp')->nullable();
                $table->text('address')->nullable();
                $table->string('facebook_url')->nullable();
                $table->string('instagram_url')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->string('youtube_url')->nullable();
                $table->timestamps();
                $table->string('created_by')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->string('updated_by')->nullable();
                $table->unsignedBigInteger('updated_by_id')->nullable();
            });
        }

        // =====================================================================
        // 9. Landing Sections (with pre_title/post_title)
        // =====================================================================
        if (! Schema::hasTable('cms_landing_sections')) {
            Schema::create('cms_landing_sections', function (Blueprint $table) {
                $table->id('landing_section_id');
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->string('section_key', 50);
                $table->string('section_name');
                $table->string('area', 20);
                $table->string('component_name', 80);
                $table->string('variant', 50);
                $table->string('title')->nullable();
                $table->string('pre_title')->nullable();
                $table->string('post_title')->nullable();
                $table->string('subtitle')->nullable();
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->unsignedSmallInteger('limit_data')->default(6);
                $table->boolean('is_active')->default(true);
                $table->json('settings')->nullable();
                $table->timestamps();
                $table->string('created_by')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->string('updated_by')->nullable();
                $table->unsignedBigInteger('updated_by_id')->nullable();
                $table->unique(['tenant_id', 'section_key']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('cms_landing_sections');
        Schema::dropIfExists('cms_landing_page_settings');
        Schema::dropIfExists('cms_ctas');
        Schema::dropIfExists('cms_clients');
        Schema::dropIfExists('cms_statistics');
        Schema::dropIfExists('cms_products');
        Schema::dropIfExists('cms_features');
        Schema::dropIfExists('cms_hero_sections');
        Schema::dropIfExists('cms_partner');
        Schema::dropIfExists('cms_testimonial');
        Schema::dropIfExists('cms_faq');
        Schema::dropIfExists('cms_slideshow');
        Schema::dropIfExists('cms_pengumuman');
        Schema::dropIfExists('cms_menu');
        Schema::dropIfExists('cms_page');
        Schema::enableForeignKeyConstraints();
    }
};
