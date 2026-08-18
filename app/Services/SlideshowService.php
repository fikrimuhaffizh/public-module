<?php

namespace Modules\Public\Services;

use Modules\Public\Models\Slideshow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SlideshowService
{
    public function getBaseQuery(): Builder
    {
        return Slideshow::query();
    }

    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->getBaseQuery();
    }

    public function findById(string|int $id): Slideshow
    {
        return Slideshow::findOrFail(decryptIdIfEncrypted($id));
    }

    public function createSlideshow(array $data): Slideshow
    {
        return DB::transaction(function () use ($data) {
            $slideshow = Slideshow::create($data);

            if (isset($data['slideshow_image'])) {
                $slideshow->addMedia($data['slideshow_image'])
                    ->toMediaCollection('slideshow_image');
            }

            logActivity('slideshow_management', "Menambah slideshow baru: {$slideshow->title}", $slideshow);

            return $slideshow;
        });
    }

    public function updateSlideshow(string|int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $slideshow = $this->findById($id);
            $slideshow->update($data);

            if (isset($data['slideshow_image'])) {
                $slideshow->addMedia($data['slideshow_image'])
                    ->toMediaCollection('slideshow_image');
            }

            logActivity('slideshow_management', "Memperbarui slideshow: {$slideshow->title}", $slideshow);

            return true;
        });
    }

    public function deleteSlideshow(string|int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $slideshow = $this->findById($id);
            $title = $slideshow->title;

            $slideshow->delete();

            logActivity('slideshow_management', "Menghapus slideshow: {$title}");

            return true;
        });
    }

    public function reorderSlideshows(array $order): bool
    {
        return DB::transaction(function () use ($order) {
            foreach ($order as $index => $encryptedId) {
                $id = decryptIdIfEncrypted($encryptedId, false);
                if ($id) {
                    Slideshow::where((new Slideshow)->getKeyName(), $id)->update(['seq' => $index + 1]);
                }
            }

            logActivity('slideshow_management', 'Memperbarui urutan slideshow');

            return true;
        });
    }
}
