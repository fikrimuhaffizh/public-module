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
                'route'         => 'cms.landing.index',
                'active_routes' => ['cms.landing.index', 'cms.landing.section.*'],
                'icon'          => 'layout-2',
                'permission'    => 'public.cms.view',
            ],
            [
                'priority'      => 2,
                'title'         => 'Template',
                'route'         => 'cms.landing.edit',
                'active_routes' => ['cms.landing.edit', 'cms.landing.update'],
                'icon'          => 'palette',
                'permission'    => 'public.cms.view',
            ],
            [
                'priority'      => 3,
                'title'         => 'Slideshow',
                'route'         => 'cms.slideshow.index',
                'active_routes' => ['cms.slideshow.*'],
                'icon'          => 'photo',
                'permission'    => 'public.cms.slideshow.view',
            ],
            [
                'priority'      => 4,
                'title'         => 'Pengumuman',
                'route'         => 'cms.pengumuman.index',
                'active_routes' => ['cms.pengumuman.*', 'cms.berita.*'],
                'icon'          => 'bell',
                'permission'    => 'public.cms.pengumuman.view',
            ],
            [
                'priority'      => 5,
                'title'         => 'Pengaturan',
                'route'         => 'cms.settings.edit',
                'active_routes' => ['cms.settings.*'],
                'icon'          => 'settings',
                'permission'    => 'public.cms.settings.view',
            ],
            [
                'priority'      => 6,
                'title'         => 'Pratinjau',
                'route'         => 'public.preview',
                'active_routes' => ['public.preview'],
                'icon'          => 'world-www',
                'permission'    => null,
            ],
        ];
    }
}
