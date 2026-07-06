<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
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

    Route::resource('branches', BranchController::class);
    Route::post('/branches/{branch}/restore', [BranchController::class, 'restore'])->name('branches.restore');
    Route::patch('/branches/{branch}/activate', [BranchController::class, 'activate'])->name('branches.activate');
    Route::patch('/branches/{branch}/deactivate', [BranchController::class, 'deactivate'])->name('branches.deactivate');

    Route::resource('departments', DepartmentController::class);
    Route::post('/departments/{department}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
    Route::patch('/departments/{department}/activate', [DepartmentController::class, 'activate'])->name('departments.activate');
    Route::patch('/departments/{department}/deactivate', [DepartmentController::class, 'deactivate'])->name('departments.deactivate');

    Route::resource('positions', PositionController::class);
    Route::post('/positions/{position}/restore', [PositionController::class, 'restore'])->name('positions.restore');
    Route::patch('/positions/{position}/activate', [PositionController::class, 'activate'])->name('positions.activate');
    Route::patch('/positions/{position}/deactivate', [PositionController::class, 'deactivate'])->name('positions.deactivate');

    Route::resource('employees', EmployeeController::class);
    Route::post('/employees/{employee}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
});

require __DIR__.'/auth.php';
