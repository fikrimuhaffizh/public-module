<?php

use Illuminate\Support\Facades\Route;
use Modules\Public\app\Http\Controllers\Cms\FAQController;
use Modules\Public\app\Http\Controllers\Cms\PengumumanController;
use Modules\Public\app\Http\Controllers\Cms\PublicMenuController;
use Modules\Public\app\Http\Controllers\Cms\PublicPageController;
use Modules\Public\app\Http\Controllers\Cms\SlideshowController;
use Modules\Public\app\Http\Controllers\Cms\LandingSettingsController;
use Modules\Public\app\Http\Controllers\Cms\PartnerController;
use Modules\Public\app\Http\Controllers\Cms\TestimonialController;
use Modules\Public\app\Http\Controllers\Web\PublicController;
use App\Http\Middleware\HandleInertiaRequests;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Admin Area (CMS)
Route::middleware(['auth', 'check.expired'])->prefix('admin/cms')->name('public.cms.')->group(function () {
    Route::get('landing-template', [LandingSettingsController::class, 'edit'])->name('landing.edit');
    Route::put('landing-template', [LandingSettingsController::class, 'update'])->name('landing.update');

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

// Web Area (Landing Page)
Route::middleware(HandleInertiaRequests::class)->prefix('public')->controller(PublicController::class)->name('public.')->group(function () {
    Route::get('/', 'home')->name('index');
    Route::get('/preview', 'preview')->name('preview');
    Route::get('/contact-us', 'contact')->name('contact');
    Route::post('/contact-us', 'sendContact')->middleware('throttle:5,1')->name('contact.send');
    Route::get('/page/{page:slug}', 'showPage')->name('page.show');
    Route::get('/announcements', 'showAllNews')->name('announcements.index');
    Route::get('/news/{pengumuman}', 'showNews')->name('news.show');
});
