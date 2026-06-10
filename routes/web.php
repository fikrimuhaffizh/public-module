<?php

use Illuminate\Support\Facades\Route;
use Modules\Public\app\Http\Controllers\Admin\FAQController;
use Modules\Public\app\Http\Controllers\Admin\PengumumanController;
use Modules\Public\app\Http\Controllers\Admin\PublicMenuController;
use Modules\Public\app\Http\Controllers\Admin\PublicPageController;
use Modules\Public\app\Http\Controllers\Admin\SlideshowController;
use Modules\Public\app\Http\Controllers\Web\PublicController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Admin Area (CMS)
Route::middleware(['auth', 'check.expired'])->prefix('admin/cms')->name('public.cms.')->group(function () {

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
Route::controller(PublicController::class)->name('public.')->group(function () {
    Route::get('/', 'home')->name('index');
    Route::get('/preview', 'preview')->name('preview');
    Route::get('/page/{page:slug}', 'showPage')->name('page.show');
    Route::get('/announcements', 'showAllNews')->name('announcements.index');
    Route::get('/news/{pengumuman}', 'showNews')->name('news.show');
});
