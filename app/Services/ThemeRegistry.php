<?php

namespace Modules\Public\Services;

/**
 * Registri tema landing page.
 *
 * Sumber data tunggal: config('public_themes') (dari Modules/Public/config/themes.php).
 * Semua tempat yang butuh daftar/validasi tema harus lewat service ini —
 * jangan hardcode daftar tema di controller/view/komponen lain.
 */
class ThemeRegistry
{
    public const DEFAULT = 'modern';

    /** Semua tema: key => metadata. */
    public function all(): array
    {
        return config('public_themes', []);
    }

    /** Daftar key tema (untuk validasi Rule::in, dsb.). */
    public function keys(): array
    {
        return array_keys($this->all());
    }

    /** Metadata satu tema, atau null bila tidak ada. */
    public function get(string $key): ?array
    {
        return $this->all()[$key] ?? null;
    }

    public function isValid(?string $key): bool
    {
        return $key !== null && isset($this->all()[$key]);
    }

    public function default(): string
    {
        return self::DEFAULT;
    }

    /** Tema dikelompokkan per kategori (institutional / umkm) untuk UI CMS. */
    public function categories(): array
    {
        $grouped = [];
        foreach ($this->all() as $key => $meta) {
            $category = $meta['category'] ?? 'institutional';
            $grouped[$category][$key] = $meta;
        }

        return $grouped;
    }
}
