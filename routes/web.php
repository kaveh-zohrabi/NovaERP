<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');

    Route::resource('companies', CompanyController::class);
    Route::post('/companies/{company}/restore', [CompanyController::class, 'restore'])->name('companies.restore');
    Route::delete('/companies/{company}/force-delete', [CompanyController::class, 'forceDelete'])->name('companies.force-delete');
    Route::patch('/companies/{company}/activate', [CompanyController::class, 'activate'])->name('companies.activate');
    Route::patch('/companies/{company}/deactivate', [CompanyController::class, 'deactivate'])->name('companies.deactivate');
});

require __DIR__.'/auth.php';
