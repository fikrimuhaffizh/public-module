<?php

namespace Modules\Public\Providers;

use App\Modules\BaseModuleServiceProvider;

class PublicServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'Public';

    protected string $nameLower = 'public';

    protected array $commands = [
        // Daftar command khusus modul Public (bila ada).
    ];

    public function boot(): void
    {
        parent::boot();
        $this->mergeConfigFrom(module_path($this->name, 'config/landing_sections.php'), 'landing_sections');
        $this->mergeConfigFrom(module_path($this->name, 'config/themes.php'), 'public_themes');
        $this->mergeConfigFrom(module_path($this->name, 'config/builder_sections.php'), 'builder_sections');
        $this->mergeConfigFrom(module_path($this->name, 'config/builder_templates.php'), 'builder_templates');
        $this->mergeConfigFrom(module_path($this->name, 'config/builder_theme.php'), 'builder_theme');
    }

    protected function menu(): array
    {
        return [
            [
                'priority'      => 0,
                'title'         => 'Dashboard',
                'route'         => 'cms.dashboard',
                'active_routes' => ['cms.dashboard'],
                'icon'          => 'home',
                'permission'    => null,
            ],
            [
                'priority'      => 3,
                'title'         => 'Halaman Statis',
                'route'         => 'cms.page.index',
                'active_routes' => ['cms.page.*'],
                'icon'          => 'file-text',
                'permission'    => 'public.cms.view',
            ],
            [
                'priority'      => 4,
                'title'         => 'Menu Publik',
                'route'         => 'cms.menu.index',
                'active_routes' => ['cms.menu.*'],
                'icon'          => 'list',
                'permission'    => 'public.cms.view',
            ],
            [
                'type'     => 'dropdown',
                'title'    => 'Website Builder',
                'id'       => 'public-builder-group',
                'icon'     => 'palette',
                'children' => [
                    [
                        'priority'      => 1,
                        'title'         => 'Pages',
                        'route'         => 'cms.builder.pages.index',
                        'active_routes' => ['cms.builder.*'],
                        'icon'          => 'files',
                        'permission'    => 'public.builder.view',
                    ],
                ],
            ],
            [
                'type'     => 'dropdown',
                'title'    => 'CMS',
                'id'       => 'public-cms-group',
                'icon'     => 'world-www',
                'children' => [
                    [
                        'priority'      => 2,
                        'title'         => 'Fitur',
                        'route'         => 'cms.feature.index',
                        'active_routes' => ['cms.feature.*'],
                        'icon'          => 'star',
                        'permission'    => 'public.cms.view',
                    ],
                    [
                        'priority'      => 3,
                        'title'         => 'Produk',
                        'route'         => 'cms.product.index',
                        'active_routes' => ['cms.product.*'],
                        'icon'          => 'apps',
                        'permission'    => 'public.cms.view',
                    ],
                    [
                        'priority'      => 4,
                        'title'         => 'Testimoni',
                        'route'         => 'cms.testimonial.index',
                        'active_routes' => ['cms.testimonial.*'],
                        'icon'          => 'message-star',
                        'permission'    => 'public.cms.view',
                    ],
                    [
                        'priority'      => 5,
                        'title'         => 'Statistik',
                        'route'         => 'cms.statistic.index',
                        'active_routes' => ['cms.statistic.*'],
                        'icon'          => 'chart-bar',
                        'permission'    => 'public.cms.view',
                    ],
                    [
                        'priority'      => 6,
                        'title'         => 'Klien',
                        'route'         => 'cms.client.index',
                        'active_routes' => ['cms.client.*'],
                        'icon'          => 'users-group',
                        'permission'    => 'public.cms.view',
                    ],
                    [
                        'priority'      => 7,
                        'title'         => 'CTA',
                        'route'         => 'cms.cta.index',
                        'active_routes' => ['cms.cta.*'],
                        'icon'          => 'alert-circle',
                        'permission'    => 'public.cms.view',
                    ],
                    [
                        'priority'      => 8,
                        'title'         => 'FAQ',
                        'route'         => 'cms.faq.index',
                        'active_routes' => ['cms.faq.*'],
                        'icon'          => 'help',
                        'permission'    => 'public.cms.view',
                    ],
                    [
                        'priority'      => 9,
                        'title'         => 'Partner',
                        'route'         => 'cms.partner.index',
                        'active_routes' => ['cms.partner.*'],
                        'icon'          => 'building-community',
                        'permission'    => 'public.cms.view',
                    ],
                    [
                        'priority'      => 10,
                        'title'         => 'Slideshow',
                        'route'         => 'cms.slideshow.index',
                        'active_routes' => ['cms.slideshow.*'],
                        'icon'          => 'photo',
                        'permission'    => 'public.cms.slideshow.view',
                    ],
                    [
                        'priority'      => 11,
                        'title'         => 'Pengumuman',
                        'route'         => 'cms.pengumuman.index',
                        'active_routes' => ['cms.pengumuman.*', 'cms.berita.*'],
                        'icon'          => 'bell',
                        'permission'    => 'public.cms.pengumuman.view',
                    ],
                ],
            ],
            [
                'priority'      => 50,
                'title'         => 'Media Sosial',
                'route'         => 'cms.media-social.edit',
                'active_routes' => ['cms.media-social.*'],
                'icon'          => 'share',
                'permission'    => 'public.cms.settings.view',
            ],
            [
                'priority'      => 51,
                'title'         => 'SEO',
                'route'         => 'cms.seo.edit',
                'active_routes' => ['cms.seo.*'],
                'icon'          => 'search',
                'permission'    => 'public.cms.settings.view',
            ],
        ];
    }
}
