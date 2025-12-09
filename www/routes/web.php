<?php

use App\Http\Controllers\GoalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionAttachmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('accounts', AccountController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('goals', GoalController::class);

    Route::get('/datatable/accounts', [AccountController::class, 'datatable'])->name('accounts.datatable');
    Route::get('/datatable/categories', [CategoryController::class, 'datatable'])->name('categories.datatable');
    Route::get('/datatable/transactions', [TransactionController::class, 'datatable'])->name('transactions.datatable');

    Route::post('/transactions/{transaction}/attachments', [TransactionAttachmentController::class, 'store'])
        ->name('transactions.attachments.store');
    Route::get('/attachments/{attachment}/download', [TransactionAttachmentController::class, 'download'])
        ->name('attachments.download');
    Route::delete('/attachments/{attachment}', [TransactionAttachmentController::class, 'destroy'])
        ->name('attachments.destroy');

    Route::get('/reports/transactions', [ReportController::class,'transactionsForm'])->name('reports.transactions');
    Route::get('/reports/transactions/excel', [ReportController::class,'transactionsExcel'])->name('reports.transactions.excel');
    Route::get('/reports/transactions/pdf', [ReportController::class,'transactionsPdf'])->name('reports.transactions.pdf');

});
