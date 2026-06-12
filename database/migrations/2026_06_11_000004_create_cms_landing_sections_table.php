<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('cms_landing_sections');
    }
};
