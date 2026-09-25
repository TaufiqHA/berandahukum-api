<?php

use App\Http\Controllers\Admin\AdsController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KomentarController;
use App\Http\Controllers\Admin\LabelController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PertanyaanController as AdminPertanyaanController;
use App\Http\Controllers\Admin\PilihanController;
use App\Http\Controllers\Admin\PopupController;
use App\Http\Controllers\Admin\QuotesController;
use App\Http\Controllers\Admin\SettingemailController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SettingscategoryController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SosialmediaController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Front\ArticleController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PertanyaanController;
use App\Http\Controllers\Front\RssController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\SettingsController as FrontSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::middleware('front.share')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/a/{uri}', [ArticleController::class, 'detail']);
    Route::get('/k/{uri}', [ArticleController::class, 'kategori']);
    Route::get('/s/{uri}', [ArticleController::class, 'subkategori']);
    Route::get('/l/{uri}', [ArticleController::class, 'label']);
    Route::get('/article/{uri}', [ArticleController::class, 'detail']);

    Route::get('/search', [SearchController::class, 'index']);

    Route::get('/rss', [RssController::class, 'index']);
    Route::get('/rss/{uri}', [RssController::class, 'getrss']);

    Route::get('/pertanyaan', [PertanyaanController::class, 'index']);
    Route::get('/pertanyaan/{page}', [PertanyaanController::class, 'index']);
    Route::match(['get', 'post'], '/kirimpertanyaan', [PertanyaanController::class, 'form']);

    Route::match(['get', 'post'], '/contact', [ContactController::class, 'index']);
    Route::get('/informasi/{id}', [FrontSettingsController::class, 'info']);

    Route::post('/add_comment', [ArticleController::class, 'addComment']);
    Route::post('/load_comment', [ArticleController::class, 'loadComment']);
});

/*
|--------------------------------------------------------------------------
| Admin — autentikasi
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::get('/forget', [AuthController::class, 'showForget']);
Route::post('/forget', [AuthController::class, 'forget']);

/*
|--------------------------------------------------------------------------
| Admin — panel
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Artikel (semua user login)
    Route::get('article', [AdminArticleController::class, 'index']);
    Route::get('article/add', [AdminArticleController::class, 'create']);
    Route::post('article/add', [AdminArticleController::class, 'store']);
    Route::get('article/edit/{id}', [AdminArticleController::class, 'edit']);
    Route::post('article/edit/{id}', [AdminArticleController::class, 'update']);
    Route::get('article/delete/{id}', [AdminArticleController::class, 'destroy']);
    Route::get('article/publish/{id}', [AdminArticleController::class, 'publish']);
    Route::get('article/draft/{id}', [AdminArticleController::class, 'draft']);
    Route::get('article/preview/{uri}', [AdminArticleController::class, 'preview']);
    Route::get('article/comment/{id}', [AdminArticleController::class, 'comment']);
    Route::get('article/publish-komentar/{id}', [AdminArticleController::class, 'publishComment']);
    Route::get('article/unpublish-komentar/{id}', [AdminArticleController::class, 'unpublishComment']);
    Route::get('article/delete-komentar/{id}', [AdminArticleController::class, 'deleteComment']);
    Route::get('article/referensi/{id}', [AdminArticleController::class, 'referensi']);
    Route::post('article/referensi-add/{id}', [AdminArticleController::class, 'referensiAdd']);
    Route::get('article/referensi-delete/{id}', [AdminArticleController::class, 'referensiDelete']);

    // Data Master + Setting + Konten lain (khusus level admin)
    Route::middleware('admin.level')->group(function () {
        Route::get('label', [LabelController::class, 'index']);
        Route::get('label/add', [LabelController::class, 'create']);
        Route::post('label/add', [LabelController::class, 'store']);
        Route::get('label/edit/{id}', [LabelController::class, 'edit']);
        Route::post('label/edit/{id}', [LabelController::class, 'update']);
        Route::get('label/delete/{id}', [LabelController::class, 'destroy']);

        Route::get('category', [CategoryController::class, 'index']);
        Route::get('category/add', [CategoryController::class, 'create']);
        Route::post('category/add', [CategoryController::class, 'store']);
        Route::get('category/edit/{id}', [CategoryController::class, 'edit']);
        Route::post('category/edit/{id}', [CategoryController::class, 'update']);
        Route::get('category/delete/{id}', [CategoryController::class, 'destroy']);

        Route::get('sub-category', [SubCategoryController::class, 'index']);
        Route::get('sub-category/add', [SubCategoryController::class, 'create']);
        Route::post('sub-category/add', [SubCategoryController::class, 'store']);
        Route::get('sub-category/edit/{id}', [SubCategoryController::class, 'edit']);
        Route::post('sub-category/edit/{id}', [SubCategoryController::class, 'update']);
        Route::get('sub-category/delete/{id}', [SubCategoryController::class, 'destroy']);

        Route::get('user', [UserController::class, 'index']);
        Route::get('user/add', [UserController::class, 'create']);
        Route::post('user/add', [UserController::class, 'store']);
        Route::get('user/edit/{id}', [UserController::class, 'edit']);
        Route::post('user/edit/{id}', [UserController::class, 'update']);
        Route::get('user/delete/{id}', [UserController::class, 'destroy']);

        Route::get('settings', [SettingsController::class, 'index']);
        Route::get('settings/add', [SettingsController::class, 'create']);
        Route::post('settings/add', [SettingsController::class, 'store']);
        Route::get('settings/edit/{id}', [SettingsController::class, 'edit']);
        Route::post('settings/edit/{id}', [SettingsController::class, 'update']);
        Route::get('settings/delete/{id}', [SettingsController::class, 'destroy']);

        Route::get('sosialmedia', [SosialmediaController::class, 'index']);
        Route::get('sosialmedia/add', [SosialmediaController::class, 'create']);
        Route::post('sosialmedia/add', [SosialmediaController::class, 'store']);
        Route::get('sosialmedia/edit/{id}', [SosialmediaController::class, 'edit']);
        Route::post('sosialmedia/edit/{id}', [SosialmediaController::class, 'update']);
        Route::get('sosialmedia/delete/{id}', [SosialmediaController::class, 'destroy']);
        Route::post('sosialmedia/updatestatus', [SosialmediaController::class, 'updateStatus']);

        Route::get('settingemail/edit', [SettingemailController::class, 'edit']);
        Route::post('settingemail/edit', [SettingemailController::class, 'update']);
        Route::get('settingemail', [SettingemailController::class, 'index']);

        Route::get('popup', [PopupController::class, 'index']);
        Route::post('popup', [PopupController::class, 'update']);

        Route::match(['get', 'post'], 'settingscategory', [SettingscategoryController::class, 'index']);

        Route::get('menu', [MenuController::class, 'index']);
        Route::get('menu/add', [MenuController::class, 'create']);
        Route::post('menu/add', [MenuController::class, 'store']);
        Route::get('menu/edit/{id}', [MenuController::class, 'edit']);
        Route::post('menu/edit/{id}', [MenuController::class, 'update']);
        Route::get('menu/delete/{id}', [MenuController::class, 'destroy']);
        Route::post('menu/getdatamenu', [MenuController::class, 'getdatamenu']);
        Route::post('menu/getartikel', [MenuController::class, 'getartikel']);

        Route::get('pilihan', [PilihanController::class, 'index']);
        Route::get('pilihan/add', [PilihanController::class, 'create']);
        Route::post('pilihan/add', [PilihanController::class, 'store']);
        Route::get('pilihan/edit/{id}', [PilihanController::class, 'edit']);
        Route::post('pilihan/edit/{id}', [PilihanController::class, 'update']);
        Route::get('pilihan/delete/{id}', [PilihanController::class, 'destroy']);
        Route::post('pilihan/getartikel', [PilihanController::class, 'getartikel']);
        Route::post('pilihan/getsub', [PilihanController::class, 'getsub']);

        // Slider / sorotan beranda (tbl_pilihan posisi "top")
        Route::get('slider', [SliderController::class, 'index']);
        Route::get('slider/add', [SliderController::class, 'create']);
        Route::post('slider/add', [SliderController::class, 'store']);
        Route::get('slider/edit/{id}', [SliderController::class, 'edit']);
        Route::post('slider/edit/{id}', [SliderController::class, 'update']);
        Route::get('slider/delete/{id}', [SliderController::class, 'destroy']);
        Route::post('slider/urutan', [SliderController::class, 'urutan']);
        Route::get('slider/articles', [SliderController::class, 'articles']);

        Route::get('ads', [AdsController::class, 'index']);
        Route::get('ads/add', [AdsController::class, 'create']);
        Route::post('ads/add', [AdsController::class, 'store']);
        Route::get('ads/edit/{id}', [AdsController::class, 'edit']);
        Route::post('ads/edit/{id}', [AdsController::class, 'update']);
        Route::get('ads/delete/{id}', [AdsController::class, 'destroy']);

        Route::get('banner', [BannerController::class, 'index']);
        Route::get('banner/add', [BannerController::class, 'create']);
        Route::post('banner/add', [BannerController::class, 'store']);
        Route::get('banner/edit/{id}', [BannerController::class, 'edit']);
        Route::post('banner/edit/{id}', [BannerController::class, 'update']);
        Route::get('banner/delete/{id}', [BannerController::class, 'destroy']);

        Route::get('quotes', [QuotesController::class, 'index']);
        Route::get('quotes/add', [QuotesController::class, 'create']);
        Route::post('quotes/add', [QuotesController::class, 'store']);
        Route::get('quotes/edit/{id}', [QuotesController::class, 'edit']);
        Route::post('quotes/edit/{id}', [QuotesController::class, 'update']);
        Route::get('quotes/delete/{id}', [QuotesController::class, 'destroy']);

        Route::get('pertanyaan', [AdminPertanyaanController::class, 'index']);
        Route::get('pertanyaan/data', [AdminPertanyaanController::class, 'data']);
        Route::match(['get', 'post'], 'pertanyaan/jawab/{id}', [AdminPertanyaanController::class, 'jawab']);
        Route::get('pertanyaan/delete/{id}', [AdminPertanyaanController::class, 'destroy']);
        Route::post('pertanyaan/updatestatus', [AdminPertanyaanController::class, 'updateStatus']);

        Route::get('komentar', [KomentarController::class, 'index']);
        Route::get('komentar/data', [KomentarController::class, 'data']);
        Route::match(['get', 'post'], 'komentar/reply/{id}', [KomentarController::class, 'reply']);
        Route::get('komentar/delete/{id}', [KomentarController::class, 'destroy']);
        Route::get('komentar/publish/{id}', [KomentarController::class, 'publish']);
        Route::get('komentar/unpublish/{id}', [KomentarController::class, 'unpublish']);
        Route::post('komentar/delete-multiple', [KomentarController::class, 'deleteMultiple']);

        Route::get('contact', [AdminContactController::class, 'index']);
    });
});
