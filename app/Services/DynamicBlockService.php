<?php

namespace Modules\Public\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Public\Models\FAQ;
use Modules\Public\Models\Pengumuman;
use Modules\Public\Models\Testimonial;
use Modules\Public\Models\Statistic;
use Modules\Public\Models\Slideshow;
use Modules\Public\Models\Client;
use Modules\Public\Models\Partner;

/**
 * Resolves dynamic content in page builder HTML.
 *
 * Supports two approaches:
 * 1. Placeholders: {{type:key=value&key2=value2}}
 * 2. CMS-backed elements: <div data-cms-source="faq" data-cms-params="limit=10">...</div>
 *
 * Supported types: faq, pengumuman, testimonial, statistik, slideshow, client, partner
 *
 * Example placeholder: {{faq:category=akademik&limit=5&is_active=true}}
 * Example CMS-backed: <div data-cms-source="faq" data-cms-params="limit=10&is_active=true">items from DB</div>
 */
class DynamicBlockService
{
    /**
     * Cache TTL in seconds (default 5 minutes).
     */
    private const CACHE_TTL = 300;

    /**
     * Resolve all dynamic content in HTML.
     * Handles both {{type:params}} placeholders and data-cms-source elements.
     */
    public function resolve(string $html): string
    {
        // 1. Resolve {{type:params}} placeholders (legacy/dynamic blocks)
        $html = $this->resolvePlaceholders($html);

        // 2. Resolve data-cms-source elements (CMS-backed blocks)
        $html = $this->resolveCmsElements($html);

        return $html;
    }

    /**
     * Resolve {{type:params}} placeholders.
     */
    private function resolvePlaceholders(string $html): string
    {
        return preg_replace_callback(
            '/\{\{(faq|pengumuman|testimonial|statistik|slideshow|client|partner):([^}]+)\}\}/',
            function ($matches) {
                $type = $matches[1];
                $params = $this->parseParams($matches[2]);

                try {
                    return $this->renderBlock($type, $params);
                } catch (\Throwable $e) {
                    return '';
                }
            },
            $html
        );
    }

    /**
     * Resolve elements with data-cms-source attribute.
     * Replaces inner content with data from database.
     * Keeps the outer wrapper (section, container, heading) intact.
     */
    private function resolveCmsElements(string $html): string
    {
        // Match <element data-cms-source="type" data-cms-params="...">...</element>
        return preg_replace_callback(
            '/<([a-z][a-z0-9]*)\s+[^>]*data-cms-source="([a-z]+)"[^>]*data-cms-params="([^"]*)"[^>]*>([\s\S]*?)<\/\1>/',
            function ($matches) {
                $tagName = $matches[1];
                $type = $matches[2];
                $paramsStr = $matches[3];
                // $matches[4] = old inner content (ignored, replaced by DB data)

                $params = $this->parseParams($paramsStr);

                try {
                    $rendered = $this->renderBlock($type, $params);
                    if ($rendered === '') {
                        return $matches[0]; // Keep original if no data
                    }
                    // Extract inner content from rendered block
                    $innerHtml = $this->extractInnerContent($rendered);
                    return "<{$tagName} data-cms-source=\"{$type}\" data-cms-params=\"{$paramsStr}\" data-cms-resolved=\"true\">{$innerHtml}</{$tagName}>";
                } catch (\Throwable $e) {
                    return $matches[0]; // Keep original on error
                }
            },
            $html
        );
    }

    /**
     * Extract inner content from a rendered block.
     * The rendered block is a full <section> wrapper; we only need the content inside.
     */
    private function extractInnerContent(string $rendered): string
    {
        // Find the content inside <section>...</section>
        if (preg_match('/<section[^>]*>([\s\S]*)<\/section>/', $rendered, $m)) {
            return trim($m[1]);
        }
        return $rendered;
    }

    /**
     * Parse placeholder parameters from string.
     */
    private function parseParams(string $paramString): array
    {
        $params = [];
        parse_str(str_replace('&amp;', '&', $paramString), $params);

        // Normalize boolean values
        foreach ($params as $key => $value) {
            if ($value === 'true') {
                $params[$key] = true;
            } elseif ($value === 'false') {
                $params[$key] = false;
            } elseif (is_numeric($value)) {
                $params[$key] = (int) $value;
            }
        }

        return $params;
    }

    /**
     * Render a dynamic block based on type and parameters.
     */
    private function renderBlock(string $type, array $params): string
    {
        $cacheKey = "dynamic_block:{$type}:" . md5(serialize($params));

        $result = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($type, $params) {
            return match ($type) {
                'faq' => $this->renderFaq($params),
                'pengumuman' => $this->renderPengumuman($params),
                'testimonial' => $this->renderTestimonial($params),
                'statistik' => $this->renderStatistik($params),
                'slideshow' => $this->renderSlideshow($params),
                'client' => $this->renderClient($params),
                'partner' => $this->renderPartner($params),
                default => '',
            };
        });

        $this->trackCacheKey($cacheKey);

        return $result;
    }

    /**
     * Render FAQ dynamic block.
     *
     * Params:
     *   - category: string (optional) - Filter by category
     *   - limit: int (optional, default 5) - Number of items
     *   - is_active: bool (optional, default true) - Filter active only
     */
    private function renderFaq(array $params): string
    {
        $query = FAQ::query();

        if (!empty($params['category'])) {
            $query->where('category', $params['category']);
        }

        if ($params['is_active'] ?? true) {
            $query->where('is_active', true);
        }

        $limit = $params['limit'] ?? 5;
        $faqs = $query->orderBy('seq')->limit($limit)->get();

        if ($faqs->isEmpty()) {
            return '';
        }

        $items = $faqs->map(function ($faq) {
            return <<<HTML
            <details>
                <summary>{$faq->question}</summary>
                <div class="wbp-faq-body">{$faq->answer}</div>
            </details>
            HTML;
        })->implode("\n");

        return <<<HTML
        <section class="wbp-section wbp-bg-white wbp-py-lg wbp-dynamic-faq">
            <div class="wbp-container wbp-container-narrow">
                <div class="wbp-faq">
                    {$items}
                </div>
            </div>
        </section>
        HTML;
    }

    /**
     * Render Pengumuman (Announcement) dynamic block.
     *
     * Params:
     *   - type: string (optional) - Filter by type (berita, pengumuman, agenda)
     *   - limit: int (optional, default 3) - Number of items
     *   - is_published: bool (optional, default true) - Filter published only
     */
    private function renderPengumuman(array $params): string
    {
        $query = Pengumuman::query();

        if (!empty($params['type'])) {
            $query->where('jenis', $params['type']);
        }

        if ($params['is_published'] ?? true) {
            $query->where('is_published', true);
        }

        $limit = $params['limit'] ?? 3;
        $items = $query->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        if ($items->isEmpty()) {
            return '';
        }

        $cards = $items->map(function ($item) {
            $date = $item->published_at instanceof \Carbon\Carbon
                ? $item->published_at->format('d M Y')
                : (is_string($item->published_at) ? \Carbon\Carbon::parse($item->published_at)->format('d M Y') : '');
            $excerpt = \Illuminate\Support\Str::limit(strip_tags($item->isi ?? ''), 120);
            $coverHtml = '';
            try {
                if ($item->hasMedia('cover')) {
                    $coverHtml = '<img src="' . $item->getFirstMedia('cover')->getUrl() . '" alt="' . htmlspecialchars($item->judul) . '" class="wbp-card-img">';
                }
            } catch (\Throwable) {}

            return <<<HTML
            <div class="wbp-card">
                {$coverHtml}
                <div class="wbp-card-body">
                    <span class="wbp-card-date">{$date}</span>
                    <h3 class="wbp-card-title">{$item->judul}</h3>
                    <p class="wbp-card-text">{$excerpt}</p>
                </div>
            </div>
            HTML;
        })->implode("\n");

        return <<<HTML
        <section class="wbp-section wbp-bg-gray wbp-py-lg wbp-dynamic-pengumuman">
            <div class="wbp-container">
                <h2 class="wbp-title wbp-title-lg wbp-text-center" style="margin-bottom:2rem">Berita & Pengumuman</h2>
                <div class="wbp-grid wbp-grid-3">
                    {$cards}
                </div>
            </div>
        </section>
        HTML;
    }

    /**
     * Render Testimonial dynamic block.
     *
     * Params:
     *   - limit: int (optional, default 3) - Number of items
     *   - is_active: bool (optional, default true) - Filter active only
     */
    private function renderTestimonial(array $params): string
    {
        $query = Testimonial::query();

        if ($params['is_active'] ?? true) {
            $query->where('is_active', true);
        }

        $limit = $params['limit'] ?? 3;
        $testimonials = $query->orderBy('seq')
            ->limit($limit)
            ->get();

        if ($testimonials->isEmpty()) {
            return '';
        }

        $items = $testimonials->map(function ($t) {
            $stars = str_repeat('⭐', $t->rating ?? 5);
            $photo = $t->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($t->name);

            return <<<HTML
            <div class="wbp-card wbp-card-testimonial">
                <div class="wbp-card-body">
                    <div class="wbp-testimonial-stars">{$stars}</div>
                    <p class="wbp-testimonial-quote">"{$t->quote}"</p>
                    <div class="wbp-testimonial-author">
                        <img src="{$photo}" alt="{$t->name}" class="wbp-testimonial-avatar">
                        <div>
                            <strong>{$t->name}</strong>
                            <span>{$t->position}, {$t->organization}</span>
                        </div>
                    </div>
                </div>
            </div>
            HTML;
        })->implode("\n");

        return <<<HTML
        <section class="wbp-section wbp-bg-white wbp-py-lg wbp-dynamic-testimonial">
            <div class="wbp-container">
                <h2 class="wbp-title wbp-title-lg wbp-text-center" style="margin-bottom:2rem">Apa Kata Mereka</h2>
                <div class="wbp-grid wbp-grid-3">
                    {$items}
                </div>
            </div>
        </section>
        HTML;
    }

    /**
     * Render Statistik dynamic block.
     *
     * Params:
     *   - limit: int (optional, default 4) - Number of items
     *   - is_active: bool (optional, default true) - Filter active only
     */
    private function renderStatistik(array $params): string
    {
        $query = Statistic::query();

        if ($params['is_active'] ?? true) {
            $query->where('is_active', true);
        }

        $limit = $params['limit'] ?? 4;
        $stats = $query->orderBy('sort_order')
            ->limit($limit)
            ->get();

        if ($stats->isEmpty()) {
            return '';
        }

        $items = $stats->map(function ($stat) {
            return <<<HTML
            <div class="wbp-statistic">
                <div class="wbp-statistic-value">{$stat->value}</div>
                <div class="wbp-statistic-label">{$stat->label}</div>
            </div>
            HTML;
        })->implode("\n");

        return <<<HTML
        <section class="wbp-section wbp-bg-brand wbp-py-lg wbp-dynamic-statistik">
            <div class="wbp-container">
                <div class="wbp-statistics">
                    {$items}
                </div>
            </div>
        </section>
        HTML;
    }

    /**
     * Render Slideshow dynamic block.
     *
     * Params:
     *   - limit: int (optional, default 5) - Number of items
     *   - is_active: bool (optional, default true) - Filter active only
     */
    private function renderSlideshow(array $params): string
    {
        $query = Slideshow::query();

        if ($params['is_active'] ?? true) {
            $query->where('is_active', true);
        }

        $limit = $params['limit'] ?? 5;
        $slides = $query->orderBy('seq')
            ->limit($limit)
            ->get();

        if ($slides->isEmpty()) {
            return '';
        }

        $items = $slides->map(function ($slide, $index) {
            $active = $index === 0 ? 'active' : '';
            $caption = $slide->caption ?? '';
            $linkUrl = $slide->link ?? '';
            $captionHtml = '';
            if ($caption) {
                $captionHtml = '<div class="wbp-slide-caption">';
                $captionHtml .= '<h2>' . htmlspecialchars($slide->title ?? '') . '</h2>';
                $captionHtml .= '<p>' . htmlspecialchars($caption) . '</p>';
                if ($linkUrl) {
                    $captionHtml .= '<a href="' . htmlspecialchars($linkUrl) . '" class="wbp-btn wbp-btn-white">Selengkapnya</a>';
                }
                $captionHtml .= '</div>';
            }

            return <<<HTML
            <div class="wbp-slide {$active}">
                <img src="{$slide->large_url}" alt="{$slide->title}">
                {$captionHtml}
            </div>
            HTML;
        })->implode("\n");

        return <<<HTML
        <div class="wbp-slideshow">
            {$items}
        </div>
        HTML;
    }

    /**
     * Render Client dynamic block.
     *
     * Params:
     *   - limit: int (optional, default 8) - Number of items
     *   - is_active: bool (optional, default true) - Filter active only
     */
    private function renderClient(array $params): string
    {
        $query = Client::query();

        if ($params['is_active'] ?? true) {
            $query->where('is_active', true);
        }

        $limit = $params['limit'] ?? 8;
        $clients = $query->orderBy('sort_order')
            ->limit($limit)
            ->get();

        if ($clients->isEmpty()) {
            return '';
        }

        $items = $clients->map(function ($client) {
            $website = $client->website ? "href=\"{$client->website}\" target=\"_blank\"" : '';
            $logo = $client->logo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($client->name);

            return <<<HTML
            <a {$website} class="wbp-client-logo">
                <img src="{$logo}" alt="{$client->name}" loading="lazy">
            </a>
            HTML;
        })->implode("\n");

        return <<<HTML
        <section class="wbp-section wbp-bg-white wbp-py-lg wbp-dynamic-client">
            <div class="wbp-container">
                <h2 class="wbp-title wbp-title-lg wbp-text-center" style="margin-bottom:2rem">Mitra Kami</h2>
                <div class="wbp-clients">
                    {$items}
                </div>
            </div>
        </section>
        HTML;
    }

    /**
     * Render Partner dynamic block.
     *
     * Params:
     *   - category: string (optional) - Filter by category
     *   - limit: int (optional, default 8) - Number of items
     *   - is_active: bool (optional, default true) - Filter active only
     */
    private function renderPartner(array $params): string
    {
        $query = Partner::query();

        if (!empty($params['category'])) {
            $query->where('category', $params['category']);
        }

        if ($params['is_active'] ?? true) {
            $query->where('is_active', true);
        }

        $limit = $params['limit'] ?? 8;
        $partners = $query->orderBy('sort_order')
            ->limit($limit)
            ->get();

        if ($partners->isEmpty()) {
            return '';
        }

        $items = $partners->map(function ($partner) {
            $website = $partner->website_url ? "href=\"{$partner->website_url}\" target=\"_blank\"" : '';
            $logo = $partner->logo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($partner->name);

            return <<<HTML
            <a {$website} class="wbp-partner-logo">
                <img src="{$logo}" alt="{$partner->name}" loading="lazy">
            </a>
            HTML;
        })->implode("\n");

        return <<<HTML
        <section class="wbp-section wbp-bg-gray wbp-py-lg wbp-dynamic-partner">
            <div class="wbp-container">
                <h2 class="wbp-title wbp-title-lg wbp-text-center" style="margin-bottom:2rem">Partner Kami</h2>
                <div class="wbp-partners">
                    {$items}
                </div>
            </div>
        </section>
        HTML;
    }

    /**
     * Clear cache for a specific dynamic block type.
     */
    public function clearCache(string $type): void
    {
        // Clear cache keys matching the pattern using Cache::forget()
        // We store keys in a known set to support clearing.
        $keys = Cache::get('dynamic_block_keys', []);
        foreach ($keys as $key) {
            if (str_starts_with($key, "dynamic_block:{$type}:")) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Clear all dynamic block cache.
     */
    public function clearAllCache(): void
    {
        $keys = Cache::get('dynamic_block_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('dynamic_block_keys');
    }

    /**
     * Register a cache key so it can be cleared later.
     */
    private function trackCacheKey(string $cacheKey): void
    {
        $keys = Cache::get('dynamic_block_keys', []);
        if (!in_array($cacheKey, $keys)) {
            $keys[] = $cacheKey;
            // Store with extra TTL so it outlives individual cache entries
            Cache::put('dynamic_block_keys', $keys, self::CACHE_TTL + 60);
        }
    }
}
