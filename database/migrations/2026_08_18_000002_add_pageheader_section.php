<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah section "Page Header" (layout-level, breadcrumb + judul halaman
 * dalam) untuk tenant yang sudah punya baris cms_landing_sections.
 * Disisipkan tepat setelah navbar di area 'top'.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_landing_sections')) {
            return;
        }

        $tenants = DB::table('cms_landing_sections')->distinct()->pluck('tenant_id');
        foreach ($tenants as $tenantId) {
            $exists = DB::table('cms_landing_sections')
                ->where('tenant_id', $tenantId)
                ->where('section_key', 'pageheader')
                ->exists();
            if ($exists) {
                continue;
            }

            $navbar = DB::table('cms_landing_sections')
                ->where('tenant_id', $tenantId)
                ->where('section_key', 'navbar')
                ->first();
            $at = $navbar ? (int) $navbar->sort_order + 1 : 0;

            // Geser section 'top' setelah navbar agar urutan tetap rapi.
            DB::table('cms_landing_sections')
                ->where('tenant_id', $tenantId)
                ->where('area', 'top')
                ->where('sort_order', '>=', $at)
                ->increment('sort_order');

            DB::table('cms_landing_sections')->insert([
                'tenant_id' => $tenantId,
                'section_key' => 'pageheader',
                'section_name' => 'Page Header',
                'area' => 'top',
                'component_name' => 'PageHeaderSection',
                'variant' => 'pageheader_1',
                'sort_order' => $at,
                'limit_data' => 6,
                'is_active' => true,
                'settings' => json_encode(['show_title' => true, 'text_align' => 'left']),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('cms_landing_sections')
            ->where('section_key', 'pageheader')
            ->delete();
    }
};
