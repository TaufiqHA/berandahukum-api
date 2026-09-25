<?php

use App\Http\Controllers\Api\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\LabelController as AdminLabelController;
use App\Http\Controllers\Api\Admin\LayoutController as AdminLayoutController;
use App\Http\Controllers\Api\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Api\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Api\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Api\Admin\SubCategoryController as AdminSubCategoryController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TaxonomyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API publik untuk aplikasi mobile (Flutter)
|--------------------------------------------------------------------------
| Base: /api/v1. URL gambar dikembalikan relatif (uploads/...).
*/
Route::prefix('v1')->group(function () {
    Route::get('home', [HomeController::class, 'index']);

    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/{uri}/comments', [CommentController::class, 'index']);
    Route::get('articles/{uri}', [ArticleController::class, 'show']);

    Route::get('search', [SearchController::class, 'index']);

    Route::get('categories', [TaxonomyController::class, 'categories']);
    Route::get('categories/{uri}', [TaxonomyController::class, 'category']);
    Route::get('subcategories/{uri}', [TaxonomyController::class, 'subcategory']);
    Route::get('labels', [TaxonomyController::class, 'labels']);
    Route::get('labels/{uri}', [TaxonomyController::class, 'label']);

    Route::get('questions', [QuestionController::class, 'index']);
    Route::post('questions', [QuestionController::class, 'store']);

    Route::post('comments', [CommentController::class, 'store']);

    Route::get('settings', [SettingController::class, 'index']);
    Route::get('settings/{id}', [SettingController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| API admin (panel mobile) — autentikasi token
|--------------------------------------------------------------------------
| Login mengembalikan token; sertakan header "Authorization: Bearer <token>".
| Artikel: admin & penulis. Data master/moderasi/pengguna/setting: admin saja.
*/
Route::prefix('v1/admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware('auth.api')->group(function () {
        Route::get('me', [AdminAuthController::class, 'me']);
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::get('dashboard', [AdminDashboardController::class, 'index']);

        // Artikel — admin & penulis
        Route::get('options', [AdminArticleController::class, 'options']);
        Route::get('articles', [AdminArticleController::class, 'index']);
        Route::post('articles', [AdminArticleController::class, 'store']);
        Route::get('articles/{id}', [AdminArticleController::class, 'show'])->whereNumber('id');
        Route::post('articles/{id}', [AdminArticleController::class, 'update'])->whereNumber('id');
        Route::delete('articles/{id}', [AdminArticleController::class, 'destroy'])->whereNumber('id');
        Route::post('articles/{id}/publish', [AdminArticleController::class, 'publish'])->whereNumber('id');
        Route::post('articles/{id}/draft', [AdminArticleController::class, 'draft'])->whereNumber('id');
        Route::get('articles/{id}/comments', [AdminArticleController::class, 'comments'])->whereNumber('id');
        Route::get('articles/{id}/referensi', [AdminArticleController::class, 'referensi'])->whereNumber('id');
        Route::post('articles/{id}/referensi', [AdminArticleController::class, 'referensiStore'])->whereNumber('id');
        Route::delete('referensi/{id}', [AdminArticleController::class, 'referensiDestroy'])->whereNumber('id');

        // Khusus admin
        Route::middleware('api.admin')->group(function () {
            Route::get('comments', [AdminCommentController::class, 'index']);
            Route::post('comments/{id}/reply', [AdminCommentController::class, 'reply'])->whereNumber('id');
            Route::post('comments/{id}/publish', [AdminCommentController::class, 'publish'])->whereNumber('id');
            Route::post('comments/{id}/unpublish', [AdminCommentController::class, 'unpublish'])->whereNumber('id');
            Route::delete('comments/{id}', [AdminCommentController::class, 'destroy'])->whereNumber('id');

            Route::get('questions', [AdminQuestionController::class, 'index']);
            Route::post('questions/{id}/answer', [AdminQuestionController::class, 'answer'])->whereNumber('id');
            Route::delete('questions/{id}', [AdminQuestionController::class, 'destroy'])->whereNumber('id');

            Route::get('categories', [AdminCategoryController::class, 'index']);
            Route::post('categories', [AdminCategoryController::class, 'store']);
            Route::post('categories/{id}', [AdminCategoryController::class, 'update'])->whereNumber('id');
            Route::delete('categories/{id}', [AdminCategoryController::class, 'destroy'])->whereNumber('id');

            Route::get('sub-categories', [AdminSubCategoryController::class, 'index']);
            Route::post('sub-categories', [AdminSubCategoryController::class, 'store']);
            Route::post('sub-categories/{id}', [AdminSubCategoryController::class, 'update'])->whereNumber('id');
            Route::delete('sub-categories/{id}', [AdminSubCategoryController::class, 'destroy'])->whereNumber('id');

            // Slider / sorotan beranda (tbl_pilihan posisi "top")
            Route::get('slider', [AdminSliderController::class, 'index']);
            Route::get('slider/articles', [AdminSliderController::class, 'articles']);
            Route::post('slider/urutan', [AdminSliderController::class, 'urutan']);
            Route::post('slider', [AdminSliderController::class, 'store']);
            Route::post('slider/{id}', [AdminSliderController::class, 'update'])->whereNumber('id');
            Route::delete('slider/{id}', [AdminSliderController::class, 'destroy'])->whereNumber('id');

            Route::get('labels', [AdminLabelController::class, 'index']);
            Route::post('labels', [AdminLabelController::class, 'store']);
            Route::post('labels/{id}', [AdminLabelController::class, 'update'])->whereNumber('id');
            Route::delete('labels/{id}', [AdminLabelController::class, 'destroy'])->whereNumber('id');

            // Tata letak beranda
            Route::get('layout', [AdminLayoutController::class, 'index']);
            Route::post('layout', [AdminLayoutController::class, 'save']);

            Route::get('users', [AdminUserController::class, 'index']);
            Route::post('users', [AdminUserController::class, 'store']);
            Route::post('users/{id}', [AdminUserController::class, 'update'])->whereNumber('id');
            Route::delete('users/{id}', [AdminUserController::class, 'destroy'])->whereNumber('id');

            Route::get('settings', [AdminSettingsController::class, 'index']);
            Route::post('settings/sys', [AdminSettingsController::class, 'updateSys']);
            Route::post('settings', [AdminSettingsController::class, 'store']);
            Route::post('settings/{id}', [AdminSettingsController::class, 'update'])->whereNumber('id');
            Route::delete('settings/{id}', [AdminSettingsController::class, 'destroy'])->whereNumber('id');
        });
    });
});
