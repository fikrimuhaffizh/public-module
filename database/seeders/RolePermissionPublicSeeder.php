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

            // Slideshow
            ['name' => 'public.cms.slideshow.view', 'category' => 'CMS / Landing Page', 'sub_category' => 'Slideshow', 'description' => 'Melihat daftar slideshow'],
            ['name' => 'public.cms.slideshow.create', 'category' => 'CMS / Landing Page', 'sub_category' => 'Slideshow', 'description' => 'Menambah slideshow baru'],
            ['name' => 'public.cms.slideshow.update', 'category' => 'CMS / Landing Page', 'sub_category' => 'Slideshow', 'description' => 'Mengubah data slideshow'],
            ['name' => 'public.cms.slideshow.delete', 'category' => 'CMS / Landing Page', 'sub_category' => 'Slideshow', 'description' => 'Menghapus slideshow'],

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
