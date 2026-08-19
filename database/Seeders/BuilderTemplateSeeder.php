<?php

namespace Modules\Public\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\View;
use Modules\Public\Models\BuilderTemplate;

/**
 * Seed starter template GrapesJS dari registry builder_sections.
 *
 * Setiap template di config(builder_templates) berisi daftar `type` section.
 * Komponen Blade (components/builder/{component}.blade.php) dirender dengan
 * content/settings default -> menjadi `text` component GrapesJS. Admin bebas
 * mengubah seluruh markup setelahnya (freeform).
 *
 * Field gambar yang kosong diisi placeholder data-uri SVG agar template
 * tidak tampak "kosong" saat pertama dibuka.
 */
class BuilderTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = config('builder_templates.templates', []);

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId(1);
        }

        $sortOrder = 1;
        foreach ($templates as $key => $template) {
            $project = [
                'components' => $this->buildComponents($template['sections'] ?? []),
                'styles' => [],
            ];

            BuilderTemplate::updateOrCreate(
                ['tenant_id' => 1, 'key' => $key],
                [
                    'name' => $template['name'],
                    'description' => $template['description'] ?? null,
                    'category' => 'marketing',
                    'gjs_project' => $project,
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }

        $this->command?->info('[Public] BuilderTemplateSeeder: template starter GrapesJS dikembangkan.');
    }

    protected function buildComponents(array $sectionTypes): array
    {
        if (empty($sectionTypes)) {
            return [];
        }

        $components = [];

        foreach ($sectionTypes as $definition) {
            $type = is_array($definition) ? ($definition['type'] ?? null) : $definition;
            $meta = $type ? config("builder_sections.sections.{$type}") : null;
            if (! $meta || empty($meta['component'])) {
                continue;
            }

            $content = $meta['defaults']['content'] ?? [];
            $content = $this->fillImagePlaceholders($content);

            try {
                $html = View::make('public::components.builder.'.$meta['component'])->with([
                    'content' => $content,
                    'settings' => $meta['defaults']['settings'] ?? [],
                    'theme' => config('builder_theme', []),
                    'section' => ['type' => $type, 'name' => $meta['name']],
                    'index' => 0,
                ])->render();
            } catch (\Throwable $e) {
                $this->command?->warn("[Public] Template seeder melewati section '{$type}': {$e->getMessage()}");
                continue;
            }

            $html = trim($html);
            if ($html === '') {
                continue;
            }

            $components[] = [
                'type' => 'text',
                'content' => $html,
            ];
        }

        return $components;
    }

    /** Isi field gambar/photo yang masih null dengan placeholder SVG inline. */
    protected function fillImagePlaceholders(array $content): array
    {
        $placeholder = $this->placeholderDataUri();

        if (empty($content['image'])) {
            $content['image'] = $placeholder;
        }

        foreach (['items', 'columns'] as $collectionKey) {
            if (! isset($content[$collectionKey]) || ! is_array($content[$collectionKey])) {
                continue;
            }

            foreach ($content[$collectionKey] as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }
                if (empty($item['image'])) {
                    $item['image'] = $placeholder;
                }
                if (empty($item['photo'])) {
                    $item['photo'] = $placeholder;
                }
                $content[$collectionKey][$index] = $item;
            }
        }

        return $content;
    }

    protected function placeholderDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="640" height="400"><rect width="100%" height="100%" fill="#e2e8f0"/><text x="50%" y="50%" font-family="Arial" font-size="22" fill="#94a3b8" text-anchor="middle" dominant-baseline="middle">Ganti Gambar</text></svg>';

        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }
}