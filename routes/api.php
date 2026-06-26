<?php

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PosterController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\VideoStatusController;
use Illuminate\Support\Facades\Route;

// SEO routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);
Route::get('/robots.txt', [SeoController::class, 'robots']);
Route::get('/structured-data/{slug}', [SeoController::class, 'structuredData']);

// Public routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/quotes', [QuoteController::class, 'index']);
Route::get('/quotes/{slug}', [QuoteController::class, 'show']);
Route::get('/ads', [AdController::class, 'publicIndex']);
Route::get('/sliders', [PublicContentController::class, 'sliders']);
Route::get('/posters', [PublicContentController::class, 'posters']);
Route::get('/video-statuses', [PublicContentController::class, 'videoStatuses']);
Route::get('/businesses', [PublicContentController::class, 'businesses']);
Route::get('/posts', [PublicContentController::class, 'posts']);
Route::get('/posts/{slug}', [PublicContentController::class, 'postShow']);
Route::post('/contact', [ContactController::class, 'store']);

// Admin auth
Route::post('/admin/login', [AuthController::class, 'login']);

// Protected admin routes
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/dashboard', [QuoteController::class, 'dashboard']);
    Route::get('/reports/daily', [ReportController::class, 'daily']);

    Route::get('/categories', [CategoryController::class, 'adminIndex']);
    Route::get('/categories/{id}', [CategoryController::class, 'adminShow']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::post('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    Route::get('/sub-categories', [SubCategoryController::class, 'index']);
    Route::get('/sub-categories/{id}', [SubCategoryController::class, 'show']);
    Route::post('/sub-categories', [SubCategoryController::class, 'store']);
    Route::put('/sub-categories/{id}', [SubCategoryController::class, 'update']);
    Route::delete('/sub-categories/{id}', [SubCategoryController::class, 'destroy']);

    Route::get('/posters', [PosterController::class, 'index']);
    Route::get('/posters/{id}', [PosterController::class, 'show']);
    Route::post('/posters', [PosterController::class, 'store']);
    Route::put('/posters/{id}', [PosterController::class, 'update']);
    Route::post('/posters/{id}', [PosterController::class, 'update']);
    Route::delete('/posters/{id}', [PosterController::class, 'destroy']);

    Route::get('/businesses', [BusinessController::class, 'index']);
    Route::get('/businesses/{id}', [BusinessController::class, 'show']);
    Route::post('/businesses', [BusinessController::class, 'store']);
    Route::put('/businesses/{id}', [BusinessController::class, 'update']);
    Route::post('/businesses/{id}', [BusinessController::class, 'update']);
    Route::delete('/businesses/{id}', [BusinessController::class, 'destroy']);

    Route::get('/sliders', [SliderController::class, 'index']);
    Route::get('/sliders/{id}', [SliderController::class, 'show']);
    Route::post('/sliders', [SliderController::class, 'store']);
    Route::put('/sliders/{id}', [SliderController::class, 'update']);
    Route::post('/sliders/{id}', [SliderController::class, 'update']);
    Route::delete('/sliders/{id}', [SliderController::class, 'destroy']);

    Route::get('/video-statuses', [VideoStatusController::class, 'index']);
    Route::get('/video-statuses/{id}', [VideoStatusController::class, 'show']);
    Route::post('/video-statuses', [VideoStatusController::class, 'store']);
    Route::put('/video-statuses/{id}', [VideoStatusController::class, 'update']);
    Route::post('/video-statuses/{id}', [VideoStatusController::class, 'update']);
    Route::delete('/video-statuses/{id}', [VideoStatusController::class, 'destroy']);

    Route::get('/ads', [AdController::class, 'index']);
    Route::get('/ads/{id}', [AdController::class, 'show']);
    Route::post('/ads', [AdController::class, 'store']);
    Route::put('/ads/{id}', [AdController::class, 'update']);
    Route::delete('/ads/{id}', [AdController::class, 'destroy']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::post('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    Route::get('/contacts', [ContactController::class, 'index']);
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::put('/contacts/{id}/read', [ContactController::class, 'markRead']);
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);

    Route::get('/quotes', [QuoteController::class, 'adminIndex']);
    Route::get('/quotes/{id}', [QuoteController::class, 'adminShow']);
    Route::post('/quotes', [QuoteController::class, 'store']);
    Route::put('/quotes/{id}', [QuoteController::class, 'update']);
    Route::delete('/quotes/{id}', [QuoteController::class, 'destroy']);

    Route::get('/users', [AdminUserController::class, 'index']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
});
