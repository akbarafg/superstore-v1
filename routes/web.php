<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Jobs\RunBackupJob;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailsController;
use App\Http\Controllers\ShoppingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShoppingDetailController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BackupController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/language/{language}', function($language) {
    Session::get('locale', $language);
    return redirect()->back();
})->name('language');


Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canResetPassword' => Route::has('password.request'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard
Route::resource('/dashboard', DashboardController::class);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Category
Route::resource('/categories', CategoryController::class);

// Supplier
Route::resource('/suppliers', SupplierController::class);

// Customer
Route::resource('/customers', CustomerController::class);

// Order
Route::resource('/orders', OrderController::class);

// Order details
Route::resource('order_details', OrderDetailsController::class);

// Shopping
Route::resource('shoppings', ShoppingController::class);

// Product 
Route::resource('products', ProductController::class);

// Shopping details
Route::resource('shopping_details', ShoppingDetailController::class);

// Employees
Route::resource('employees', EmployeeController::class);

// Expenses
Route::resource('expenses', ExpenseController::class);




Route::get('backup', [BackupController::class, 'index'])->name('backup.index');

Route::get('/export/database', [BackupController::class, 'backupDatabase']);
Route::get('/export/project', [BackupController::class, 'backupProjectFiles']);


require __DIR__.'/auth.php';
