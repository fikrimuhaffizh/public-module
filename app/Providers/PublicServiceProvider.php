<?php

namespace Modules\Public\Providers;

use App\Modules\BaseModuleServiceProvider;

class PublicServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'Public';

    protected string $nameLower = 'public';

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), 'public');
    }

    public function boot(): void
    {
        parent::boot();
    }

    protected function menu(): array
    {
        return [
            [
                'priority'      => 900,
                'type'          => 'dropdown',
                'title'         => 'CMS / Landing Page',
                'id'            => 'navbar-cms',
                'icon'          => 'world-www',
                'active_routes' => ['public.cms.*'],
                'permission'    => null,
                'children'      => [
                    [
                        'priority'      => 1,
                        'title'         => 'Pengaturan Landing',
                        'route'         => 'public.cms.settings.edit',
                        'active_routes' => ['public.cms.settings.*'],
                        'icon'          => 'settings',
                        'permission'    => 'public.cms.settings.view',
                    ],
                    [
                        'priority'      => 5,
                        'title'         => 'Template Landing',
                        'route'         => 'public.cms.landing.edit',
                        'active_routes' => ['public.cms.landing.*'],
                        'icon'          => 'layout',
                        'permission'    => 'public.cms.update',
                    ],
                    [
                        'priority'      => 8,
                        'title'         => 'Hero Section',
                        'route'         => 'public.cms.hero.index',
                        'active_routes' => ['public.cms.hero.*'],
                        'icon'          => 'layout-navbar',
                        'permission'    => 'public.cms.hero.view',
                    ],
                    [
                        'priority'      => 9,
                        'title'         => 'Fitur',
                        'route'         => 'public.cms.feature.index',
                        'active_routes' => ['public.cms.feature.*'],
                        'icon'          => 'sparkles',
                        'permission'    => 'public.cms.feature.view',
                    ],
                    [
                        'priority'      => 11,
                        'title'         => 'Produk / Modul',
                        'route'         => 'public.cms.product.index',
                        'active_routes' => ['public.cms.product.*'],
                        'icon'          => 'apps',
                        'permission'    => 'public.cms.product.view',
                    ],
                    [
                        'priority'      => 12,
                        'title'         => 'Statistik',
                        'route'         => 'public.cms.statistic.index',
                        'active_routes' => ['public.cms.statistic.*'],
                        'icon'          => 'chart-bar',
                        'permission'    => 'public.cms.statistic.view',
                    ],
                    [
                        'priority'      => 13,
                        'title'         => 'Klien',
                        'route'         => 'public.cms.client.index',
                        'active_routes' => ['public.cms.client.*'],
                        'icon'          => 'users-group',
                        'permission'    => 'public.cms.client.view',
                    ],
                    [
                        'priority'      => 14,
                        'title'         => 'Call To Action',
                        'route'         => 'public.cms.cta.index',
                        'active_routes' => ['public.cms.cta.*'],
                        'icon'          => 'click',
                        'permission'    => 'public.cms.cta.view',
                    ],
                    [
                        'priority'      => 10,
                        'title'         => 'Slideshow',
                        'route'         => 'public.cms.slideshow.index',
                        'active_routes' => ['public.cms.slideshow.*'],
                        'icon'          => 'photo',
                        'permission'    => 'public.cms.slideshow.view',
                    ],
                    [
                        'priority'      => 20,
                        'title'         => 'Testimoni',
                        'route'         => 'public.cms.testimonial.index',
                        'active_routes' => ['public.cms.testimonial.*'],
                        'icon'          => 'message-star',
                        'permission'    => 'public.cms.testimonial.view',
                    ],
                    [
                        'priority'      => 30,
                        'title'         => 'Partner',
                        'route'         => 'public.cms.partner.index',
                        'active_routes' => ['public.cms.partner.*'],
                        'icon'          => 'building-community',
                        'permission'    => 'public.cms.partner.view',
                    ],
                    [
                        'priority'      => 40,
                        'title'         => 'Pengumuman',
                        'route'         => 'public.cms.pengumuman.index',
                        'active_routes' => ['public.cms.pengumuman.*'],
                        'icon'          => 'news',
                        'permission'    => 'public.cms.pengumuman.view',
                    ],
                    [
                        'priority'      => 50,
                        'title'         => 'FAQ',
                        'route'         => 'public.cms.faq.index',
                        'active_routes' => ['public.cms.faq.*'],
                        'icon'          => 'help',
                        'permission'    => 'public.cms.faq.view',
                    ],
                    [
                        'priority'      => 60,
                        'title'         => 'Halaman Statis',
                        'route'         => 'public.cms.page.index',
                        'active_routes' => ['public.cms.page.*'],
                        'icon'          => 'file-text',
                        'permission'    => 'public.cms.page.view',
                    ],
                    [
                        'priority'      => 70,
                        'title'         => 'Menu Navigasi',
                        'route'         => 'public.cms.menu.index',
                        'active_routes' => ['public.cms.menu.*'],
                        'icon'          => 'list',
                        'permission'    => 'public.cms.menu.view',
                    ],
                ],
            ],
        ];
    }
}
