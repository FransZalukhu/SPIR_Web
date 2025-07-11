<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Admin\ReportAdminController;
use App\Http\Controllers\Api\AdminLoginApiController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);;
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

Route::post('/admin/login', [AdminLoginApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::patch('/user', [UserController::class, 'update']);
});

Route::get('/reports', [ReportController::class, 'index']);

Route::get('/reports/{id}', [ReportController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reports', [ReportController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/my-reports/{id}', [ReportController::class, 'destroyOwnReport']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-reports', [ReportController::class, 'myReports']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::patch('/admin/reports/{id}/status', [ReportAdminController::class, 'updateStatus']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/admin/reports/chart-data', [ReportAdminController::class, 'getChartData']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/admin/users', [UserController::class, 'getUsers']);
});

Route::get('/categories', [CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'me']);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/paginated', [UserController::class, 'indexPaginated']);
Route::get('/users/secure', [UserController::class, 'indexSecure']);
Route::get('/users/by-role', [UserController::class, 'indexByRole']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Komentar
Route::get('/reports/{report}/comments', [CommentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reports/{report}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});
