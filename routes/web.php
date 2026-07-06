<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
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
    Route::patch('/employees/{employee}/terminate', [EmployeeController::class, 'terminate'])->name('employees.terminate');
    Route::patch('/employees/{employee}/reactivate', [EmployeeController::class, 'reactivate'])->name('employees.reactivate');

    // Inventory
    Route::resource('products', ProductController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/{stock}', [StockController::class, 'show'])->name('stock.show');
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('/stock-movements/create', [StockMovementController::class, 'create'])->name('stock-movements.create');
    Route::post('/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');

    // Purchasing
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchasing-orders', PurchaseOrderController::class);
    Route::patch('/purchasing-orders/{purchasing_order}/approve', [PurchaseOrderController::class, 'approve'])->name('purchasing-orders.approve');
    Route::patch('/purchasing-orders/{purchasing_order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchasing-orders.cancel');
    Route::get('/purchasing-orders/{purchasing_order}/receive', [GoodsReceiptController::class, 'create'])->name('receipts.create');
    Route::post('/purchasing-orders/{purchasing_order}/receive', [GoodsReceiptController::class, 'store'])->name('receipts.store');

    // Sales
    Route::resource('customers', CustomerController::class);
    Route::resource('orders', SalesOrderController::class);
    Route::patch('/orders/{order}/confirm', [SalesOrderController::class, 'confirm'])->name('orders.confirm');
    Route::patch('/orders/{order}/cancel', [SalesOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/orders/{order}/invoice', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::patch('/invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])->name('invoices.paid');
    Route::patch('/invoices/{invoice}/cancel', [InvoiceController::class, 'markCancelled'])->name('invoices.cancel');

    // Accounting
    Route::resource('accounts', ChartOfAccountController::class);
    Route::resource('journal-entries', JournalEntryController::class);
    Route::patch('/journal-entries/{journal_entry}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post');
    Route::patch('/journal-entries/{journal_entry}/reverse', [JournalEntryController::class, 'reverse'])->name('journal-entries.reverse');
});

require __DIR__.'/auth.php';
