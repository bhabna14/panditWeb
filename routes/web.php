<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\userController;
use App\Http\Controllers\User\FlowerUserBookingController;

use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\FlowerRegistrationController;
use App\Http\Controllers\User\FooterController;

use App\Http\Controllers\OtplessLoginController;

use App\Http\Controllers\Admin\PujaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TitleController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PodcastController;
use App\Http\Controllers\Admin\PodcastCreateController;
use App\Http\Controllers\Admin\PodcastScriptController;
use App\Http\Controllers\Admin\PodcastEditingController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\YoutubeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\FlowerRequestController;
use App\Http\Controllers\Admin\FlowerOrderController;
use App\Http\Controllers\Admin\LocalityController;
use App\Http\Controllers\Admin\PromonationController;
use App\Http\Controllers\Admin\PodcastReportController;
use App\Http\Controllers\Admin\PublishPodcastController;
use App\Http\Controllers\Admin\PodcastMediaController;
use App\Http\Controllers\Admin\PodcastSocialMediaController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\FlowerVendorController;
use App\Http\Controllers\Admin\FlowerPickupController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SubadminController;
use App\Http\Controllers\Admin\FollowUpController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\CustomizeProductController;
use App\Http\Controllers\Admin\FlowerDashboardController;
use App\Http\Controllers\Admin\PodcastPlanningController;
use App\Http\Controllers\Admin\MarketingVisitPlaceController;
use App\Http\Controllers\Admin\OfferDetailsController;
use App\Http\Controllers\Admin\FlowerCalendarController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\OfficeTransactionController;
use App\Http\Controllers\Admin\MonthWiseFlowerPriceController;
use App\Http\Controllers\Admin\Product\ProductSubscriptionController;
use App\Http\Controllers\Admin\ProductRequestController;

use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\UserCustomizeOrderController;
use App\Http\Controllers\NewUserOrderController;

use App\Http\Controllers\Pandit\AreaController;
use App\Http\Controllers\Pandit\BankController;
use App\Http\Controllers\Pandit\SkillController;
use App\Http\Controllers\Pandit\CareerController;
use App\Http\Controllers\Pandit\PanditController;
use App\Http\Controllers\Pandit\AddressController;
use App\Http\Controllers\Pandit\ProfileController;
use App\Http\Controllers\Pandit\PoojaListController;
use App\Http\Controllers\Pandit\PoojaDetailsController;
use App\Http\Controllers\Pandit\PanditOtpController;
use App\Http\Controllers\Pandit\PoojaStatusController;
use App\Http\Controllers\Pandit\PoojaHistoryController;

use App\Http\Controllers\Reports\FlowerReportsController;

use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\Superadmin\SuperAdminController;

use App\Http\Controllers\Refer\ReferController;

use Twilio\Rest\Client;


Route::fallback(function () {
    abort(404);
});

Route::controller(FooterController::class)->group(function() {
    Route::get('/contact-us','contactUs')->name('user.contactUs');
    Route::get('/about-us','aboutUs')->name('user.aboutUs');
    Route::get('/our-story','ourStory')->name('user.ourStory');
    Route::get('/crores','crores')->name('user.crores');
    Route::get('/term-condition','termsAndConditions')->name('user.termsAndConditions');
    Route::get('/privacy-data','privacyPolicy')->name('user.privacyData');
    Route::get('/cancel-return','cancelReturn')->name('user.cancelReturn');
    Route::get('/business-enrolledment','businessEnrollment')->name('user.businessEnrollment');
    Route::get('/religious-service-provider','religiousProvider')->name('user.religiousProvider');

});

Route::get('/otplogin', [OtplessLoginController::class, 'otplogin'])->name('otplogin');
Route::post('/send-otp-user', [OtplessLoginController::class, 'sendOtp']);
Route::post('/verify-otp-user', [OtplessLoginController::class, 'verifyOtp']);

Route::get('/admin/switcherpage', function () {
    return view('admin.switcherpage');
});

## flowerregistration
Route::controller(FlowerRegistrationController::class)->group(function() {
    Route::get('/flower-registration', 'flowerregistration')->name('flowerregistration');
    Route::post('/send-otp-flower', 'sendOtpflower');
    Route::post('/verify-otp-flower', 'verifyOtpflower');

    Route::get('/flower/address', 'floweruseraddress')->name('floweruseraddress');
    Route::post('/flowersaveaddress', 'flowersaveaddress')->name('flowersaveaddress');
    
});

Route::group(['middleware' => ['auth:users']], function () {
    Route::controller(FlowerRegistrationController::class)->group(function() {
    });
});

## user login
Route::controller(userController::class)->group(function() {
    Route::get('/register', 'userregister')->name('user-register');
    Route::post('store', 'store')->name('store');
    Route::get('/', 'userindex')->name('userindex');

    Route::get('/pandit-list', 'panditlist')->name('panditlist');
    Route::get('/pandits/{pooja_id}/{pandit_id}',  'list')->name('pandit.list');
    Route::get('/pooja-list', 'poojalist')->name('poojalist');
    Route::get('/pooja/{slug}', 'poojadetails')->name('pooja.show');
    Route::get('/pooja/{poojaSlug}/{panditSlug}', 'panditDetails')->name('pandit.details');
    Route::get('/our-pandit/{slug}', 'singlepanditDetails')->name('pandit.show');
    Route::get('/book-now/{panditSlug}/{poojaSlug}/{poojaFee}', 'bookNow')->name('book.now');
    Route::post('/booking/confirm',  'confirmBooking')->name('booking.confirm');
    
    // Route::get('/booking/success',  'bookingSuccess')->name('booking.success');
    // Route::get('/pandit-details', 'panditetails')->name('panditdetails');
    // Route::get('/book-now', 'booknow')->name('booknow');
    Route::get('/contact', 'contact')->name('contact');
   
    Route::get('/userlogin', 'userlogin')->name('userlogin');
    Route::post('/save-userlogin', 'storeloginData')->name('user.login');
    Route::get('/userotp','showOtpForm')->name('user.otp');
    Route::post('/check-otp', 'checkuserotp')->name('check.userotp');
    Route::post('user/authenticate', 'userauthenticate')->name('userauthenticate');
    Route::post('user/logout', 'userlogout')->name('userlogout');
    // Route::get('/search',  'search')->name('pandit.search');
    Route::get('/ajax-search',  'ajaxSearch')->name('pandit.ajaxSearch');
    Route::get('/ajax-search-pooja', 'ajaxSearchPooja')->name('pooja.ajaxSearchPooja');

    // Route::get('/search-pooja','searchPooja')->name('search.pooja');

    // routes/web.php
Route::get('/poojas', 'fetchPoojas')->name('fetchPoojas');

Route::get('/register', 'userregister')->name('user-register');

});

// foolter routes


Route::controller(FlowerUserBookingController::class)->group(function() {
        //flower routes
        Route::get('/flower', 'flower')->name('flower');
        Route::get('/checkout/{product_id}',  'show')->name('checkout');
        Route::post('/booking/flower/subscription', 'processBooking')->name('booking.flower.subscription');
        Route::get('/flower-booking/success/{order_id}',  'showSuccessPage')->name('flower-booking.success');
});
//user middleware routes

Route::group(['middleware' => ['auth:users']], function () {
        Route::controller(userController::class)->group(function() {
        Route::get('/user-dashboard', 'userdashboard')->name('userdashboard');
        
        Route::get('/manage-address', 'mngaddress')->name('mngaddress');
        Route::get('/address/set-default/{id}', 'setDefault')->name('setDefaultAddress');
        Route::get('/addaddress', 'addfrontaddress')->name('addfrontaddress');
        Route::get('/add-address', 'addaddress')->name('addaddress');
        Route::post('/saveaddress', 'saveaddress')->name('saveaddress');
        Route::post('/savefrontaddress', 'savefrontaddress')->name('savefrontaddress');
        Route::get('editaddress/{id}',  'editAddress')->name('editAddress');
        Route::post('updateaddress', 'updateAddress')->name('updateaddress');
        Route::get('removeaddress/{id}',  'removeAddress')->name('removeaddress');
        Route::get('/booking-history', 'orderhistory')->name('booking.history');
        Route::get('/rate-pooja/{id}','ratePooja')->name('rate.pooja');
        // Route::post('submit-rating', 'submitRating')->name('submitRating');
        // Route::post('/submit-or-update-rating',  'submitOrUpdateRating')->name('submitOrUpdateRating');
        Route::post('/submitOrUpdateRating', 'submitOrUpdateRating')->name('submitOrUpdateRating');


        Route::get('/view-ordered-pooja-details/{id}', [UserController::class, 'viewdetails'])->name('viewdetails');

        // Route::get('/view-ordered-pooja-details', 'viewdetails')->name('viewdetails');
        Route::get('/userprofile', 'userprofile')->name('user.userprofile');
        Route::get('/coupons', 'coupons')->name('coupons');
        // Route::delete('/profile/photo', 'deletePhoto')->name('user.deletePhoto');
        Route::put('/profile',  'updateProfile')->name('user.updateProfile');

        Route::delete('/delete-user-photo', 'deletePhoto')->name('delete.user.photo');

    });
});
Route::group(['middleware' => ['auth:users']], function () {
    Route::get('/payment/{booking_id}', [PaymentController::class, 'showPaymentPage'])->name('payment.page');
    Route::post('/payment/process/{booking_id}', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/booking-success/{booking}', [PaymentController::class,'bookingSuccess'])->name('booking.success');

    Route::get('/cancel-pooja/{id}', [PaymentController::class, 'showCancelForm'])->name('cancelForm');
    Route::post('/cancel-pooja/{booking_id}', [PaymentController::class, 'cancelBooking'])->name('cancelBooking');

    Route::post('/payment/pay-remaining/{booking_id}', [PaymentController::class, 'processRemainingPayment'])->name('payment.processRemainingPayment');

    Route::get('/pay-remaining-amount/{booking_id}', [PaymentController::class, 'payRemainingAmount'])->name('payRemainingAmount');
    // // Route::post('/process-remaining-payment/{booking_id}', [PaymentController::class, 'processRemainingPayment'])->name('processRemainingPayment');
    // Route::post('/process-remaining-payment/{booking_id}', [PaymentController::class, 'processRemainingPayment'])->name('processRemainingPayment');

});

## admin login
Route::controller(AdminController::class)->group(function() {
    Route::get('/admin', 'adminlogin')->name('adminlogin');
    Route::post('admin/authenticate', 'authenticate')->name('adminauthenticate');
    Route::post('admin/logout', 'adminlogout')->name('adminlogout');
});

## super admin login
Route::controller(SuperAdminController::class)->group(function() {
    Route::get('superadmin/', 'superadminlogin')->name('login');
    Route::post('superadmin/authenticate', 'authenticate')->name('authenticate');
    Route::get('superadmin/dashboard', 'dashboard')->name('dashboard');
    Route::post('superadmin/logout', 'sulogout')->name('sulogout');
    Route::post('superadmin/logout', 'sulogout')->name('sulogout');
});


##super admin routes
Route::prefix('superadmin')->middleware(['superadmin'])->group(function () {
    Route::controller(SuperAdminController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
    });

    Route::get('/addadmin', [SuperAdminController::class, 'addadmin']);
    Route::post('/saveadmin', [SuperAdminController::class, 'saveadmin']);
    Route::get('/editadmin/{id}', [SuperAdminController::class, 'editadmin'])->name('editadmin');
    Route::post('/update/{id}', [SuperAdminController::class, 'update'])->name('update');
    Route::get('/dltadmin/{id}', [SuperAdminController::class, 'dltadmin'])->name('dltadmin');

});

## admin routes
    Route::prefix('admin')->middleware(['admin'])->group(function () {
    // flower dashboard
    Route::controller(FlowerDashboardController::class)->group(function() {
        Route::get('/flower-dashboard', 'flowerDashboard')->name('flowerDashboard');
        Route::get('/total-deliveries', 'showTodayDeliveries')->name('admin.totalDeliveries');
    });

    Route::get('/manage-subadmins',  [SubadminController::class, 'managesubadmin'])->name('managesubadmin');
    Route::get('/subadmins/{id}/edit', [SubadminController::class, 'edit'])->name('subadmins.edit');
    Route::post('/subadmins/{id}/update', [SubadminController::class, 'update'])->name('subadmins.update');
    Route::delete('/subadmins/{id}/delete', [SubadminController::class, 'delete'])->name('subadmins.delete');


    Route::get('/add-product', [ProductController::class, 'addproduct']);
    Route::get('/manage-product',  [ProductController::class, 'manageproduct'])->name('manageproduct');

    Route::post('/create-product', [ProductController::class, 'createProduct'])->name('admin.products.store');
    Route::post('/purchaseSubscription', [ProductController::class, 'purchaseSubscription']);
    Route::post('/deactivate-expired-subscriptions', [ProductController::class, 'deactivateExpiredSubscriptions']);
    Route::get('/edit-product/{id}', [ProductController::class, 'editProduct'])->name('admin.edit-product');
    Route::post('/update-product/{id}', [ProductController::class, 'updateProduct'])->name('admin.update-product');
    Route::get('/delete-product/{id}', [ProductController::class, 'deleteProduct'])->name('admin.delete-product');


    // product dashboard
    Route::controller(ProductRequestController::class)->group(function() {

    Route::get('/manage-product-request', 'showRequests')->name('product-request');
    Route::post('/save-product-order/{id}', 'saveProductOrder')->name('admin.saveProductOrder');
    Route::post('/mark-product-payment/{id}', 'markPayment')->name('admin.markProductPayment');

    });

    Route::get('/manage-flower-request', [FlowerRequestController::class, 'showRequests'])->name('flower-request');

    Route::post('/save-order/{id}', [FlowerRequestController::class, 'saveOrder'])->name('admin.saveOrder');
    Route::post('/mark-payment/{id}', [FlowerRequestController::class, 'markPayment'])->name('admin.markPayment');

    Route::get('/notifications', [FlowerOrderController::class, 'showNotifications']);

    Route::get('/flower-orders', [FlowerOrderController::class, 'showOrders'])->name('admin.orders.index');
// ✅ Route must match the request you're making
    Route::put('/subscriptions/{id}/updateDates', [FlowerOrderController::class, 'updateDates'])->name('admin.subscriptions.updateDates');
    Route::post('/subscriptions/{id}/update-status', [FlowerOrderController::class, 'updateStatus'])
        ->name('admin.subscriptions.updateStatus');
    Route::post('/subscriptions/{id}/update-pause-dates', [FlowerOrderController::class, 'updatePauseDates'])->name('admin.subscriptions.updatePauseDates');


    Route::get('/product-orders', [ProductSubscriptionController::class, 'showProductOrder'])->name('admin.product.index');


    Route::get('/subscription/pause-page/{id}',[FlowerOrderController::class,  'pausePage'])->name('subscription.pausepage');
    Route::get('/subscription/resume-page/{id}',[FlowerOrderController::class,   'resumePage'])->name('subscription.resumepage');
    Route::post('/subscription/{id}/pause', [FlowerOrderController::class, 'pause'])->name('subscription.pause');
    Route::post('/subscription/{id}/resume', [FlowerOrderController::class, 'resume'])->name('subscription.resume');
    Route::get('/subscriptions/discontinue/{userId}', [FlowerOrderController::class, 'discontinue'])->name('admin.subscriptions.discontinue');

    Route::put('/orders/{order_id}/update-payment-status', [FlowerOrderController::class, 'updatePaymentStatus'])->name('admin.orders.updatePaymentStatus');
    Route::get('/manage-delivery-history', [FlowerOrderController::class, 'mngdeliveryhistory'])->name('admin.managedeliveryhistory');
    Route::get('/rider-all-details/{id}', [FlowerOrderController::class, 'showRiderDetails'])->name('admin.riderAllDetails');
    Route::post('/orders/mark-as-viewed', [OrderController::class, 'markAsViewed'])->name('orders.markAsViewed');

    //rider assign by admin and update
    Route::post('orders/{id}/assignRider', [FlowerOrderController::class, 'assignRider'])->name('admin.orders.assignRider');
    Route::post('orders/{id}/refferRider', [FlowerOrderController::class, 'refferRider'])->name('admin.orders.refferRider');
    
    Route::get('orders/{id}/editRider', [FlowerOrderController::class, 'editRider'])->name('admin.orders.editRider');
    Route::post('orders/{id}/updateRider', [FlowerOrderController::class, 'updateRider'])->name('admin.orders.updateRider');

    Route::get('/show-customer/{id}/details', [FlowerOrderController::class, 'showCustomerDetails'])->name('showCustomerDetails');

    Route::get('/active-subscriptions', [FlowerOrderController::class, 'showActiveSubscriptions'])->name('active.subscriptions');
    Route::get('/paused-subscriptions', [FlowerOrderController::class, 'showPausedSubscriptions'])->name('paused.subscriptions');
    Route::get('/expired-subscriptions', [FlowerOrderController::class, 'showexpiredSubscriptions'])->name('expired.subscriptions');

    Route::get('/orders-today', [FlowerOrderController::class, 'showOrdersToday'])->name('orders.today');
    
    Route::get('/flower-orders/{id}', [FlowerOrderController::class, 'showorderdetails'])->name('admin.orders.show');

    Route::put('/orders/{id}/update-address', [FlowerOrderController::class, 'updateAddress'])->name('admin.orders.updateAddress');

    Route::put('/orders/{id}/update-price', [FlowerOrderController::class, 'updatePrice'])->name('admin.orders.updatePrice');



    // Followup Controller 

    Route::get('/follow-up-subscriptions', [FollowUpController::class, 'followUpSubscriptions'])->name('admin.followUpSubscriptions');
    Route::post('/save-follow-up', [FollowUpController::class, 'saveFollowUp'])->name('admin.saveFollowUp');

// PRODUCT DETAILS ROUTES
Route::controller(ProductSubscriptionController::class)->group(function() {

    Route::get('/manage-customize-request','showCustomizeRequest')->name('product-customize-request');
    Route::get('/manage-product-subscription','showProductSubscription')->name('admin.productSubscriptionOrder');
    Route::post('/save-customize-price/{id}','saveCustomizePrice')->name('admin.saveCustomizePrice');
  });
  
    Route::get('/flower-pickup-report', [ReportController::class, 'flowerPickupReport'])->name('admin.flowerPickupReport');
    Route::post('/flower-pickup-report', [ReportController::class, 'generateReport'])->name('admin.generateFlowerPickupReport');
    Route::get('/revenue-report', [ReportController::class, 'showRevenueReport'])->name('admin.revenueReport');
    Route::post('/revenue-report', [ReportController::class, 'filterRevenueReport'])->name('admin.filterRevenueReport');

    // flower vendor controller

    Route::controller(FlowerVendorController::class)->group(function() {
        Route::get('/add-vendor-details', 'addVendorDetails')->name('admin.addVendorDetails');
        Route::post('/save-vendor-details', 'saveVendorDetails')->name('admin.saveVendorDetails');
        Route::get('/manage-vendor-details', 'manageVendorDetails')->name('admin.managevendor');
        Route::get('/vendor-all-details/{id}', 'vendorAllDetails')->name('admin.vendorAllDetails');

        Route::post('/delete-vendor-details/{imad}', 'deleteVendorDetails')->name('admin.deletevendor');
        Route::get('/edit-vendor-details/{id}', 'editVendorDetails')->name('admin.editVendorDetails');
        Route::put('/update-vendor-details/{vendor_id}',  'updateVendorDetails')->name('admin.updateVendorDetails');
    });

    Route::controller(FlowerPickupController::class)->group(function() {
        Route::get('/add-flower-pickup-details', 'addflowerpickupdetails')->name('admin.addflowerpickupdetails');
        Route::get('/add-flower-pickup-request', 'addflowerpickuprequest')->name('admin.addflowerpickuprequest');
        Route::post('/flower-pickup-request/approve/{id}',  'approveRequest')->name('flower-pickup-request.approve');

        Route::get('/manage-flower-pickup-details', 'manageflowerpickupdetails')->name('admin.manageflowerpickupdetails');
        Route::post('/save-flower-pickup-details', 'saveFlowerPickupDetails')->name('admin.saveFlowerPickupDetails');
        Route::post('/save-flower-pickup-assign-rider', 'saveFlowerPickupAssignRider')->name('admin.saveFlowerPickupAssignRider');
       
        Route::post('/update-payment/{pickup_id}', 'updatePayment')->name('update.payment');

        Route::get('/flower-pickup/edit/{id}', 'edit')->name('flower-pickup.edit');
        Route::put('/flower-pickup/update/{id}', 'update')->name('flower-pickup.update');
    });


   Route::controller(RiderController::class)->group(function() {
        Route::get('/add-rider-details', 'addRiderDetails')->name('admin.addRiderDetails');
        Route::post('/save-rider-details', 'saveRiderDetails')->name('admin.saveRiderDetails');
        Route::get('/manage-rider-details', 'manageRiderDetails')->name('admin.manageRiderDetails');
        Route::get('/delete-rider-details/{id}',  'deleteRiderDetails')->name('admin.deleteRiderDetails');
        Route::get('/edit-rider-details/{id}', 'editRiderDetails')->name('admin.editRiderDetails');
        Route::put('/update-rider-details/{id}', 'updateRiderDetails')->name('admin.updateRiderDetails');
        Route::get('/manage-order-assign', 'manageOrderAssign')->name('admin.manageOrderAssign');
        Route::get('/add-order-assign', 'addOrderAssign')->name('admin.addRiderDetails');
        Route::get('/get-apartments', 'getApartments')->name('admin.getApartments');
        Route::post('/save-order-assign', 'saveOrderAssign')->name('admin.saveOrderAssign');
        Route::get('/edit-order-assign/{id}', 'editOrderAssign')->name('admin.editOrderAssign');
        Route::put('/update-order-assign/{id}', 'updateOrderAssign')->name('admin.updateOrderAssign');
        Route::post('/delete-order-assign/{id}', 'deleteOrderAssign')->name('admin.deleteOrderAssign');
        Route::get('/deactive-order-assign/{rider_id}',  'deactiveOrderAssign')->name('admin.deactiveOrderAssign');
    });

    Route::controller(AdminController::class)->group(function() {

        Route::get('/dashboard', 'admindashboard')->name('admin.dashboard');
        Route::get('/manage-pandits', 'managepandit')->name('managepandit');
        Route::get('/pandit-profile', 'panditprofile')->name('panditprofile');
        Route::get('/manage-users', 'manageuser')->name('manageuser');
        Route::get('/user-profile/{id}', 'userProfile')->name('userprofile');
        Route::post('admin/pandit/accept/{id}', 'acceptPandit')->name('acceptPandit');
        Route::post('admin/pandit/reject/{id}', 'rejectPandit')->name('rejectPandit');
        Route::get('pandit-profile/{id}',  'showProfile')->name('panditProfile');

        Route::get('/deletIdproofs/{id}', 'deletIdproof')->name('deletIdproof');
        Route::get('/deletEducations/{id}', 'deletEducation')->name('deletEducation');
        Route::get('/deletVedics/{id}', 'deletVedic')->name('deletVedic');
        Route::get('/add-panditProfile', 'addProfile')->name('addProfile');
        Route::get('/add-panditCareer', 'addCareer')->name('addCareer');
        Route::post('/save-profile', 'saveprofile');
        Route::post('/save-career', 'savecareer');
        Route::get('admin/order-assign/{riderId}', 'showRiderDetails')->name('admin.orderAssign');
        Route::post('/admin/transfer-order', 'transferOrders')->name('admin.transferOrder');

        Route::get('/address-categories', 'showAddressByCategory')->name('admin.address.categories');
        Route::get('/address-category-users','getAddressUsersByCategory')->name('admin.address.category.users');
        Route::post('/address-update','updateAddress')->name('admin.address.update');
        Route::get('/apartment-users/{apartment}', 'viewApartmentUsers')->name('admin.apartment.users');

    });
    
    Route::controller(PujaController::class)->group(function() {
        Route::get('/manage-puja', 'managePuja')->name('managepuja');
        Route::get('/manage-special-puja', 'manageSpecialPuja')->name('manageSpecialPuja');

        Route::get('/add-puja', 'addpuja')->name('addpuja');
        Route::post('/savepuja', 'savepuja')->name('savepuja');
        Route::get('/editpooja/{pooja}', 'editpooja')->name('editpooja');
        Route::post('/updatepooja/{pooja}', 'updatepooja')->name('updatepooja');
        Route::get('/dltpooja/{pooja}', 'dltpooja')->name('dltpooja');

        Route::get('/manage-puja-list', 'managePujaList')->name('managePujaList');
        Route::post('/saveitem', 'saveitem')->name('saveitem');
        Route::get('/edititem/{item}', 'edititem')->name('edititem');
        Route::put('/updateitem', 'updateitem')->name('updateitem');
        Route::get('/dltitem/{item}', 'dltitem')->name('dltitem');
        
        // Route::get('/add-puja-list', 'addpujalist')->name('addpujalist');

        Route::get('/manage-puja-unit', 'manageunit')->name('manageunit');
        Route::post('/saveunit', 'saveunit')->name('saveunit');
        Route::get('/editunit/{unit}', 'editunit')->name('editunit');
        Route::put('/updateunit', 'updateunit')->name('updateunit');
        Route::get('/dltunit/{unit}', 'dltunit')->name('dltunit');
    });
    Route::controller(LocationController::class)->group(function() {
        Route::get('/manage-location', 'managelocation')->name('managelocation');
        Route::get('/add-location', 'addlocation')->name('addlocation');
        Route::get('/savelocation', 'savelocation')->name('savelocation');
    });

    Route::controller(LanguageController::class)->group(function() {
        Route::get('/manage-lang', 'managelang')->name('managelang');
        Route::get('/add-language', 'addlang')->name('addlang');
        Route::post('/savelang', 'savelang')->name('savelang');
        Route::get('/editlang/{lang}', 'editlang')->name('editlang');
        Route::put('/updatelang', 'updatelang')->name('updatelang');
        Route::get('/dltlang/{lang}', 'dltlang')->name('dltlang');
    });

    Route::controller(TitleController::class)->group(function() {
        Route::get('/manage-title', 'managetitle')->name('managetitle');
        Route::post('/savetitle', 'savetitle')->name('savetitle');
        Route::get('/edittitle/{title}', 'edittitle')->name('edittitle');
        Route::put('/updatetitle', 'updatetitle')->name('updatetitle');
        Route::get('/dlttitle/{title}', 'dlttitle')->name('dlttitle');
    });

    Route::controller(OrderController::class)->group(function() {
        Route::get('/manage-orders', 'manageorders')->name('manageorders');
        Route::get('/booking/{id}','showbooking')->name('admin.booking.show');
        Route::delete('/dltbooking/{id}', 'deleteBooking')->name('admin.booking.delete');
    });

    Route::controller(PodcastCreateController::class)->group(function() {
        Route::get('/podcast-create', 'podcastCreate')->name('podcastCreate');
        Route::post('/save-podcast-create', 'savePodcastCreate')->name('savePodcastCreate');
    });

    Route::controller(OfferDetailsController::class)->group(function() {
        Route::get('/offer-details', 'offerDetails')->name('admin.offerDetails');
        Route::post('/save-offer-details', 'saveOfferDetails')->name('admin.saveOfferDetails');
        Route::get('/manage-offer-details', 'manageOfferDetails')->name('admin.manageOfferDetails');
        Route::post('/update-offer-details','updateOfferDetails')->name('admin.updateOfferDetails');
        Route::delete('/delete-offer-details/{id}','deleteOfferDetails')->name('admin.deleteOfferDetails');
    });

     Route::controller(FlowerCalendarController::class)->group(function() {
        Route::get('/get-festival-calendar', 'getFestivalCalendar')->name('admin.getFestivalCalendar');
        Route::post('/save-festival-calendar', 'saveFestivalCalendar')->name('admin.saveFestivalCalendar');
        Route::get('/manage-festival-calendar', 'manageFestivalCalendar')->name('admin.manageFestivalCalendar');
        Route::delete('/delete-festival-calendar/{id}','deleteFestivalCalendar')->name('admin.deleteFestivalCalendar');
    });

    Route::controller(PodcastReportController::class)->group(function() {
        Route::get('/podcast-report', 'podcastReport')->name('podcastReport');
        Route::get('/podcast/script-details','getScriptDetails')->name('admin.podcast.scriptDetails');
        Route::get('/podcast/recording-details', 'getRecordingDetails')->name('admin.podcast.recordingDetails');
        Route::get('/podcast/editingDetails',  'getEditingDetails')->name('admin.podcast.editingDetails');
        Route::get('/podcast/publishDetails', 'getPublishDetails')->name('admin.podcast.publishDetails');
    });

    Route::controller(PodcastEditingController::class)->group(function () {
        Route::get('/podcast-editing', 'podcastEditing')->name('podcastEditing');
        Route::post('/podcast/save-editing/{podcast_id}', 'saveEditing')->name('podcast.saveEditing');
    
        Route::post('/start-podcast-edit/{podcast_id}', 'startPodcastEdit')->name('startPodcastEdit');
        Route::post('/cancel-podcast-edit/{podcast_id}', 'cancelPodcastEdit')->name('cancelPodcastEdit');
        Route::post('/complete-podcast-edit/{podcast_id}', 'completePodcastEdit')->name('completePodcastEdit');

        Route::get('/podcast-editing-verified', 'podcastEditingVerified')->name('podcastEditingVerified');
        Route::post('/update-editing-verified/{podcast_id}', 'updateEditingVerified')->name('updateEditingVerified');

        Route::post('/approve-editing-podcast/{podcast_id}', 'approvePodcastEditing')->name('approvePodcastEditing');
        Route::post('/reject-editing-podcast/{podcast_id}','rejectPodcastEditing')->name('rejectPodcastEditing');
    });
    

    Route::controller(PodcastScriptController::class)->group(function() {

        Route::get('/podcast-script', 'podcastScript')->name('podcastScript');
        Route::post('/update-podcast-details/{id}', 'updatePodcastDetails')->name('updatePodcastDetails');

        Route::post('/update-podcast-script/{podcast_id}', 'updatePodcastScript')->name('updatePodcastScript');
        Route::get('/podcast-recording', 'podcastRecording')->name('podcastRecording');

        Route::post('/start-podcast/{podcast_id}',  'startPodcast')->name('startPodcast');
        Route::post('/cancel-podcast/{podcast_id}', 'cancelPodcast')->name('cancelPodcast');
        Route::post('/complete-podcast/{podcast_id}','completePodcast')->name('completePodcast');
        Route::post('/save-complete-url/{podcast_id}','saveCompleteUrl')->name('saveCompleteUrl');

        // script editor
        Route::get('/script-editor/{podcast_id}','scriptEditor')->name('scriptEditor');
        Route::post('/save-script-editor/{podcast_id}','saveScriptEditor')->name('saveScriptEditor');

        Route::get('/podcast-script-verified', 'podcastScriptVerified')->name('podcastScriptVerified');
        Route::post('/update-script-verified/{podcast_id}', 'updateScriptVerified')->name('updateScriptVerified');

        Route::post('/approve-script-podcast/{podcast_id}', 'approvePodcastScript')->name('approvePodcastScript');
        Route::post('/reject-script-podcast/{podcast_id}','rejectPodcastScript')->name('rejectPodcastScript');
    });

    Route::controller(PublishPodcastController::class)->group(function() {
        Route::get('/publish-podcast', 'publishPodcast')->name('publishPodcast');
        Route::post('/savePublishPodcast', 'savePublishPodcast')->name('savePublishPodcast');
    });

    Route::controller(PodcastMediaController::class)->group(function() {
        Route::get('/podcast-media', 'podcastMedia')->name('podcastMedia');
        Route::post('/admin/podcast/update/{podcast_id}', 'updatePodcastMedia')->name('updatePodcastMedia');
    });

    Route::controller(PodcastSocialMediaController::class)->group(function() {
        Route::get('/social-media', 'PodcastSocialMedia')->name('PodcastSocialMedia');
        Route::post('/update-podcast-social-media/{podcast_id}', 'updatePodcastSocialMedia')->name('updatePodcastSocialMedia');
    });

    Route::controller(PodcastPlanningController::class)->group(function() {
        Route::get('/podcast-planning', 'podcastPlanning')->name('PodcastSocialMedia');
    });

    Route::controller(PodcastController::class)->group(function() {
        Route::get('/manage-podcast', 'managepodcast')->name('managepodcast');
        Route::post('/savepodcast', 'savepodcast')->name('savepodcast');
        Route::get('/editpodcast/{podcast}', 'editpodcast')->name('editpodcast');
        Route::post('/updatepodcast/{podcast}', 'updatepodcast')->name('updatepodcast');
        Route::get('/dltpodcast/{podcast}', 'destroy')->name('destroy');
        Route::get('/manage-podcast-category', 'managepodcastcategory')->name('managepodcastcategory');
        Route::post('/savecategory', 'saveCategory')->name('savecategory');
    });

    Route::put('updatecategory', [PodcastController::class, 'updateCategory'])->name('updatecategory');

    // Delete a category
    Route::get('deletecategory/{id}', [PodcastController::class, 'deleteCategory'])->name('deletecategory');
    Route::controller(BannerController::class)->group(function() {
        Route::get('/manage-app-banner', 'manageappbanner')->name('manageappbanner');
        Route::get('/add-app-banner', 'addbanner')->name('addbanner');
        Route::post('/savebanner', 'savebanner')->name('savebanner');
        Route::get('/editbanner/{banner}', 'editbanner')->name('editbanner');
        Route::post('/updatebanner/{id}', 'updatebanner')->name('updatebanner');
        Route::get('/deletebanner/{id}', 'deletebanner')->name('deletebanner');
    });

    Route::controller(LocalityController::class)->group(function() {
        Route::get('/manage-locality', 'managelocality')->name('admin.managelocality');
        Route::get('/add-locality', 'addlocality')->name('admin.addlocality');
        Route::post('/savelocality', 'savelocality')->name('savelocality');
        Route::get('/editlocality/{id}', 'editLocality')->name('editlocality');
        Route::put('/updatelocality/{id}', 'updateLocality')->name('updatelocality');
        Route::delete('/deletelocality/{id}', 'deleteLocality')->name('deletelocality');
    });

    Route::controller(PromonationController::class)->group(function() {
        Route::get('/manage-promonation', 'managepromonation')->name('admin.managepromonation');
        Route::get('/add-promonation', 'addpromonation')->name('admin.addpromonation');
        Route::post('/savepromonation', 'savepromonation')->name('savepromonation');
        Route::get('/editpromonation/{id}', 'editpromonation')->name('editpromonation');
        Route::post('/updatepromonation/{id}', 'updatepromonation')->name('updatepromonation');
        Route::delete('/deletepromonation/{id}', 'deletepromonation')->name('deletepromonation');
    });

    Route::get('/send-notification', [AdminNotificationController::class, 'create'])->name('admin.notification.create');
    Route::post('/send-notification', [AdminNotificationController::class, 'send'])->name('admin.notification.send');
    Route::delete('/notifications/{id}', [AdminNotificationController::class, 'delete'])->name('admin.notifications.delete');
    Route::post('/notifications/resend/{id}', [AdminNotificationController::class, 'resend'])->name('admin.notifications.resend');
    Route::get('/send-whatsapp-notification', [AdminNotificationController::class, 'whatsappcreate'])->name('admin.whatsapp-notification.create');
    Route::post('/send-whatsapp-notification', [AdminNotificationController::class, 'sendWhatsappNotification'])->name('admin.whatsapp-notification.send');

    Route::controller(YoutubeController::class)->group(function() {
        Route::get('/youtube', 'youTube')->name('youTube');
        Route::post('/save-youtube-url', 'store')->name('saveYoutubeUrl');
        Route::get('/manage-youtube', 'manageYoutube')->name('manageYoutube');
        Route::get('/delete-youtube/{id}', 'destroy')->name('deleteYoutube');
        Route::get('/edit-youtube/{id}', 'edit')->name('editYoutube');
        Route::post('/update-youtube/{id}',  'update')->name('updateYoutube');
    });

    Route::controller(UserManagementController::class)->group(function() {
        Route::get('/existing-user', 'existingUser')->name('existingUser');
        Route::post('/save-demo-order-details', 'handleUserData')->name('saveDemoOrderDetails');
        Route::get('/get-user-addresses/{userId}','getUserAddresses');
    });

    Route::controller(UserCustomizeOrderController::class)->group(function() {
        Route::get('/demo-customize-order', 'demoCustomizeOrder')->name('demoCustomizeOrder');
        Route::post('/save-customize-order', 'saveCustomizeOrder')->name('saveCustomizeOrder');
        Route::get('/get-user-addresses/{userId}','getUserAddresses');
    });

    Route::controller(NewUserOrderController::class)->group(function() {
        Route::get('/new-user-order', 'newUserOrder')->name('newUserOrder');
        Route::post('/save-new-user-order', 'saveNewUserOrder')->name('saveNewUserOrder');
    });
});

// user routes
// Route::prefix('user')->middleware(['user'])->group(function () {
//     Route::controller(userController::class)->group(function() {
//         Route::get('/dashboard', 'dashboard')->name('user.dashboard');
//     });
// });

// Route::controller(PanditLoginController::class)->group(function() {
//     Route::post('/pandit/save-panditlogin', 'storeLoginData')->name('pandit.login');
//     Route::get('/pandit/panditotp','showOtpForm')->name('pandit.otp');
//     Route::post('/pandit/check-otp', 'checkOtp')->name('check.otp');
// }); 

// Route::controller(PanditLoginController::class)->group(function() {
//     Route::get('/pandit/panditotp','showOtpForm')->name('pandit.otp');
// });


Route::controller(PanditOtpController::class)->group(function() {
    Route::post('/send-otp',  'sendOtp');
    Route::post('/verify-otp',  'verifyOtp');
});

/// pandit routes
// Route::group(['prefix' => 'pandit'], function () {
//         Route::controller(PanditController::class)->group(function() {
//         Route::get('/panditlogin', 'panditlogin')->name('panditlogin');
//         Route::get('/poojarequest', 'poojarequest')->name('poojarequest');
//         Route::get('/booking/details/{id}', 'getDetails')->name('bookingdetails');
//         Route::post('/booking/approve/{id}', 'approveBooking')->name('pandit.booking.approve');
//         Route::post('/booking/reject/{id}', 'rejectBooking')->name('pandit.booking.reject');
//         Route::get('/dashboard', 'index')->name('pandit.dashboard')->middleware('auth:pandits');
//         Route::post('/panditlogout', 'panditlogout')->name('pandit.logout');
//     });
// });

Route::group(['prefix' => 'pandit'], function () {
    Route::controller(PanditController::class)->group(function() {
        Route::get('/panditlogin', 'panditlogin')->name('panditlogin');
        Route::get('/poojarequest', 'poojarequest')->name('poojarequest');
        Route::get('/booking/details/{id}', 'getDetails')->name('bookingdetails');
        Route::get('/calender/pooja/{date}','calenderPooja');
        Route::post('/booking/approve/{id}', 'approveBooking')->name('pandit.booking.approve');
        Route::post('/booking/reject/{id}', 'rejectBooking')->name('pandit.booking.reject');
        Route::get('/dashboard', 'index')->name('pandit.dashboard')->middleware('auth:pandits');
        Route::post('/panditlogout', 'panditlogout')->name('pandit.logout');
    });
});

// pandit profile crud operation
    Route::group(['prefix' => 'pandit'], function () {
    Route::controller(ProfileController::class)->group(function() {
    Route::get('/profile', 'panditprofiles')->name('pandit.profile');
    Route::post('/save-profile', 'saveprofile');
    Route::get('/manageprofile', 'manageprofile')->name('manageprofile');
    Route::put('/updateProfile/{id}','updateProfile')->name('updateProfile');
    });
});

// pandit career crud operation
Route::group(['prefix' => 'pandit'], function () {
    Route::controller(CareerController::class)->group(function() {
    Route::get('/career', 'profilecareer')->name('profilecareer');
    Route::get('/managecareer', 'managecareer')->name('managecareer');
    Route::post('/save-career', 'savecareer');
    Route::get('/deletIdproof/{id}', 'deletIdproof')->name('deletIdproof');
    Route::get('/deletEducation/{id}', 'deletEducation')->name('deletEducation');
    Route::get('/deletVedic/{id}', 'deletVedic')->name('deletVedic');
    Route::put('/updateCareer/{pandit_id}', 'updateCareer')->name('updateCareer');
    });

    });
// pandit skill crud operation
Route::group(['prefix' => 'pandit'], function () {
    Route::controller(SkillController::class)->group(function() {
    Route::post('/save-skillpooja', 'saveSkillPooja');
    Route::put('/update-skillpooja', 'updateSkillPooja')->name('updateSkillPooja');
    Route::get('/poojaskill', 'poojaskill')->name('poojaskill');
    Route::get('/managepoojaskill', 'managepoojaskill')->name('managepoojaskill');
    });
});
// pandit pooja details crud operation

Route::group(['prefix' => 'pandit'], function () {
    Route::controller(PoojaDetailsController::class)->group(function() {
    Route::get('/poojadetails', 'poojadetails')->name('poojadetails');
    Route::post('/save-poojadetails', 'savePoojadetails');
    Route::get('/managepoojadetails', 'managepoojadetails')->name('managepoojadetails');
    Route::put('/update-poojadetails', 'updatePoojadetails')->name('updatePoojadetails');
    Route::get('/pandit/poojadetails',  'poojadetails')->name('pandit.poojadetails');
    });
});

Route::controller(AreaController::class)->group(function() {
    Route::get('pandit/poojaarea', 'poojaArea')->name('poojaarea');
    Route::get('pandit/managearea', 'manageArea')->name('managearea');
    Route::get('pandit/get-district/{stateCode}', 'getDistrict');
    Route::get('pandit/get-subdistrict/{districtCode}', 'getSubdistrict'); 
    Route::get('pandit/get-village/{subdistrictCode}', 'getVillage'); 
    Route::post('pandit/save-form', 'saveForm')->name('save.form');
    Route::delete('/pandit/delete-poojaarea/{id}', 'deletePoojaArea')->name('delete.poojaarea');
    Route::get('/pandit/edit-poojaarea/{id}', 'editPoojaArea')->name('edit.poojaarea');
    Route::put('/pandit/update-poojaarea/{id}','updatePoojaArea')->name('update.poojaarea');
});

// pandit bank details
Route::group(['prefix' => 'pandit'], function () {
    Route::controller(BankController::class)->group(function() {
        Route::get('/bankdetails', 'bankdetails')->name('bankdetails');
        Route::post('/savebankdetails', 'savebankdetails');
    });
});

Route::group(['prefix' => 'pandit'], function () {
    Route::controller(AddressController::class)->group(function() {
        Route::get('/address', 'address')->name('address');
        Route::post('/saveaddress', 'saveaddress');
    });
});

// Route::group(['prefix' => 'pandit'], function () {
//         Route::controller(PoojaListController::class)->group(function() {
//         Route::get('/poojaitemlist', 'poojaitemlist')->name('poojaitemlist');
//         Route::get('/poojaitem', 'singlepoojaitem');
//         Route::post('/save-poojaitemlist', 'savePoojaItemList');
//         Route::get('/managepoojaitem', 'managepoojaitem')->name('managepoojaitem');
//         Route::delete('/delete-poojaitem/{id}','deletePoojaItem')->name('deletePoojaItem');
//         Route::get('/get-poojadetails/{pooja_id}', 'getPoojaDetails');
//         Route::put('/updatepoojalist', 'updatePoojalist');
//         Route::get('/get-variants/{listName}', 'getVariants');
//         Route::put('/pooja/{id}', 'updatePoojaItem');
//     });
// });

Route::group(['prefix' => 'pandit'], function () {
    Route::controller(PoojaListController::class)->group(function() {
    Route::get('/poojaitemlist', 'poojaitemlist')->name('poojaitemlist');
    Route::get('/poojaitem', 'singlepoojaitem');
    Route::post('/save-poojaitemlist', 'savePoojaItemList');
    Route::get('/managepoojaitem', 'managepoojaitem')->name('managepoojaitem');
    Route::delete('/delete-poojaitem/{id}', 'deletePoojaItem')->name('deletePoojaItem');
    Route::get('/get-poojadetails/{pooja_id}', 'getPoojaDetails');
    Route::put('/updatepoojalist', 'updatePoojalist');
    Route::get('/get-variants/{listName}', 'getVariants');
    Route::get('/get-variants-title/{itemId}',  'getVariantTitle');
    Route::get('/edit-poojaitem/{id}', 'editPoojaItem')->name('editPoojaItem');
    Route::put('/update-poojaitem/{id}', 'updatePoojaItem')->name('updatePoojaItem');
    });
});

Route::controller(PoojaStatusController::class)->group(function() {
    Route::post('/pooja/start','start')->name('pooja.start');
    Route::post('/pooja/end', 'end')->name('pooja.end');
});

Route::controller(PoojaHistoryController::class)->group(function() {
    Route::get('pandit/poojahistory', 'poojahistory')->name('poojahistory');
});

Route::controller(MarketingVisitPlaceController::class)->group(function() {
    Route::get('/marketing-visit-place','getVisitPlace')->name('admin.getVisitPlace');
    Route::post('/save-marketing-visit-place', 'storeVisitPlace')->name('marketing.visit.place.store');
    Route::get('/manage-marketing-visit-place','manageVisitPlace')->name('admin.visitPlace');
    Route::get('/visit-place/edit/{id}',  'editVisitPlace')->name('admin.editVisitPlace');
    Route::post('/visit-place/update/{id}', 'updateVisitPlace')->name('admin.updateVisitPlace');
});

Route::controller(FlowerReportsController::class)->group(function() {
    Route::get('/report-subscription','subscriptionReport')->name('subscription.report');
    Route::get('/report-customize','reportCustomize')->name('report.customize');
    Route::match(['get', 'post'], '/report-flower-pick-up', 'flowerPickUp')->name('report.flower.pickup');
});

Route::post('/admin/save-flower-promotion', [PromotionController::class, 'saveFlowerPromotion'])->name('admin.saveFlowerPromotion');
Route::get('/admin/manage-flower-promotion', [PromotionController::class, 'manageFlowerPromotion'])->name('admin.manageFlowerPromotion');
Route::put('/admin/promotions/{id}', [PromotionController::class, 'updateFlowerPromotion'])->name('admin.updateFlowerPromotion');
Route::delete('/admin/promotions/{id}', [PromotionController::class, 'deleteFlowerPromotion'])->name('admin.deleteFlowerPromotion');

// Example promotions list page (for redirect)
Route::get('/admin/promotion-details', function () {
    return view('admin.flower-promotion'); // your blade file
})->name('admin.promotionList');

Route::get('/admin/office-fund', function () {
    return view('admin.office-fund-received'); // your blade file
})->name('admin.officeFundReceived');

Route::controller(OfficeTransactionController::class)->group(function() {
   Route::get('/admin/office-trasaction','getOfficeTransaction')->name('admin.officeTransactionDetails');
   Route::post('/save-office-transaction',  'saveOfficeTransaction')->name('saveOfficeTransaction');
   Route::get('/manage-office-transaction',  'manageOfficeTransaction')->name('manageOfficePayments');
   Route::put('/office-transactions/{id}', 'update')->name('officeTransactions.update');
   Route::delete('/office-transactions/{id}', 'destroy')->name('officeTransactions.destroy');
   Route::get('/office-fund/total-by-category', 'fundTotalsByCategory')->name('officeFund.totalByCategory');
   Route::get('/office-transactions/filter',  'filterOfficeTransactions')->name('officeTransactions.filter');
   Route::post('/save-office-fund',  'saveOfficeFund')->name('saveOfficeFund');
   Route::get('/manage-office-fund',  'manageOfficeFund')->name('manageOfficeFund');
   Route::put('/office-fund/{id}', 'updateOfficeFund')->name('officeFund.update');
   Route::delete('/office-fund/{id}', 'destroyOfficeFund')->name('officeFund.destroy');
   Route::get('/office-fund/filter', 'filterOfficeFund')->name('officeFund.filter');
});

Route::controller(ReferController::class)->group(function() {
    Route::get('/offer-create','offerCreate')->name('refer.offerCreate');
    Route::post('/save-refer-offer', 'saveReferOffer')->name('refer.saveReferOffer');
    Route::get('/manage-refer-offer', 'manageReferOffer')->name('refer.manageReferOffer');
    Route::put('/refer/offers/{offer}','update')->name('refer.offer.update');
    Route::delete('/refer/offers/{offer}',  'destroy')->name('refer.offer.destroy');

    // offer claim
    Route::get('/offer-claim', 'offerClaim')->name('refer.offerClaim');
    Route::post('/save-offer-claim', 'saveOfferClaim')->name('refer.saveOfferClaim');
    Route::get('/manage-offer-claim', 'manageOfferClaim')->name('refer.manageOfferClaim');
    Route::put('/refer/claim/{claim}', 'updateClaimStatus')->name('refer.claim.update');
    Route::delete('/refer/claim/{claim}', 'destroyClaim')->name('refer.claim.destroy');
    Route::post('/refer/claims/{claim}/approve/start',  'startApprovalCode')->name('refer.claim.approve.start');
    Route::post('/refer/claims/{claim}/approve/verify', 'verifyApprovalCode')->name('refer.claim.approve.verify');
    Route::get('/refer/offer-claims/list', 'listOfferClaims')->name('refer.offerClaims.list');
    Route::get('/admin/referrals','referralsIndex')->name('admin.referrals.index');
});

Route::controller(MonthWiseFlowerPriceController::class)->group(function() {
    Route::get('/month-wise-flower-price', 'create')->name('admin.monthWiseFlowerPrice');
    Route::post('/month-wise-flower-price', 'saveFlowerPrice')->name('admin.saveFlowerPrice');
    Route::get('/vendor-flowers', 'vendorFlowers')->name('admin.getVendorFlowers');
    Route::get('/manage-flower-price', 'manageFlowerPrice')->name('admin.manageFlowerPrice');

    Route::post('/items-store','storeItem')->name('items.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::put('/flower-price/update/{id}', [MonthWiseFlowerPriceController::class, 'updateFlowerPrice'])
        ->name('flower-price.update');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::delete('/flower-price/delete/{id}', [MonthWiseFlowerPriceController::class, 'deleteFlowerPrice'])->name('deleteFlowerPrice');
});