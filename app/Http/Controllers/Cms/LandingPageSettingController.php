<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\Http\Requests\LandingPageSettingRequest;
use Modules\Public\Models\LandingPageSetting;

class LandingPageSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.settings.view')->only(['editSettings']);
        $this->middleware('permission:public.cms.settings.update')->only(['updateSettings']);
    }

    /**
     * Combined Settings — Kontak, Media Sosial, dan SEO dalam satu halaman.
     */
    public function editSettings()
    {
        return view('public::pages.cms.settings', [
            'settings' => LandingPageSetting::forCurrentTenant(),
        ]);
    }

    public function updateSettings(LandingPageSettingRequest $request)
    {
        $fields = [
            'contact_email', 'contact_phone', 'whatsapp', 'address',
            'facebook_url', 'instagram_url', 'linkedin_url', 'youtube_url',
            'meta_title', 'meta_description', 'meta_keywords',
        ];

        LandingPageSetting::forCurrentTenant()->update(
            Arr::only($request->validated(), $fields)
        );

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
