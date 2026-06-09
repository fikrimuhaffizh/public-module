<?php

namespace Modules\Public\Providers;

use App\Modules\BaseModuleServiceProvider;

class PublicServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'Public';

    protected string $nameLower = 'public';

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
                'permission'    => 'public.cms.view',
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
                        'title'         => 'FAQ',
                        'route'         => 'public.cms.faq.index',
                        'active_routes' => ['public.cms.faq.*'],
                        'icon'          => 'help',
                        'permission'    => 'public.cms.faq.view',
                    ],
                    [
                        'priority'      => 40,
                        'title'         => 'Halaman Statis',
                        'route'         => 'public.cms.page.index',
                        'active_routes' => ['public.cms.page.*'],
                        'icon'          => 'file-text',
                        'permission'    => 'public.cms.page.view',
                    ],
                    [
                        'priority'      => 50,
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
