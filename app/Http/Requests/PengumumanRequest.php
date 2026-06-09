<?php

namespace Modules\Public\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengumumanRequest extends FormRequest
{
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
}
