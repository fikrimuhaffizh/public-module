<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tenant_id');
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->string('price', 50);
            $table->string('period', 50)->nullable()->comment('bulan, tahun, sekali, dll');
            $table->json('features')->nullable()->comment('Array of feature strings');
            $table->boolean('highlight')->default(false)->comment('Paket unggulan');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_pricing');
    }
};
