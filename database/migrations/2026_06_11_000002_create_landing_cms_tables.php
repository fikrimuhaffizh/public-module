<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
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
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
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
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
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
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
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
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
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
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
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
                $table->string('updated_by')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_landing_page_settings');
        Schema::dropIfExists('cms_ctas');
        Schema::dropIfExists('cms_clients');
        Schema::dropIfExists('cms_statistics');
        Schema::dropIfExists('cms_products');
        Schema::dropIfExists('cms_features');
        Schema::dropIfExists('cms_hero_sections');
    }
};
