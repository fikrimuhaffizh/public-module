<?php

namespace Modules\Public\app\Http\Requests;

use App\Http\Requests\BaseRequest;

class PengumumanRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'jenis' => 'required|string|in:pengumuman,berita',
            'penulis_id' => 'nullable|exists:users,id',
            'is_published' => 'nullable|boolean',
            'image_url' => 'nullable|url',
            'cover' => 'nullable|image|max:2048',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }

    public function attributes(): array
    {
        return [
            'judul' => 'Judul',
            'isi' => 'Isi Konten',
            'jenis' => 'Jenis',
            'penulis_id' => 'Penulis',
            'is_published' => 'Status Publikasi',
            'image_url' => 'URL Gambar',
            'cover' => 'Gambar Sampul',
            'attachments' => 'Lampiran',
            'attachments.*' => 'Lampiran',
        ];
    }
}
