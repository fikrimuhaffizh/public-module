<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old CMS tables (data already migrated to cms_sections)
        // Order matters: drop tables with foreign keys first
        Schema::dropIfExists('cms_features');
        Schema::dropIfExists('cms_products');
        Schema::dropIfExists('cms_clients');
        Schema::dropIfExists('cms_partner');
        Schema::dropIfExists('cms_testimonial');
        Schema::dropIfExists('cms_ctas');
        Schema::dropIfExists('cms_statistics');
    }

    public function down(): void
    {
        // Note: This cannot restore data. Data was migrated to cms_sections.
        // To restore, you would need to reverse-migrate from cms_sections.
    }
};
