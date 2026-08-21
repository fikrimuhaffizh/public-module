<?php

namespace Modules\Public\Traits;

use Illuminate\Database\Eloquent\Model;
use Modules\Public\Services\DynamicBlockService;

/**
 * Trait that automatically clears dynamic block cache when model is updated.
 *
 * Add this trait to models that are used in dynamic blocks:
 * - FAQ
 * - Pengumuman
 * - Testimonial
 * - Statistic
 * - Slideshow
 * - Client
 * - Partner
 *
 * Usage:
 *   class FAQ extends Model
 *   {
 *       use ClearsDynamicBlockCache;
 *
 *       protected static function dynamicBlockType(): string
 *       {
 *           return 'faq';
 *       }
 *   }
 */
trait ClearsDynamicBlockCache
{
    /**
     * Boot the trait - register model event listeners.
     */
    public static function bootClearsDynamicBlockCache(): void
    {
        static::saved(function (Model $model) {
            $model->clearDynamicBlockCache('saved');
        });

        static::deleted(function (Model $model) {
            $model->clearDynamicBlockCache('deleted');
        });
    }

    /**
     * Clear dynamic block cache for this model type.
     */
    protected function clearDynamicBlockCache(string $event): void
    {
        try {
            $type = static::dynamicBlockType();

            if (empty($type)) {
                return;
            }

            $service = app(DynamicBlockService::class);
            $service->clearCache($type);

            // Log the cache invalidation
            if (function_exists('logActivity')) {
                logActivity(
                    'dynamic_block_cache',
                    "Cache dynamic block '{$type}' di-clear setelah {$event}: " . static::class . " #{$this->getKey()}"
                );
            }
        } catch (\Throwable $e) {
            // Don't let cache clearing break the main operation
            \Log::warning('Failed to clear dynamic block cache', [
                'model' => static::class,
                'id' => $this->getKey(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the dynamic block type for this model.
     * Must be implemented by the model.
     */
    abstract protected static function dynamicBlockType(): string;
}
