<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ViolationController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('index');
});

Route::get('/admin/login', [UserController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [UserController::class, 'submitLogin'])->name('admin.login.submit');
Route::get('/admin/logout', [UserController::class, 'index'])->name('admin.logout');

//Tạo các route admin có middedleware là auth
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/xem-truc-tiep', [ViolationController::class, 'live'])->name('admin.violations.live');

    Route::get('/tai-len', [ViolationController::class, 'create'])->name('admin.violations.create');
});