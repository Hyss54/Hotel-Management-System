<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FrontDeskController;
use App\Http\Controllers\HousekeepingTaskController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\KitchenOrderController;
use App\Http\Controllers\LaundryOrderController;
use App\Http\Controllers\BarOrderController;
use App\Http\Controllers\BarItemController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rooms & Accommodation
    Route::resource('room-types', RoomTypeController::class);
    Route::resource('rooms', RoomController::class);
    Route::patch('rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.updateStatus');

    // Customers & Organizations
    Route::resource('customers', CustomerController::class);
    Route::resource('organizations', OrganizationController::class);
    Route::get('organizations/{organization}/receipt', [OrganizationController::class, 'receipt'])->name('organizations.receipt');

    // Bookings
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{booking}/check-in', [BookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::post('bookings/{booking}/check-out', [BookingController::class, 'checkOut'])->name('bookings.check-out');

    // Kitchen
    Route::resource('kitchen', KitchenOrderController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('kitchen/{kitchen}/status', [KitchenOrderController::class, 'updateStatus'])->name('kitchen.updateStatus');

    // Laundry
    Route::resource('laundry', LaundryOrderController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('laundry/{laundry}/status', [LaundryOrderController::class, 'updateStatus'])->name('laundry.updateStatus');

    // Bar
    Route::resource('bar-items', BarItemController::class);
    Route::get('api/bar-items/{barItem}', [BarItemController::class, 'details'])->name('bar-items.details');
    Route::resource('bar', BarOrderController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('bar/{bar}/status', [BarOrderController::class, 'updateStatus'])->name('bar.updateStatus');

    // Housekeeping
    Route::resource('housekeeping', HousekeepingTaskController::class);
    Route::patch('housekeeping/{housekeeping}/status', [HousekeepingTaskController::class, 'updateStatus'])->name('housekeeping.updateStatus');

    // Front Desk Portal & Incidents
    Route::get('/frontdesk', [FrontDeskController::class, 'index'])->name('frontdesk.index');
    Route::get('/frontdesk/search', [FrontDeskController::class, 'search'])->name('frontdesk.search');
    Route::resource('incidents', IncidentController::class);
    Route::patch('incidents/{incident}/status', [IncidentController::class, 'updateStatus'])->name('incidents.updateStatus');

    // Billing & Payments
    Route::resource('bills', BillController::class)->except(['edit', 'update', 'destroy']);
    Route::post('bills/{bill}/payment', [BillController::class, 'recordPayment'])->name('bills.payment');
    Route::get('bills/{bill}/invoice', [BillController::class, 'invoice'])->name('bills.invoice');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('daily-sales', [ReportController::class, 'dailySales'])->name('daily-sales');
        Route::get('accommodation', [ReportController::class, 'accommodation'])->name('accommodation');
        Route::get('department', [ReportController::class, 'department'])->name('department');
        Route::get('customers', [ReportController::class, 'customers'])->name('customers');
    });

    // Administration
    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::resource('users', UserController::class);

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Audit Trail
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::delete('/audit-logs/clear', [AuditLogController::class, 'clear'])->name('audit-logs.clear');
});

require __DIR__ . '/auth.php';
