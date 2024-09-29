<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\Passwords\Confirm;
use App\Http\Livewire\Auth\Passwords\Email;
use App\Http\Livewire\Auth\Passwords\Reset;
use App\Http\Livewire\Auth\Register;
use App\Http\Livewire\Auth\Verify;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Reporting;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockManagementController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\IncidentReportingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProductViewController;
use App\Http\Controllers\ProductSellController;
use App\Http\Controllers\ProductAddController;
use App\Http\Controllers\ProductEditController;
use App\Http\Controllers\NewOrdersController;
use App\Http\Controllers\ProductBuyController;
use App\Http\Controllers\OwnershipController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\MyNetworkController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\UplineController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductKAController;
use App\Http\Controllers\DigitalGoldController;
use App\Http\Controllers\PhysicalGoldController;
use App\Http\Controllers\PurchaseHistoryController;
use App\Http\Controllers\BankInformationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AllNewsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SnapAPI;
use App\Http\Controllers\ToyyibpayController;
use App\Http\Controllers\UsersController;
use App\Http\Livewire\Auth\RegisterAgent;
use App\Http\Livewire\Auth\VerifyOtp;
use App\Http\Livewire\Page\Admin\Newsletter;
use App\Http\Livewire\Page\Admin\Announcement\ListAnnouncement;
use App\Http\Livewire\Page\Admin\Announcement\CreateAnnouncement;
use App\Http\Livewire\Page\Admin\UserManagement;
use App\Http\Livewire\Page\Reporting\ListReport;
use App\Http\Livewire\Page\Admin\Promo\PromoList;
use App\Http\Livewire\Page\Admin\Promo\PromoAdd;
use App\Http\Livewire\Page\Admin\UserDetails;
use App\Http\Livewire\Page\Admin\UserGoldProfile\UserDetailedInfo;
use App\Http\Livewire\Page\PhysicalGold\ArrahnuPawn;
use App\Http\Livewire\Page\PhysicalGold\ArrahnuPawnCheckout;
use App\Http\Livewire\Page\PhysicalGold\GoldMinting;
use App\Http\Livewire\Page\PhysicalGold\GoldMintingCheckout;
use App\Http\Livewire\Page\PhysicalGold\OutrightCheckoutSG;
use App\Http\Livewire\Page\PhysicalGold\OutrightSpotGold;
use Illuminate\Support\Facades\Artisan;

use App\Http\Livewire\Page\Reporting\AgentReport;
use App\Http\Livewire\Page\Reporting\CommissionReport;
use App\Http\Livewire\Page\Reporting\ExitReport;
use App\Http\Livewire\Page\Reporting\GoldReport;
use App\Http\Livewire\Page\Reporting\UserReport;

use App\Http\Livewire\Page\Lelongan\Lelongan;
use App\Http\Livewire\Page\PurchaseHistory\PurchaseHistory;

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return "All Cache is cleared";
});

Route::middleware('guest')->group(function () {
    /** Landing Page */
    // Route::view('/', 'welcome')->name('leadingpage');
    // Route::view('/mengenai-kami', 'about')->name('aboutUs');

    // Bypass landing page
    // Route::get('/', Login::class)->name('login');
    Route::post('toyyibpay-callback', [ToyyibpayController::class, 'callback'])->name('toyyibpay-callback');
    Route::post('toyyibpay-callback-mint', [ToyyibpayController::class, 'callbackMint'])->name('toyyibpay-callback-mint');
    Route::post('toyyibpay-callbackConv', [ToyyibpayController::class, 'callbackConv'])->name('toyyibpay-callbackConv');
    Route::post('pay2', [SnapAPI::class, 'callback'])->name('pay2');



    // ** Authentication *
    Route::get('login', Login::class)->name('login');
    Route::get('register/{code}', Register::class)->name('register');
    Route::get('agentRegistration', RegisterAgent::class)->name('register.agent');
    Route::get('password/reset', Email::class)->name('password.request');
    Route::get('password/reset/{token}', Reset::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    /** Authentication */
    Route::get('verify-otp', VerifyOtp::class)->name('verifyOTP');
    Route::get('email/verify', Verify::class)->middleware('throttle:6,1')->name('verification.notice');
    Route::get('password/confirm', Confirm::class)->name('password.confirm');
    Route::get('email/verify/{id}/{hash}', EmailVerificationController::class)->middleware('signed')->name('verification.verify');
    Route::post('logout', LogoutController::class)->name('logout');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile')->middleware('verified.otp');
    Route::post('profile/ic_front/{id}', [ProfileController::class, 'deleteIcFront'])->name('profile.deleteIcFront');
    Route::post('profile/ic_back/{id}', [ProfileController::class, 'deleteIcBack'])->name('profile.deleteIcBack');
    Route::get('nomineePDF', [ProfileController::class, 'nomineePDF'])->name('nomineePDF');

    Route::middleware(['passScreen', 'verified.otp'])->group(function () {
        Route::get('home', [DashboardController::class, 'index'])->name('home');

        //-- KASIH GOLD --//
        Route::get('stock/management', [StockManagementController::class, 'index'])->name('stock-management');
        Route::get('stock/movement', [StockMovementController::class, 'index'])->name('stock-movement');
        Route::get('incident-reporting', [IncidentReportingController::class, 'index'])->name('incidentReporting');
        Route::get('tracking', [TrackingController::class, 'index'])->name('tracking');
        Route::get('product/detail', [ProductDetailController::class, 'index'])->name('product-detail');
        Route::get('product/buy', [ProductBuyController::class, 'index'])->name('product-buy');
        Route::get('product/view', [ProductViewController::class, 'index'])->name('product-view');
        Route::get('product/sell', [ProductSellController::class, 'index'])->name('product-sell');
        Route::get('product/sell-add', [ProductAddController::class, 'index'])->name('product-add');
        Route::get('product/sell-edit', [ProductEditController::class, 'index'])->name('product-edit');
        Route::get('new-orders', [NewOrdersController::class, 'index'])->name('new-orders');
        Route::get('tracking/ownership', [OwnershipController::class, 'index'])->name('ownership');
        Route::get('tracking/delivery', [DeliveryController::class, 'index'])->name('delivery');
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('order/my-order', [MyOrderController::class, 'index'])->name('my-order');
        Route::get('order/customer-order', [CustomerOrderController::class, 'index'])->name('customer-order');
        Route::get('shipment', [ShipmentController::class, 'index'])->name('shipment');
        Route::get('my-network', [MyNetworkController::class, 'index'])->name('my-network');
        Route::get('commission', [CommissionController::class, 'index'])->name('commission');
        Route::get('my-network/upline-detail', [UplineController::class, 'index'])->name('upline-detail');
        Route::get('my-network/downline-detail', [UplineController::class, 'downline'])->name('downline-detail');
        Route::get('setting', [settingController::class, 'setting'])->name('setting');

        Route::get('pay', [SnapAPI::class, 'index'])->name('pay');
        Route::get('snapBuy', [SnapAPI::class, 'snapBuy'])->name('snapBuy');
        Route::post('pay2', [SnapAPI::class, 'callback'])->name('pay2');

        Route::get('cart', [CartController::class, 'index'])->name('cart');
        Route::post('cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::get('bank-information', [BankInformationController::class, 'index'])->name('bank-information');


        //-- KASIH AP --//
        Route::get('pending-approval-kap-agent', [DashboardController::class, 'pendingApprovalAgent'])->name('pending-approval-kap-agent');
        Route::post('pending-approval-kap-agent/{id}', [DashboardController::class, 'delete'])->name('pending-approval-kap-agent-delete');
        Route::get('my-agent-kap', [DashboardController::class, 'myAgent'])->name('my-agent-kap');
        Route::get('pending-approval-kap', [DashboardController::class, 'pendingApproval'])->name('pending-approval-kap');
        Route::post('pending-approval-kap/{id}', [DashboardController::class, 'delete'])->name('pending-approval-kap-delete');
        Route::get('cashback', [DashboardController::class, 'cashback'])->name('cashback');
        Route::get('todays-transaction', [DashboardController::class, 'todaysTransaction'])->name('todays-transaction');

        Route::get('product/ka/sell', [ProductKAController::class, 'sell'])->name('product-ka-sell');
        Route::get('digital-gold', [DigitalGoldController::class, 'index'])->name('digital-gold');
        Route::get('digital-gold-details', [DigitalGoldController::class, 'details'])->name('digital-gold-details');
        Route::get('physical-gold', [physicalGoldController::class, 'index'])->name('physical-gold');
        Route::get('physical-gold-cart', [physicalGoldController::class, 'cart'])->name('physical-gold-cart');
        Route::get('physical-gold-confirmation', [physicalGoldController::class, 'confirm'])->name('physical-gold-confirmation');
        Route::get('outright-gold-cart', [physicalGoldController::class, 'ocart'])->name('outright-gold-cart');
        Route::get('bb-gold-cart', [physicalGoldController::class, 'bbcart'])->name('bb-gold-cart');
        Route::get('purchase-history/{userId}', PurchaseHistory::class)->name('purchase-history');

        Route::get('gold-minting', GoldMinting::class)->name('goldMinting');
        Route::get('gm-checkout', GoldMintingCheckout::class)->name('mintingCheckout');
        Route::get('outright-spot-gold', OutrightSpotGold::class)->name('outSpotGold');
        Route::get('outright-sg', OutrightCheckoutSG::class)->name('outCheckoutSG');
        Route::get('arrahnu-pawn', ArrahnuPawn::class)->name('arrahnuPawn');
        Route::get('arrahnu-pawn-checkout', ArrahnuPawnCheckout::class)->name('arrahnuPawnCheckout');



        Route::get('setting-kap', [settingController::class, 'settingKAP'])->name('setting-kap');
        Route::get('all-news', [AllNewsController::class, 'index'])->name('all-news');
        Route::get('lelongan', Lelongan::class)->name('lelongan');

        // -- Toyyib Pay -- //
        Route::get('toyyibpay-status-conv', [ToyyibpayController::class, 'paymentStatusConv'])->name('toyyibpay-status-conv');
        Route::get('toyyibpay-status-gMint', [ToyyibpayController::class, 'paymentStatusMint'])->name('toyyibpay-status-gMint');
        Route::get('toyyibpay-status', [ToyyibpayController::class, 'paymentStatusBuy'])->name('toyyibpay-status-buy');
    });

    Route::middleware('auth.admin')->group(function () {
        Route::get('admin/screening', [ScreeningController::class, 'index'])->name('admin.screening');
        Route::get('admin/incident-reporting', [IncidentReportingController::class, 'admin'])->name('admin.incidentReporting');
        Route::get('admin/suppliers', [SuppliersController::class, 'index'])->name('admin.suppliers');
        Route::get('admin/list-promotion', PromoList::class)->name('admin.list-promotion');
        Route::get('admin/add-promotion', PromoAdd::class)->name('admin.add-promotion');
        Route::get('withdrawal-request', [DashboardController::class, 'withdrawalRequest'])->name('withdrawal-request');
        Route::get('admin/exit-approval', [ProductKAController::class, 'exitApp'])->name('exit-approval');
        Route::get('admin/exit-approval-bb', [ProductKAController::class, 'exitAppBB'])->name('exit-approval-bb');
        Route::get('admin/exit-approval-outright', [ProductKAController::class, 'exitAppOutright'])->name('exit-approval-outright');
        Route::get('product/admin/product/sell', [ProductAddController::class, 'admin'])->name('admin.product-sell-hq');
        Route::get('product/admin/product/sell-add', [ProductAddController::class, 'adminAdd'])->name('admin.product-add-hq');
        Route::get('admin/newsletter', Newsletter::class)->name('admin.newsletter');
        Route::get('admin/list-announcements', ListAnnouncement::class)->name('admin.list-announcements');
        Route::get('admin/create-announcements', CreateAnnouncement::class)->name('admin.create-announcements');
        Route::post('admin/list-announcements/{id}', [AnnouncementController::class, 'delete'])->name('admin.list-announcements.delete');
        Route::get('admin/user-management', UserManagement::class)->name('admin.userManagement');
        Route::get('admin/user-details/{id}', UserDetails::class)->name('admin.user-details');
        Route::get('admin/user-detailed-info', UserDetailedInfo::class)->name('admin.user-detailed-info');

        Route::get('/report/show', [Reporting::class, 'index'])->name('show_report');
        Route::get('reporting', ListReport::class)->name('reporting');

        Route::get('reporting/agent-report', AgentReport::class)->name('reporting.agent-report');
        Route::get('reporting/commission-report', CommissionReport::class)->name('reporting.commission-report');
        Route::get('reporting/exit-report', ExitReport::class)->name('reporting.exit-report');
        Route::get('reporting/gold-report', GoldReport::class)->name('reporting.gold-report');
        Route::get('reporting/user-report', UserReport::class)->name('reporting.user-report');




        Route::get('reporting/summary-goldbar', [ReportingController::class, 'summaryGoldbar'])->name('summaryGoldbar');
        Route::get('reporting/summary-agent', [ReportingController::class, 'summaryAgent'])->name('summaryAgent');
        Route::get('reporting/summary-commission', [ReportingController::class, 'summaryCommission'])->name('summaryCommission');
        Route::get('reporting/user/buyOrNot', [ReportingController::class, 'userBuyOrNot'])->name('userBuyOrNot');
        Route::get('reporting/exit', [ReportingController::class, 'exitReporting'])->name('reporting.exit');
    });
});
