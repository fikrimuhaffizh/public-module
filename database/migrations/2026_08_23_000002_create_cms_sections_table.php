<?php

use App\Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends BaseMigration
{
    public function up(): void
    {
        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id('section_id');
            $table->unsignedBigInteger('tenant_id')->default(1)->index();

            // Type discriminator: feature, product, client, partner, testimonial, cta, statistic
            $table->string('type', 50)->index();

            // Common content fields
            $table->string('title');                    // Judul/nama/label
            $table->string('slug')->nullable();         // Slug (product only, nullable)
            $table->text('description')->nullable();    // Deskripsi/quote
            $table->string('icon')->nullable();         // Icon class
            $table->unsignedInteger('sort_order')->default(0);

            // Flexible settings per type (JSON)
            // product: { short_description, demo_url }
            // client: { website }
            // partner: { category, website_url }
            // testimonial: { position, organization, rating }
            // cta: { button_text, button_link }
            // statistic: { value }
            $table->json('settings')->nullable();

            $table->boolean('is_active')->default(true);

            $this->addStandardColumns($table);

            // Unique slug per tenant (only for types that use slug)
            $table->unique(['tenant_id', 'type', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_sections');
    }
};
