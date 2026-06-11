<?php

namespace Modules\Public\Database\Seeders;

use App\Models\Sys\Permission;
use App\Models\Sys\Role;
use Illuminate\Database\Seeder;

class RolePermissionPublicSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $tenantId = 1;

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($tenantId);
        }

        $permissionData = [
            // CMS
            ['name' => 'public.cms.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Dashboard', 'description' => 'Melihat menu CMS / Landing Page'],
            ['name' => 'public.cms.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Pengaturan Landing', 'description' => 'Mengubah template landing page'],

            // Slideshow
            ['name' => 'public.cms.slideshow.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Slideshow', 'description' => 'Melihat daftar slideshow'],
            ['name' => 'public.cms.slideshow.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Slideshow', 'description' => 'Menambah slideshow baru'],
            ['name' => 'public.cms.slideshow.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Slideshow', 'description' => 'Mengubah data slideshow'],
            ['name' => 'public.cms.slideshow.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Slideshow', 'description' => 'Menghapus slideshow'],

            // Testimoni
            ['name' => 'public.cms.testimonial.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Testimoni', 'description' => 'Melihat daftar testimoni'],
            ['name' => 'public.cms.testimonial.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Testimoni', 'description' => 'Menambah testimoni'],
            ['name' => 'public.cms.testimonial.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Testimoni', 'description' => 'Mengubah dan mengurutkan testimoni'],
            ['name' => 'public.cms.testimonial.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Testimoni', 'description' => 'Menghapus testimoni'],

            // Partner
            ['name' => 'public.cms.partner.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Partner', 'description' => 'Melihat daftar partner'],
            ['name' => 'public.cms.partner.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Partner', 'description' => 'Menambah partner'],
            ['name' => 'public.cms.partner.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Partner', 'description' => 'Mengubah dan mengurutkan partner'],
            ['name' => 'public.cms.partner.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Partner', 'description' => 'Menghapus partner'],

            // Pengumuman
            ['name' => 'public.cms.pengumuman.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Pengumuman', 'description' => 'Melihat daftar pengumuman'],
            ['name' => 'public.cms.pengumuman.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Pengumuman', 'description' => 'Menambah pengumuman baru'],
            ['name' => 'public.cms.pengumuman.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Pengumuman', 'description' => 'Mengubah data pengumuman'],
            ['name' => 'public.cms.pengumuman.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Pengumuman', 'description' => 'Menghapus pengumuman'],

            // FAQ
            ['name' => 'public.cms.faq.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'FAQ', 'description' => 'Melihat daftar FAQ'],
            ['name' => 'public.cms.faq.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'FAQ', 'description' => 'Menambah FAQ baru'],
            ['name' => 'public.cms.faq.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'FAQ', 'description' => 'Mengubah data FAQ'],
            ['name' => 'public.cms.faq.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'FAQ', 'description' => 'Menghapus FAQ'],

            // Halaman Statis
            ['name' => 'public.cms.page.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Halaman Statis', 'description' => 'Melihat daftar halaman statis'],
            ['name' => 'public.cms.page.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Halaman Statis', 'description' => 'Menambah halaman statis baru'],
            ['name' => 'public.cms.page.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Halaman Statis', 'description' => 'Mengubah data halaman statis'],
            ['name' => 'public.cms.page.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Halaman Statis', 'description' => 'Menghapus halaman statis'],

            // Menu Navigasi
            ['name' => 'public.cms.menu.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Menu Navigasi', 'description' => 'Melihat daftar menu navigasi'],
            ['name' => 'public.cms.menu.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Menu Navigasi', 'description' => 'Menambah menu navigasi baru'],
            ['name' => 'public.cms.menu.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Menu Navigasi', 'description' => 'Mengubah data menu navigasi'],
            ['name' => 'public.cms.menu.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Menu Navigasi', 'description' => 'Menghapus menu navigasi'],

            // Pengaturan Landing
            ['name' => 'public.cms.settings.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Pengaturan Landing', 'description' => 'Melihat pengaturan landing page'],
            ['name' => 'public.cms.settings.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Pengaturan Landing', 'description' => 'Mengubah pengaturan landing page'],

            // Hero Section
            ['name' => 'public.cms.hero.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Hero Section', 'description' => 'Melihat daftar hero section'],
            ['name' => 'public.cms.hero.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Hero Section', 'description' => 'Menambah hero section'],
            ['name' => 'public.cms.hero.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Hero Section', 'description' => 'Mengubah hero section'],
            ['name' => 'public.cms.hero.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Hero Section', 'description' => 'Menghapus hero section'],

            // Fitur
            ['name' => 'public.cms.feature.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Fitur', 'description' => 'Melihat daftar fitur'],
            ['name' => 'public.cms.feature.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Fitur', 'description' => 'Menambah fitur'],
            ['name' => 'public.cms.feature.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Fitur', 'description' => 'Mengubah dan mengurutkan fitur'],
            ['name' => 'public.cms.feature.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Fitur', 'description' => 'Menghapus fitur'],

            // Produk
            ['name' => 'public.cms.product.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Produk / Modul', 'description' => 'Melihat daftar produk/modul'],
            ['name' => 'public.cms.product.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Produk / Modul', 'description' => 'Menambah produk/modul'],
            ['name' => 'public.cms.product.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Produk / Modul', 'description' => 'Mengubah dan mengurutkan produk/modul'],
            ['name' => 'public.cms.product.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Produk / Modul', 'description' => 'Menghapus produk/modul'],

            // Statistik
            ['name' => 'public.cms.statistic.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Statistik', 'description' => 'Melihat daftar statistik'],
            ['name' => 'public.cms.statistic.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Statistik', 'description' => 'Menambah statistik'],
            ['name' => 'public.cms.statistic.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Statistik', 'description' => 'Mengubah dan mengurutkan statistik'],
            ['name' => 'public.cms.statistic.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Statistik', 'description' => 'Menghapus statistik'],

            // Klien
            ['name' => 'public.cms.client.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Klien', 'description' => 'Melihat daftar klien'],
            ['name' => 'public.cms.client.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Klien', 'description' => 'Menambah klien'],
            ['name' => 'public.cms.client.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Klien', 'description' => 'Mengubah dan mengurutkan klien'],
            ['name' => 'public.cms.client.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Klien', 'description' => 'Menghapus klien'],

            // CTA
            ['name' => 'public.cms.cta.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Call To Action', 'description' => 'Melihat daftar CTA'],
            ['name' => 'public.cms.cta.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Call To Action', 'description' => 'Menambah CTA'],
            ['name' => 'public.cms.cta.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Call To Action', 'description' => 'Mengubah CTA'],
            ['name' => 'public.cms.cta.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Call To Action', 'description' => 'Menghapus CTA'],
        ];

        foreach ($permissionData as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                [
                    'category' => $permission['category'],
                    'sub_category' => $permission['sub_category'],
                    'description' => $permission['description'],
                ]
            );
        }

        // Assign permissions to Administrator role
        $permissionNames = collect($permissionData)->pluck('name');
        foreach (['Root', 'Super Administrator', 'Administrator'] as $roleName) {
            Role::where('name', $roleName)
                ->where('tenant_id', $tenantId)
                ->first()
                ?->givePermissionTo($permissionNames);
        }
    }
}
