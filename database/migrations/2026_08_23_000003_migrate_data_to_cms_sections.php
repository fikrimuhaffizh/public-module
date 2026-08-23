<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Features → cms_sections (type=feature)
        $features = DB::table('cms_features')->whereNull('deleted_at')->get();
        foreach ($features as $f) {
            DB::table('cms_sections')->insert([
                'tenant_id'   => $f->tenant_id,
                'type'        => 'feature',
                'title'       => $f->title,
                'description' => $f->description,
                'icon'        => $f->icon,
                'sort_order'  => $f->sort_order,
                'is_active'   => $f->is_active,
                'created_at'  => $f->created_at,
                'updated_at'  => $f->updated_at,
            ]);
        }

        // 2. Products → cms_sections (type=product)
        $products = DB::table('cms_products')->whereNull('deleted_at')->get();
        foreach ($products as $p) {
            $settings = array_filter([
                'short_description' => $p->short_description,
                'demo_url'          => $p->demo_url,
            ], fn ($v) => $v !== null && $v !== '');

            DB::table('cms_sections')->insert([
                'tenant_id'   => $p->tenant_id,
                'type'        => 'product',
                'title'       => $p->name,
                'slug'        => $p->slug,
                'description' => $p->description,
                'icon'        => $p->icon,
                'sort_order'  => $p->sort_order,
                'settings'    => $settings ? json_encode($settings) : null,
                'is_active'   => $p->is_active,
                'created_at'  => $p->created_at,
                'updated_at'  => $p->updated_at,
            ]);
        }

        // 3. Clients → cms_sections (type=client)
        $clients = DB::table('cms_clients')->whereNull('deleted_at')->get();
        foreach ($clients as $c) {
            $settings = array_filter([
                'website' => $c->website,
            ], fn ($v) => $v !== null && $v !== '');

            DB::table('cms_sections')->insert([
                'tenant_id'   => $c->tenant_id,
                'type'        => 'client',
                'title'       => $c->name,
                'sort_order'  => $c->sort_order,
                'settings'    => $settings ? json_encode($settings) : null,
                'is_active'   => $c->is_active,
                'created_at'  => $c->created_at,
                'updated_at'  => $c->updated_at,
            ]);
        }

        // 4. Partners → cms_sections (type=partner)
        $partners = DB::table('cms_partner')->whereNull('deleted_at')->get();
        foreach ($partners as $p) {
            $settings = array_filter([
                'category'    => $p->category,
                'website_url' => $p->website_url,
            ], fn ($v) => $v !== null && $v !== '');

            DB::table('cms_sections')->insert([
                'tenant_id'   => $p->tenant_id,
                'type'        => 'partner',
                'title'       => $p->name,
                'sort_order'  => $p->seq,
                'settings'    => $settings ? json_encode($settings) : null,
                'is_active'   => $p->is_active,
                'created_at'  => $p->created_at,
                'updated_at'  => $p->updated_at,
            ]);
        }

        // 5. Testimonials → cms_sections (type=testimonial)
        $testimonials = DB::table('cms_testimonial')->whereNull('deleted_at')->get();
        foreach ($testimonials as $t) {
            $settings = array_filter([
                'position'     => $t->position,
                'organization' => $t->organization,
                'rating'       => $t->rating,
            ], fn ($v) => $v !== null && $v !== '');

            DB::table('cms_sections')->insert([
                'tenant_id'   => $t->tenant_id,
                'type'        => 'testimonial',
                'title'       => $t->name,
                'description' => $t->quote,
                'sort_order'  => $t->seq,
                'settings'    => $settings ? json_encode($settings) : null,
                'is_active'   => $t->is_active,
                'created_at'  => $t->created_at,
                'updated_at'  => $t->updated_at,
            ]);
        }

        // 6. CTAs → cms_sections (type=cta)
        $ctas = DB::table('cms_ctas')->whereNull('deleted_at')->get();
        foreach ($ctas as $c) {
            $settings = array_filter([
                'button_text' => $c->button_text,
                'button_link' => $c->button_link,
            ], fn ($v) => $v !== null && $v !== '');

            DB::table('cms_sections')->insert([
                'tenant_id'   => $c->tenant_id,
                'type'        => 'cta',
                'title'       => $c->title,
                'description' => $c->description,
                'sort_order'  => 0,
                'settings'    => $settings ? json_encode($settings) : null,
                'is_active'   => $c->is_active,
                'created_at'  => $c->created_at,
                'updated_at'  => $c->updated_at,
            ]);
        }

        // 7. Statistics → cms_sections (type=statistic)
        $stats = DB::table('cms_statistics')->whereNull('deleted_at')->get();
        foreach ($stats as $s) {
            $settings = array_filter([
                'value' => $s->value,
            ], fn ($v) => $v !== null && $v !== '');

            DB::table('cms_sections')->insert([
                'tenant_id'   => $s->tenant_id,
                'type'        => 'statistic',
                'title'       => $s->label,
                'icon'        => $s->icon,
                'sort_order'  => $s->sort_order,
                'settings'    => $settings ? json_encode($settings) : null,
                'is_active'   => $s->is_active,
                'created_at'  => $s->created_at,
                'updated_at'  => $s->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('cms_sections')->delete();
    }
};
