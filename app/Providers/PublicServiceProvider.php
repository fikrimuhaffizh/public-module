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
                'priority'      => 1,
                'title'         => 'Section',
                'route'         => 'cms.section.index',
                'active_routes' => ['cms.section.index', 'cms.section.*'],
                'icon'          => 'layout-2',
                'permission'    => 'public.cms.view',
            ],
            [
                'priority'      => 2,
                'title'         => 'Template',
                'route'         => 'cms.section.template',
                'active_routes' => ['cms.section.template', 'cms.section.template.update'],
                'icon'          => 'palette',
                'permission'    => 'public.cms.view',
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
                'title'    => 'CMS',
                'id'       => 'public-cms-group',
                'icon'     => 'world-www',
                'children' => [
                    [
                        'priority'      => 1,
                        'title'         => 'Hero',
                        'route'         => 'cms.hero.index',
                        'active_routes' => ['cms.hero.*'],
                        'icon'          => 'rocket',
                        'permission'    => 'public.cms.view',
                    ],
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
                        'icon'          => 'message-circle-heart',
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
                        'icon'          => 'brand-tailwind',
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
                        'icon'          => 'help-circle',
                        'permission'    => 'public.cms.view',
                    ],
                    [
                        'priority'      => 9,
                        'title'         => 'Partner',
                        'route'         => 'cms.partner.index',
                        'active_routes' => ['cms.partner.*'],
                        'icon'          => 'handshake',
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
                'title'         => 'Pengaturan',
                'route'         => 'cms.settings.edit',
                'active_routes' => ['cms.settings.*'],
                'icon'          => 'settings',
                'permission'    => 'public.cms.settings.view',
            ],
            [
                'priority'      => 99,
                'title'         => 'Pratinjau',
                'route'         => 'public.preview',
                'active_routes' => ['public.preview'],
                'icon'          => 'eye',
                'permission'    => null,
            ],
        ];
    }
}
