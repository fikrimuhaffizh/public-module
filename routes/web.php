<?php

use Illuminate\Support\Facades\Route;
use Modules\Public\Http\Controllers\Cms\FAQController;
use Modules\Public\Http\Controllers\Cms\PengumumanController;
use Modules\Public\Http\Controllers\Cms\PublicMenuController;
use Modules\Public\Http\Controllers\Cms\PublicPageController;
use Modules\Public\Http\Controllers\Cms\SlideshowController;
use Modules\Public\Http\Controllers\Cms\ClientController;
use Modules\Public\Http\Controllers\Cms\CtaController;
use Modules\Public\Http\Controllers\Cms\FeatureController;
use Modules\Public\Http\Controllers\Cms\LandingPageSettingController;
use Modules\Public\Http\Controllers\Cms\SectionController;
use Modules\Public\Http\Controllers\Cms\PartnerController;
use Modules\Public\Http\Controllers\Cms\ProductController;
use Modules\Public\Http\Controllers\Cms\StatisticController;
use Modules\Public\Http\Controllers\Cms\TestimonialController;
use Modules\Public\Http\Controllers\Web\PublicController;
use App\Http\Middleware\HandleInertiaRequests;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Admin Area (CMS)
Route::middleware(['auth', 'check.expired', 'module:public'])->prefix('cms')->name('cms.')->group(function () {
    Route::get('/dashboard', [Modules\Public\Http\Controllers\Cms\DashboardController::class, 'index'])->name('dashboard');

    // Section Management (was landing)
    Route::get('section', [SectionController::class, 'index'])->name('section.index');
    Route::get('section/template', [SectionController::class, 'edit'])->name('section.template');
    Route::put('section/template', [SectionController::class, 'update'])->name('section.template.update');
    Route::get('section/{section}/edit', [SectionController::class, 'editSection'])->name('section.edit');
    Route::put('section/{section}', [SectionController::class, 'updateSection'])->name('section.update');
    Route::post('section/{section}/toggle', [SectionController::class, 'toggleSection'])->name('section.toggle');
    Route::post('section/reorder', [SectionController::class, 'reorderSections'])->name('section.reorder');
    Route::post('section/reorder-all', [SectionController::class, 'reorderAllSections'])->name('section.reorder-all');
    Route::post('section/upload-logo', [SectionController::class, 'uploadLogo'])->name('section.upload-logo');
    Route::post('section/upload-background', [SectionController::class, 'uploadBackground'])->name('section.upload-background');
    Route::delete('section/delete-logo/{collection}', [SectionController::class, 'deleteLogo'])->name('section.delete-logo');

    // Legacy redirects (backward compatibility)
    Route::redirect('landing', '/cms/section')->name('landing.index');
    Route::redirect('landing-template', '/cms/section/template')->name('landing.edit');
    Route::redirect('landing-sections', '/cms/section')->name('landing.sections');

    Route::get('media-social', [LandingPageSettingController::class, 'editSocial'])->name('media-social.edit');
    Route::put('media-social', [LandingPageSettingController::class, 'updateSocial'])->name('media-social.update');
    Route::get('seo', [LandingPageSettingController::class, 'editSeo'])->name('seo.edit');
    Route::put('seo', [LandingPageSettingController::class, 'updateSeo'])->name('seo.update');

    Route::post('feature/reorder', [FeatureController::class, 'reorder'])->name('feature.reorder');
    Route::resource('feature', FeatureController::class)->except('show');
    Route::post('product/reorder', [ProductController::class, 'reorder'])->name('product.reorder');
    Route::resource('product', ProductController::class)->except('show');
    Route::post('statistic/reorder', [StatisticController::class, 'reorder'])->name('statistic.reorder');
    Route::resource('statistic', StatisticController::class)->except('show');
    Route::post('client/reorder', [ClientController::class, 'reorder'])->name('client.reorder');
    Route::resource('client', ClientController::class)->except('show');
    Route::resource('cta', CtaController::class)->except('show');

    // FAQ
    Route::post('faq/reorder', [FAQController::class, 'reorder'])->name('faq.reorder');
    Route::resource('faq', FAQController::class);

    // Pengumuman
    Route::get('pengumuman/data', [PengumumanController::class, 'data'])->name('pengumuman.data');
    Route::resource('pengumuman', PengumumanController::class);

    // Berita (menggunakan PengumumanController dengan type berita)
    Route::prefix('berita')->name('berita.')->controller(PengumumanController::class)->group(function () {
        Route::get('/data', 'data')->name('data');
        Route::get('/', 'beritaIndex')->name('index');
        Route::get('/create', 'create')->defaults('type', 'berita')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{pengumuman}', 'show')->name('show');
        Route::get('/{pengumuman}/edit', 'edit')->name('edit');
        Route::put('/{pengumuman}', 'update')->name('update');
        Route::delete('/{pengumuman}', 'destroy')->name('destroy');
    });

    // Slideshow
    Route::post('slideshow/reorder', [SlideshowController::class, 'reorder'])->name('slideshow.reorder');
    Route::get('slideshow/data', [SlideshowController::class, 'data'])->name('slideshow.data');
    Route::resource('slideshow', SlideshowController::class);

    Route::post('testimonial/reorder', [TestimonialController::class, 'reorder'])->name('testimonial.reorder');
    Route::resource('testimonial', TestimonialController::class)->except('show');

    Route::post('partner/reorder', [PartnerController::class, 'reorder'])->name('partner.reorder');
    Route::resource('partner', PartnerController::class)->except('show');

    // Pages
    Route::get('public-page/data', [PublicPageController::class, 'data'])->name('page.data');
    Route::resource('public-page', PublicPageController::class)
        ->names('page')
        ->parameters(['public-page' => 'page']);

    // Public Menu
    Route::post('public-menu/reorder', [PublicMenuController::class, 'reorder'])->name('menu.reorder');
    Route::post('public-menu/reorder-position', [PublicMenuController::class, 'reorderPosition'])->name('menu.reorder-position');
    Route::get('public-menu/data', [PublicMenuController::class, 'data'])->name('menu.data');
    Route::resource('public-menu', PublicMenuController::class)
        ->names('menu')
        ->parameters(['public-menu' => 'menu']);
});

// Web Area (Landing Page) — no prefix, routes register at / level (e.g. /, /contact-us, /page/{slug})
Route::middleware(HandleInertiaRequests::class)->controller(PublicController::class)->name('public.')->group(function () {
    Route::get('/', 'home')->name('index');
    Route::get('/preview', 'preview')->name('preview');
    Route::post('/preview/design', 'saveDesign')->middleware('auth')->name('design.save');
    Route::get('/contact-us', 'contact')->name('contact');
    Route::post('/contact-us', 'sendContact')->middleware('throttle:5,1')->name('contact.send');
    Route::get('/page/{page:slug}', 'showPage')->name('page.show');
    Route::get('/announcements', 'showAllNews')->name('announcements.index');
    Route::get('/news/{pengumuman}', 'showNews')->name('news.show');
});
