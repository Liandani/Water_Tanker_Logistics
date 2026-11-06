<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CustomerManagementController;
use App\Http\Controllers\Admin\DriverManagementController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\RatingController;
use App\Http\Controllers\Admin\SuratJalanController;
use App\Http\Controllers\Admin\TariffController;
use App\Http\Controllers\Admin\VehicleManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerOrderController;
use App\Http\Controllers\Driver\DriverDashboardController;
use App\Http\Controllers\Driver\DriverJobController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register/customer', [AuthController::class, 'showCustomerRegister'])->name('register.customer');
Route::post('/register/customer', [AuthController::class, 'customerRegister']);

Route::get('/register/driver', [AuthController::class, 'showDriverRegister'])->name('register.driver');
Route::post('/register/driver', [AuthController::class, 'driverRegister']);

Route::get('/register/admin', [AuthController::class, 'showAdminRegister'])->name('register.admin');
Route::post('/register/admin', [AuthController::class, 'adminRegister']);

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

Route::get('/driver/pending-verification', [AuthController::class, 'driverPending'])->name('driver.pending');

// Public API for live calculation
Route::post('/api/calculate-price', [CustomerOrderController::class, 'calculateApi'])->name('api.calculate');

/*
|--------------------------------------------------------------------------
| Authenticated Common Routes (Notifications)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');

    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [CustomerOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [CustomerOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/tracking', [CustomerOrderController::class, 'tracking'])->name('orders.tracking');
    Route::get('/orders/{order}/tracking-api', [CustomerOrderController::class, 'trackingLocationApi'])->name('orders.tracking.api');
    Route::get('/orders/{order}/invoice', [CustomerOrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/rate', [CustomerOrderController::class, 'rate'])->name('orders.rate');
});

/*
|--------------------------------------------------------------------------
| Driver Routes (Mobile-first)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('dashboard');
    Route::post('/toggle-availability', [DriverDashboardController::class, 'toggleAvailability'])->name('toggleAvailability');
    Route::get('/profile', [DriverDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [DriverDashboardController::class, 'updateProfile'])->name('profile.update');

    Route::get('/jobs', [DriverJobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{assignment}', [DriverJobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{assignment}/accept', [DriverJobController::class, 'accept'])->name('jobs.accept');
    Route::post('/jobs/{assignment}/reject', [DriverJobController::class, 'reject'])->name('jobs.reject');
    Route::post('/jobs/{assignment}/status', [DriverJobController::class, 'updateStatus'])->name('jobs.status');
    Route::get('/jobs/{assignment}/navigation', [DriverJobController::class, 'navigation'])->name('jobs.navigation');
    Route::post('/jobs/{assignment}/gps-update', [DriverJobController::class, 'updateGpsLocation'])->name('jobs.gps');
    Route::post('/jobs/{assignment}/complete', [DriverJobController::class, 'complete'])->name('jobs.complete');
    Route::post('/jobs/{assignment}/report-issue', [DriverJobController::class, 'reportIssue'])->name('jobs.reportIssue');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring-maps', [AdminDashboardController::class, 'monitoringMaps'])->name('monitoring.maps');
    Route::get('/monitoring-data', [AdminDashboardController::class, 'monitoringData'])->name('monitoring.data');

    // CRUD Customers
    Route::resource('customers', CustomerManagementController::class);

    // CRUD Drivers
    Route::resource('drivers', DriverManagementController::class);
    Route::post('/drivers/{driver}/verify', [DriverManagementController::class, 'verifyDriver'])->name('drivers.verify');

    // CRUD Vehicles
    Route::resource('vehicles', VehicleManagementController::class)->except(['show']);

    // Order Management
    Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/approve', [OrderManagementController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{order}/pending', [OrderManagementController::class, 'pending'])->name('orders.pending');
    Route::post('/orders/{order}/reject', [OrderManagementController::class, 'reject'])->name('orders.reject');
    Route::post('/orders/{order}/reassign', [OrderManagementController::class, 'reassign'])->name('orders.reassign');

    // Digital Surat Izin Jalan
    Route::get('/surat-jalan', [SuratJalanController::class, 'index'])->name('surat-jalan.index');
    Route::get('/surat-jalan/{document}', [SuratJalanController::class, 'show'])->name('surat-jalan.show');
    Route::get('/surat-jalan/{document}/pdf', [SuratJalanController::class, 'downloadPdf'])->name('surat-jalan.pdf');
    Route::get('/surat-jalan/{document}/print', [SuratJalanController::class, 'print'])->name('surat-jalan.print');

    // Financial
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    Route::post('/financial/expense', [FinancialController::class, 'storeExpense'])->name('financial.storeExpense');
    Route::delete('/financial/expense/{transaction}', [FinancialController::class, 'destroyExpense'])->name('financial.destroyExpense');
    Route::get('/financial/reports', [FinancialController::class, 'reports'])->name('financial.reports');
    Route::get('/financial/export-pdf', [FinancialController::class, 'exportPdf'])->name('financial.exportPdf');
    Route::get('/financial/export-excel', [FinancialController::class, 'exportExcel'])->name('financial.exportExcel');

    // Tariff Settings
    Route::get('/tariff', [TariffController::class, 'index'])->name('tariff.index');
    Route::put('/tariff', [TariffController::class, 'update'])->name('tariff.update');

    // Ratings & Reviews
    Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');

    // Activity Audit Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});
