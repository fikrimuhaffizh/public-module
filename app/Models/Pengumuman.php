<?php

namespace Modules\Public\Models;

use Modules\Account\Models\User;
use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Pengumuman extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, HasFactory, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_pengumuman';

    protected $primaryKey = 'pengumuman_id';

    protected $fillable = [
        'judul',
        'isi',
        'jenis',
        'penulis_id',
        'is_published',
        'image_url',
        'published_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'jenis' => 'string',
        'pengumuman_id' => 'string',
    ];

    /**
     * Relationship: Announcement belongs to a penulis (user)
     */
    public function penulis()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->useFallbackUrl(asset('images/no-image.jpg'))
            ->useFallbackPath(public_path('images/no-image.jpg'))
            ->singleFile();

        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media?->collection_name == 'cover') {
            $this->addMediaConversion('small')
                ->fit(Fit::Crop, 150, 150)
                ->optimize()
                ->nonQueued();

            $this->addMediaConversion('medium')
                ->fit(Fit::Crop, 400, 400)
                ->optimize()
                ->nonQueued();
        }
    }

    public function getHasImageAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;

        return ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) || $this->hasMedia('cover');
    }

    public function getIsExternalImageAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;

        return $imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL);
    }

    public function getCoverUrlAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;
        if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        $media = $this->getFirstMedia('cover');
        return $media ? sys_media_url($media) : asset('images/no-image.jpg');
    }

    public function getCoverSmallUrlAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;
        if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        $media = $this->getFirstMedia('cover');
        return $media ? sys_media_url($media, null, 60, 'small') : asset('images/no-image.jpg');
    }

    public function getCoverMediumUrlAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;
        if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        $media = $this->getFirstMedia('cover');
        return $media ? sys_media_url($media, null, 60, 'medium') : asset('images/no-image.jpg');
    }

    public function getAttachmentsUrlAttribute()
    {
        $media = $this->getFirstMedia('attachments');
        return $media ? sys_media_url($media) : null;
    }

}
