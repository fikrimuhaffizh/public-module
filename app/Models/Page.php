<?php

namespace Modules\Public\app\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, HasFactory, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_page';

    protected $primaryKey = 'page_id';

    protected $appends = ['main_image_url'];

    public function getMainImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('main_image');
        return $media ? sys_media_url($media) : null;
    }

    protected $fillable = [
        'meta_desc',
        'meta_keywords',
        'is_published',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

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
    }
}
