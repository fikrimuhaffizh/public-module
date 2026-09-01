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
                'permission'    => 'cms.dashboard.view',
            ],
            [
                'priority'      => 3,
                'title'         => 'Daftar Halaman',
                'route'         => 'cms.menu.index',
                'active_routes' => ['cms.menu.*', 'cms.page.*'],
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
                'priority'      => 4,
                'title'         => 'CMS Sections',
                'route'         => 'cms.section.index',
                'query'         => ['type' => 'feature'],
                'active_routes' => ['cms.section.*'],
                'icon'          => 'layout-list',
                'permission'    => 'public.cms.view',
            ],
            [
                'priority'      => 50,
                'title'         => 'Kontak & SEO',
                'route'         => 'cms.settings.edit',
                'active_routes' => ['cms.settings.*', 'cms.media-social.*', 'cms.seo.*'],
                'icon'          => 'settings',
                'permission'    => 'public.cms.settings.view',
            ],
        ];
    }
}
