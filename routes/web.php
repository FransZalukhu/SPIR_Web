<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportAdminController;
use App\Http\Controllers\Admin\LoginController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout')->middleware('auth');

Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('admin.login')->with('error', 'You are not logged in. Please login to access the dashboard.');
    }
    return view('dashboard');
})->name('dashboard')->middleware('auth');

Route::get('/reports', [ReportAdminController::class, 'showReportPage'])->name('admin.reports')->middleware('auth');

Route::get('/admin/reports', [ReportAdminController::class, 'index'])->middleware('auth');

Route::put('/admin/reports/{id}/status', [ReportAdminController::class, 'updateStatus'])->middleware('auth');

Route::get('/users', function () {
    return view('user');
})->name('user')->middleware(['auth']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
