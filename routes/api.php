<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Admin\ReportAdminController;
use App\Http\Controllers\Api\AdminLoginApiController;

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

Route::post('/admin/login', [AdminLoginApiController::class, 'login']);

Route::get('/reports', [ReportController::class, 'index']);

Route::get('/reports/{id}', [ReportController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reports', [ReportController::class, 'store']); // buat report
});

Route::middleware('auth:api')->group(function () {
    Route::get('/admin/reports/chart-data', [ReportAdminController::class, 'getChartData']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/admin/users', [UserController::class, 'getUsers']);
});

// Untuk API routes
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/paginated', [UserController::class, 'indexPaginated']);
Route::get('/users/secure', [UserController::class, 'indexSecure']);
Route::get('/users/by-role', [UserController::class, 'indexByRole']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
