<?php

namespace Modules\Public\Providers;

use App\Modules\BaseModuleServiceProvider;

class PublicServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'Public';

    protected string $nameLower = 'public';

    protected array $commands = [];

    public function boot(): void
    {
        parent::boot();
        $this->mergeConfigFrom(module_path($this->name, 'config/landing_sections.php'), 'landing_sections');
    }

    protected function menu(): array
    {
        return [
            [
                'priority'      => 900,
                'type'          => 'dropdown',
                'title'         => 'CMS',
                'id'            => 'navbar-cms',
                'icon'          => 'world-www',
                'active_routes' => ['public.cms.*'],
                'permission'    => null,
                'children'      => [
                    [
                        'priority'      => 5,
                        'type'          => 'dropdown',
                        'title'         => 'Landing Page',
                        'id'            => 'cms-landing-group',
                        'icon'          => 'layout',
                        'active_routes' => ['public.cms.landing.*', 'public.cms.settings.*'],
                        'children'      => [
                            ['priority' => 1, 'title' => 'Template & Tampilan', 'route' => 'public.cms.landing.index', 'active_routes' => ['public.cms.landing.*'], 'icon' => 'layout', 'permission' => 'public.cms.view'],
                            ['priority' => 2, 'title' => 'Pengaturan', 'route' => 'public.cms.settings.edit', 'active_routes' => ['public.cms.settings.*'], 'icon' => 'settings', 'permission' => 'public.cms.view'],
                        ],
                    ],
                    ['priority' => 10, 'title' => 'Feature', 'route' => 'public.cms.feature.index', 'icon' => 'stars', 'permission' => null],
                    ['priority' => 11, 'title' => 'Product', 'route' => 'public.cms.product.index', 'icon' => 'package', 'permission' => null],
                    ['priority' => 12, 'title' => 'Statistic', 'route' => 'public.cms.statistic.index', 'icon' => 'chart-bar', 'permission' => null],
                    ['priority' => 13, 'title' => 'Client', 'route' => 'public.cms.client.index', 'icon' => 'users', 'permission' => null],
                    ['priority' => 14, 'title' => 'CTA', 'route' => 'public.cms.cta.index', 'icon' => 'button', 'permission' => null],
                    ['priority' => 15, 'title' => 'FAQ', 'route' => 'public.cms.faq.index', 'icon' => 'help', 'permission' => null],
                    ['priority' => 16, 'title' => 'Testimonial', 'route' => 'public.cms.testimonial.index', 'icon' => 'star', 'permission' => null],
                    ['priority' => 17, 'title' => 'Partner', 'route' => 'public.cms.partner.index', 'icon' => 'handshake', 'permission' => null],
                    [
                        'priority'      => 20,
                        'title'         => 'Pengumuman',
                        'route'         => 'public.cms.pengumuman.index',
                        'active_routes' => ['public.cms.pengumuman.*'],
                        'icon'          => 'news',
                        'permission'    => 'public.cms.pengumuman.view',
                    ],
                    [
                        'priority'      => 21,
                        'title'         => 'Berita',
                        'route'         => 'public.cms.berita.index',
                        'icon'          => 'file-text',
                        'permission'    => null,
                    ],
                    [
                        'priority'      => 30,
                        'title'         => 'Halaman & Navigasi',
                        'route'         => 'public.cms.menu.index',
                        'active_routes' => ['public.cms.menu.*', 'public.cms.page.*'],
                        'icon'          => 'sitemap',
                        'permission'    => 'public.cms.menu.view',
                    ],
                    [
                        'priority'      => 40,
                        'title'         => 'Slideshow',
                        'route'         => 'public.cms.slideshow.index',
                        'active_routes' => ['public.cms.slideshow.*'],
                        'icon'          => 'photo',
                        'permission'    => 'public.cms.slideshow.view',
                    ],
                    [
                        'priority'      => 90,
                        'title'         => 'Pratinjau',
                        'route'         => 'public.preview',
                        'active_routes' => [],
                        'icon'          => 'eye',
                        'permission'    => 'public.cms.view',
                        'target'        => '_blank',
                    ],
                ],
            ],
        ];
    }
}
