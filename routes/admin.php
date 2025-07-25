<?php

use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DomainController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\SubscriptionAuditController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;















Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Plans
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/create', [PlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');
    Route::patch('/plans/{plan}/toggle-status', [PlanController::class, 'toggleStatus'])->name('plans.toggleStatus');

    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::patch('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::patch('/subscriptions/{subscription}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::post('/subscriptions/{subscription}/resend-invoice', [SubscriptionController::class, 'resendInvoice'])->name('subscriptions.resendInvoice');
    Route::get('/subscriptions/export', [SubscriptionController::class, 'export'])->name('subscriptions.export');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{invoice}/resend', [InvoiceController::class, 'resendNotification'])->name('invoices.resend');
    Route::patch('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.markPaid');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('/invoices/{invoice}/email', [InvoiceController::class, 'email'])->name('invoices.email');



    // Payment Gateway
    Route::resource('gateways', PaymentGatewayController::class)->except(['create', 'edit', 'show']);
    Route::patch('gateways/{gateway}/toggle-status', [PaymentGatewayController::class, 'toggleStatus'])->name('gateways.toggleStatus');

    // Tickets
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('{ticket}', [TicketController::class, 'show'])->name('show');
        Route::get('{ticket}/edit', [TicketController::class, 'edit'])->name('edit');
        Route::put('{ticket}', [TicketController::class, 'update'])->name('update');
        Route::delete('{ticket}', [TicketController::class, 'destroy'])->name('destroy');

        Route::post('{ticket}/reply', [TicketController::class, 'reply'])->name('reply');
        Route::get('{ticket}/messages', [TicketController::class, 'messages'])->name('messages');
        Route::post('{ticket}/status', [TicketController::class, 'updateStatus'])->name('status');
        Route::post('{ticket}/assign', [TicketController::class, 'assign'])->name('assign');
    });

    // Schools
    Route::prefix('schools')->name('schools.')->group(function () {
        Route::get('/', [SchoolController::class, 'index'])->name('index');
        Route::post('/', [SchoolController::class, 'store'])->name('store');
        Route::get('/{school}', [SchoolController::class, 'show'])->name('show');
        Route::put('/{school}', [SchoolController::class, 'update'])->name('update');
        Route::delete('/{school}', [SchoolController::class, 'destroy'])->name('destroy');
    });

    Route::get('/domains', [DomainController::class, 'index'])->name('domains.index');
    Route::delete('/subdomains/{subdomain}', [DomainController::class, 'destroySubdomain'])->name('subdomains.destroy');
    Route::delete('/custom-domains/{customDomain}', [DomainController::class, 'destroyCustom'])->name('custom-domains.destroy');

    Route::get('/subscription-audits', [SubscriptionAuditController::class, 'index'])->name('subscription-audits.index');

    // Settings
    Route::get('settings', [SystemSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SystemSettingController::class, 'update'])->name('settings.update');

    // Coupon
    Route::resource('admin/coupons', CouponController::class);

    // Notifications
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/send', [AdminNotificationController::class, 'send'])->name('notifications.send');
    Route::post('/notifications/bulk', [AdminNotificationController::class, 'bulk'])
        ->name('notifications.bulk');

    // Profile
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

});
