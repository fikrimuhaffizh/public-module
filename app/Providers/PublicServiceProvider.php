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
                        'priority'      => 10,
                        'title'         => 'Slideshow',
                        'route'         => 'public.cms.slideshow.index',
                        'active_routes' => ['public.cms.slideshow.*'],
                        'icon'          => 'photo',
                        'permission'    => 'public.cms.slideshow.view',
                    ],
                    [
                        'priority'      => 20,
                        'title'         => 'Pengumuman',
                        'route'         => 'public.cms.pengumuman.index',
                        'active_routes' => ['public.cms.pengumuman.*'],
                        'icon'          => 'news',
                        'permission'    => 'public.cms.pengumuman.view',
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
                        'priority'      => 50,
                        'title'         => 'Landing Page',
                        'route'         => 'public.cms.landing.index',
                        'active_routes' => ['public.cms.landing.*'],
                        'icon'          => 'layout',
                        'permission'    => 'public.cms.view',
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
