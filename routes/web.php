<?php

use Illuminate\Support\Facades\Route;
use Modules\Public\app\Http\Controllers\Cms\FAQController;
use Modules\Public\app\Http\Controllers\Cms\PengumumanController;
use Modules\Public\app\Http\Controllers\Cms\PublicMenuController;
use Modules\Public\app\Http\Controllers\Cms\PublicPageController;
use Modules\Public\app\Http\Controllers\Cms\SlideshowController;
use Modules\Public\app\Http\Controllers\Cms\ClientController;
use Modules\Public\app\Http\Controllers\Cms\CtaController;
use Modules\Public\app\Http\Controllers\Cms\FeatureController;
use Modules\Public\app\Http\Controllers\Cms\LandingPageSettingController;
use Modules\Public\app\Http\Controllers\Cms\LandingSettingsController;
use Modules\Public\app\Http\Controllers\Cms\PartnerController;
use Modules\Public\app\Http\Controllers\Cms\ProductController;
use Modules\Public\app\Http\Controllers\Cms\StatisticController;
use Modules\Public\app\Http\Controllers\Cms\TestimonialController;
use Modules\Public\app\Http\Controllers\Web\PublicController;
use App\Http\Middleware\HandleInertiaRequests;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Admin Area (CMS)
Route::middleware(['auth', 'check.expired', 'module:public'])->prefix('cms')->name('public.cms.')->group(function () {
    Route::get('landing', [LandingSettingsController::class, 'index'])->name('landing.index');
    Route::get('landing-template', [LandingSettingsController::class, 'edit'])->name('landing.edit');
    Route::put('landing-template', [LandingSettingsController::class, 'update'])->name('landing.update');
    Route::get('landing-sections', [LandingSettingsController::class, 'sections'])->name('landing.sections');
    Route::get('landing-sections/{section}/edit', [LandingSettingsController::class, 'editSection'])->name('landing.section.edit');
    Route::put('landing-sections/{section}', [LandingSettingsController::class, 'updateSection'])->name('landing.section.update');
    Route::post('landing-sections/{section}/toggle', [LandingSettingsController::class, 'toggleSection'])->name('landing.section.toggle');
    Route::post('landing-sections/reorder', [LandingSettingsController::class, 'reorderSections'])->name('landing.sections.reorder');

    Route::get('landing-settings', [LandingPageSettingController::class, 'edit'])->name('settings.edit');
    Route::put('landing-settings', [LandingPageSettingController::class, 'update'])->name('settings.update');

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
    Route::get('faq/data', [FAQController::class, 'data'])->name('faq.data');
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
    Route::get('public-menu/data', [PublicMenuController::class, 'data'])->name('menu.data');
    Route::resource('public-menu', PublicMenuController::class)
        ->names('menu')
        ->parameters(['public-menu' => 'menu']);
});

// Web Area (Landing Page) — no prefix, routes register at / level (e.g. /, /contact-us, /page/{slug})
Route::middleware(HandleInertiaRequests::class)->controller(PublicController::class)->name('public.')->group(function () {
    Route::get('/', 'home')->name('index');
    Route::get('/preview', 'preview')->name('preview');
    Route::get('/contact-us', 'contact')->name('contact');
    Route::post('/contact-us', 'sendContact')->middleware('throttle:5,1')->name('contact.send');
    Route::get('/page/{page:slug}', 'showPage')->name('page.show');
    Route::get('/announcements', 'showAllNews')->name('announcements.index');
    Route::get('/news/{pengumuman}', 'showNews')->name('news.show');
});
