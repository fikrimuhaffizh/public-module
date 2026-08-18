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
        $this->middleware('permission:public.cms.settings.view')->only(['editSocial', 'editSeo']);
        $this->middleware('permission:public.cms.settings.update')->only(['updateSocial', 'updateSeo']);
    }

    /**
     * Media Sosial — kontak (email/telepon/WhatsApp/alamat) + akun sosial media.
     */
    public function editSocial()
    {
        return view('public::pages.cms.media-social', [
            'settings' => LandingPageSetting::forCurrentTenant(),
        ]);
    }

    public function updateSocial(LandingPageSettingRequest $request)
    {
        $fields = [
            'contact_email', 'contact_phone', 'whatsapp', 'address',
            'facebook_url', 'instagram_url', 'linkedin_url', 'youtube_url',
        ];

        LandingPageSetting::forCurrentTenant()->update(
            Arr::only($request->validated(), $fields)
        );

        return back()->with('success', 'Media sosial berhasil disimpan.');
    }

    /**
     * SEO — meta title / keywords / description untuk seluruh halaman publik.
     */
    public function editSeo()
    {
        return view('public::pages.cms.seo', [
            'settings' => LandingPageSetting::forCurrentTenant(),
        ]);
    }

    public function updateSeo(LandingPageSettingRequest $request)
    {
        $fields = ['meta_title', 'meta_description', 'meta_keywords'];

        LandingPageSetting::forCurrentTenant()->update(
            Arr::only($request->validated(), $fields)
        );

        return back()->with('success', 'SEO berhasil disimpan.');
    }
}
