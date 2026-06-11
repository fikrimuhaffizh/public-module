<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_partner');
        Schema::dropIfExists('cms_testimonial');
    }
};
