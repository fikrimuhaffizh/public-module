<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, HasFactory, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_page';

    protected $primaryKey = 'page_id';

    protected $appends = ['main_image_url'];

    public const MODE_TEMPLATE = 'template';
    public const MODE_CUSTOM = 'custom';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'render_mode',
        'template_key',
        'meta_desc',
        'meta_keywords',
        'seo_title',
        'is_published',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function builderData(): HasOne
    {
        return $this->hasOne(BuilderPageData::class, 'page_id', 'page_id');
    }

    public function isCustom(): bool
    {
        return $this->render_mode === self::MODE_CUSTOM;
    }

    public function isTemplate(): bool
    {
        return $this->render_mode !== self::MODE_CUSTOM;
    }

    public function getMainImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('main_image');
        return $media ? sys_media_url($media) : null;
    }

    /**
     * URL publik: mode custom diekspos di /{slug} (HTML-first),
     * mode template tetap di /page/{slug} (React/Inertia).
     */
    public function publicUrl(): string
    {
        if ($this->isCustom()) {
            return \Illuminate\Support\Facades\Route::has('public.builder.show')
                ? route('public.builder.show', ['slug' => $this->slug])
                : url('/'.$this->slug);
        }

        return \Illuminate\Support\Facades\Route::has('public.page.show')
            ? route('public.page.show', ['page' => $this->slug])
            : url('/page/'.$this->slug);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field === 'slug') {
            return $this->where('slug', $value)->firstOrFail();
        }

        return $this->resolveHashidRouteBinding($value);
    }

    private function resolveHashidRouteBinding($value)
    {
        $decryptedId = decryptId($value, false);

        return $this->where($this->getRouteKeyName(), $decryptedId ?: decryptId($value))->firstOrFail();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_image')
            ->singleFile();

        $this->addMediaCollection('attachments');

        $this->addMediaCollection('builder_assets');
    }
}