<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\BukuKasHarianController;
use App\Http\Controllers\InventoryController;

// --- ROUTE AUTENTIKASI ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- ROUTE YANG DIKUNCI (Harus Login) ---
Route::middleware(['auth'])->group(function () {

    // Halaman awal otomatis ke kasir
    Route::get('/', function () {
        return redirect()->route('pos.index');
    });

    // Dashboard & Kasir - Bisa diakses Admin dan Kasir
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kasir', [PosController::class, 'index'])->name('pos.index');
    Route::post('/kasir/checkout', [PosController::class, 'store'])->name('pos.store');
    Route::get('/kasir/struk/{id}', [PosController::class, 'struk'])->name('pos.struk');
    Route::resource('buku-kas-harian', BukuKasHarianController::class)->only(['index', 'store']);

    // --- KHUSUS ADMIN ---
    Route::middleware(['role:admin'])->group(function () {
        Route::put('/buku-kas-harian/{bukuKasHarian}', [BukuKasHarianController::class, 'update'])->name('buku-kas-harian.update');
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('items', ItemController::class);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/rekap', [ReportController::class, 'rekap'])->name('reports.rekap');
        Route::get('/reports/daily', [ReportController::class, 'dailyReport'])->name('reports.daily');
        Route::patch('/orders/{order}/cancel', [ReportController::class, 'cancel'])->name('orders.cancel');

        // Export PDF Routes
        Route::get('/reports/export-pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.exportSalesPdf');
        Route::get('/reports/daily/export-pdf', [ReportController::class, 'exportDailyPdf'])->name('reports.exportDailyPdf');
        Route::get('/reports/rekap/export-pdf', [ReportController::class, 'exportRekapPdf'])->name('reports.exportRekapPdf');
        Route::get('/pengeluaran/export-pdf', [PengeluaranController::class, 'exportPdf'])->name('pengeluaran.exportPdf');
        Route::get('/inventories/export-pdf', [InventoryController::class, 'exportPdf'])->name('inventories.exportPdf');

        Route::resource('pengeluaran', PengeluaranController::class);
        Route::resource('inventories', InventoryController::class);
    });
});