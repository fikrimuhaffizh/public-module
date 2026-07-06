<?php

namespace Modules\Public\Services;

use Modules\Public\Models\Pengumuman;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengumumanService
{
    public function getBaseQuery(): Builder
    {
        return Pengumuman::with(['penulis', 'media']);
    }

    public function getFilteredQuery(string $type): Builder
    {
        return $this->getBaseQuery()
            ->where('jenis', $type);
    }

    public function findById(string|int $id): Pengumuman
    {
        return Pengumuman::findOrFail(decryptIdIfEncrypted($id));
    }

    public function createPengumuman(array $data): Pengumuman
    {
        return DB::transaction(function () use ($data) {
            $isPublished = $data['is_published'] ?? false;

            $pengumuman = Pengumuman::create([
                'judul' => $data['judul'],
                'isi' => $data['isi'],
                'jenis' => $data['jenis'],
                'penulis_id' => Auth::id(),
                'is_published' => $isPublished,
                'image_url' => $data['image_url'] ?? null,
                'published_at' => $isPublished ? now() : null,
            ]);

            $this->handleMedia($pengumuman, $data);

            logActivity(
                'pengumuman_management',
                "Membuat {$data['jenis']} baru: {$pengumuman->judul}"
            );

            return $pengumuman;
        });
    }

    public function updatePengumuman(string|int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $pengumuman = $this->findById($id);
            $oldTitle = $pengumuman->judul;

            $isPublished = $data['is_published'] ?? false;

            $pengumuman->update([
                'judul' => $data['judul'],
                'isi' => $data['isi'],
                'is_published' => $isPublished,
                'image_url' => $data['image_url'] ?? $pengumuman->image_url,
                'published_at' => $isPublished ? now() : $pengumuman->published_at,
            ]);

            $this->handleMedia($pengumuman, $data);

            logActivity(
                'pengumuman_management',
                "Memperbarui {$pengumuman->jenis}: {$oldTitle}".($oldTitle !== $pengumuman->judul ? " menjadi {$pengumuman->judul}" : '')
            );

            return true;
        });
    }

    public function deletePengumuman(string|int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $pengumuman = $this->findById($id);
            $jenis = $pengumuman->jenis;
            $judul = $pengumuman->judul;

            $pengumuman->delete();

            logActivity('pengumuman_management', "Menghapus {$jenis}: {$judul}");

            return true;
        });
    }

    protected function handleMedia(Pengumuman $pengumuman, array $data)
    {
        if (isset($data['cover']) && $data['cover']) {
            $pengumuman->clearMediaCollection('cover');
            $pengumuman->addMedia($data['cover'])->toMediaCollection('cover');
        }

        if (isset($data['attachments']) && is_array($data['attachments'])) {
            if ($pengumuman->exists && count($data['attachments']) > 0) {
                $pengumuman->clearMediaCollection('attachments');
            }

            foreach ($data['attachments'] as $file) {
                $pengumuman->addMedia($file)->toMediaCollection('attachments');
            }
        }
    }
}
