<?php



use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AffiliatePayoutController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\icons\Boxicons;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\tables\Basic as TablesBasic;
use App\Http\Controllers\User\CampaignController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DomainController;




use App\Http\Controllers\User\FlutterwaveController;
use App\Http\Controllers\User\InvoiceController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\ProfileController;


use App\Http\Controllers\User\SchoolController;
use App\Http\Controllers\User\SchoolSubscriptionController;
use App\Http\Controllers\User\TicketController;
use App\Http\Controllers\User\UserDomainController;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;





















// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Password reset
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Email verification
Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->middleware(['auth'])->name('verification.resend');

Broadcast::routes(['middleware' => ['auth']]);


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('role:user')->name('dashboard');

    Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate.dashboard');
    Route::post('/affiliate/request-payout', [AffiliateController::class, 'requestPayout'])->name('affiliate.payout.request');
    Route::put('/user/update-bank', [AffiliateController::class, 'updateBank'])->name('update.bank');
    Route::get('/affiliate/export', [AffiliateController::class, 'exportCsv'])->name('affiliate.export');
    Route::post('/resolve-account', [AffiliateController::class, 'resolveAccount'])->name('paystack.resolve.account');
    Route::get('/settings', [AffiliateController::class, 'bankSettings'])->name('user.settings');

    Route::post('/affiliate/payouts', [AffiliatePayoutController::class, 'store'])
    ->name('affiliate.payouts.store');

    Route::prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::post('campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::put('campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
    Route::post('campaigns/{campaign}/regenerate-code', [CampaignController::class, 'regenerateCode'])->name('campaigns.regenerate');
});



    Route::get('/admin/dashboard', fn () => view('admin.dashboard'))
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

// Authenticated user routes with role:user
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {

    // Profile
    Route::get('/profile', function () {
        return view('user.profile');
    })->name('profile');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

    // Domain Management
    Route::get('/domains', [UserDomainController::class, 'index'])->name('domains.index');

    // Subdomain actions
    Route::get('domains/create', [DomainController::class, 'create'])->name('domains.create');
    Route::post('/domains/subdomain', [UserDomainController::class, 'storeSubdomain'])->name('domains.subdomain.store');
    Route::delete('/domains/subdomain/{id}', [UserDomainController::class, 'destroySubdomain'])->name('domains.subdomain.destroy');

    // Custom domain actions
    Route::post('/domains/custom', [UserDomainController::class, 'storeCustomDomain'])->name('domains.custom.store');
    Route::delete('/domains/custom/{id}', [UserDomainController::class, 'destroyCustomDomain'])->name('domains.custom.destroy');

     // School Management
    Route::prefix('schools')->name('schools.')->middleware(['auth', 'role:user'])->group(function () {
        Route::get('/', [SchoolController::class, 'index'])->name('index');
        Route::get('/create', [SchoolController::class, 'create'])->name('create');
        Route::post('/', [SchoolController::class, 'store'])->name('store');

        Route::get('/{school}/edit', [SchoolController::class, 'edit'])->name('edit');
        Route::put('/{school}', [SchoolController::class, 'update'])->name('update');
        Route::delete('/{school}', [SchoolController::class, 'destroy'])->name('destroy');

        // Custom domain update (optional)
        Route::post('/{school}/custom-domain', [SchoolController::class, 'updateCustomDomain'])->name('custom-domain');
        Route::get('/{school}/current-subscription', [SchoolController::class, 'getCurrentSubscription'])->name('current-subscription');
        // Route::post('/subscribe', [SchoolController::class, 'subscribe'])->name('subscribe');
        Route::post('/{school}/subscribe', [SchoolSubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::get('/{school}/subscriptions', [SchoolSubscriptionController::class, 'history'])->name('subscriptions.history');

        Route::post('/{school}/renew', [SchoolSubscriptionController::class, 'renew'])->name('renew');
        Route::post('/{school}/upgrade', [SchoolSubscriptionController::class, 'upgrade'])->name('upgrade');

        Route::post('/{plan}/price', [SchoolSubscriptionController::class, 'getPrice']);
        Route::post('/{school}/cancel', [SchoolSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    });

    Route::get('/subscriptions', [SchoolSubscriptionController::class, 'index'])->name('subscriptions.index');

    // Ticket
    Route::middleware(['auth'])->prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('{ticket}', [TicketController::class, 'show'])->name('show');
        Route::post('{ticket}/reply', [TicketController::class, 'reply'])->name('reply');

    });

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::post('/invoices/{invoice}/email', [InvoiceController::class, 'email'])->name('invoices.email');
    Route::get('/invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/{invoice}/manual-payment', [InvoiceController::class, 'submitManualPayment'])
    ->name('invoices.manual.submit');
    Route::get('/flutterwave/callback', [FlutterwaveController::class, 'handleCallback'])->name('flutterwave.callback');




    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
    ->name('notifications.read');
    Route::patch('/notifications/mark-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');


});

 // Paystack callback (not protected by auth in case user exits browser)
Route::get('/paystack/callback', [InvoiceController::class, 'handlePaystackCallback'])->name('paystack.callback');


// Main Page Route
// Route::get('user/dashboard', [Analytics::class, 'index'])->name('dashboard-analytics');



require __DIR__ . '/admin.php';
