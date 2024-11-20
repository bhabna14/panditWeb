<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\userController;
use App\Http\Controllers\User\FlowerUserBookingController;

use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\FlowerRegistrationController;
use App\Http\Controllers\User\FlowerAddressController;


use App\Http\Controllers\OtplessLoginController;


use App\Http\Controllers\Admin\PujaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TitleController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PodcastController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\YoutubeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\FlowerRequestController;
use App\Http\Controllers\Admin\FlowerOrderController;
use App\Http\Controllers\Admin\LocalityController;
use App\Http\Controllers\Admin\PromonationController;


use App\Http\Controllers\Pandit\AreaController;
use App\Http\Controllers\Pandit\BankController;
use App\Http\Controllers\Pandit\SkillController;
use App\Http\Controllers\Pandit\CareerController;
use App\Http\Controllers\Pandit\PanditController;
use App\Http\Controllers\Pandit\AddressController;
use App\Http\Controllers\Pandit\ProfileController;
use App\Http\Controllers\Pandit\PoojaListController;
// use App\Http\Controllers\Pandit\PanditLoginController; 
use App\Http\Controllers\Pandit\PoojaDetailsController;
use App\Http\Controllers\Pandit\PanditOtpController;
use App\Http\Controllers\Pandit\PoojaStatusController;
use App\Http\Controllers\Pandit\PoojaHistoryController;


use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\Superadmin\SuperAdminController;
use Twilio\Rest\Client;


Route::fallback(function () {
    abort(404);
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
    Route::get('/about-us', 'aboutus')->name('aboutus');
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
Route::controller(FlowerUserBookingController::class)->group(function() {
        //flower routes
        Route::get('/flower', 'flower')->name('flower');
});
Route::group(['middleware' => ['auth:users']], function () {
    Route::controller(FlowerUserBookingController::class)->group(function() {
      
        Route::get('/checkout/{product_id}',  'show')->name('checkout');

        Route::post('/booking/flower/subscription', 'processBooking')->name('booking.flower.subscription');
        Route::get('/subscription-history', 'subscriptionhistory')->name('subscription.history');
        Route::post('/subscription/{order_id}/pause',  'pause')->name('subscription.pause');
        Route::post('/subscription/{order_id}/resume',  'resume')->name('subscription.resume');

        Route::get('/subscription-details/{order_id}',  'viewSubscriptionOrderDetails')->name('subscription.details');
        // customized order route
        Route::get('/cutsomized-checkout/{product_id}',  'cutsomizedcheckout')->name('cutsomized-checkout');

        Route::post('/booking/customized-order', 'customizedstore')->name('booking.flower.customizedstore');
        Route::post('/request-order/payment', 'RequestpaymentCallback')->name('request.order.payment.callback');
        Route::get('/requested-order-history', 'requestedorderhistory')->name('requested.order.history');
        Route::get('/requested-order/{id}/details', 'requestedOrderDetails')->name('requested.order.details');

        Route::get('/user-flower-dashboard', 'userflowerdashboard')->name('userflowerdashboard');
       

    });
    Route::controller(FlowerAddressController::class)->group(function() {
      
       
        Route::get('/manage-user-address', 'mnguseraddress')->name('mnguseraddress');
        Route::get('/useraddress/set-default/{id}', 'usersetDefault')->name('usersetDefaultAddress');
        Route::get('/user-add-address', 'useraddaddress')->name('useraddaddress');

        Route::get('/useraddaddress', 'useraddfrontaddress')->name('useraddfrontaddress');

        Route::post('/saveuseraddress', 'saveuseraddress')->name('saveuseraddress');
        Route::post('/savefrontaddress', 'savefrontaddress')->name('savefrontaddress');

        Route::get('edituseraddress/{id}',  'edituseraddress')->name('edituseraddress');
        Route::post('updateuseraddress', 'updateuseraddress')->name('updateuseraddress');
        Route::get('removeaddress/{id}',  'removeAddress')->name('removeaddress');


    });
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
        // Route::post('/savefrontaddress', 'savefrontaddress')->name('savefrontaddress');

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
    Route::get('/add-product', [ProductController::class, 'addproduct']);
    Route::get('/manage-product',  [ProductController::class, 'manageproduct'])->name('manageproduct');

    Route::post('/create-product', [ProductController::class, 'createProduct'])->name('admin.products.store');
    Route::post('/purchaseSubscription', [ProductController::class, 'purchaseSubscription']);
    Route::post('/deactivate-expired-subscriptions', [ProductController::class, 'deactivateExpiredSubscriptions']);
    Route::get('/edit-product/{id}', [ProductController::class, 'editProduct'])->name('admin.edit-product');
    Route::post('/update-product/{id}', [ProductController::class, 'updateProduct'])->name('admin.update-product');
    Route::get('/delete-product/{id}', [ProductController::class, 'deleteProduct'])->name('admin.delete-product');
    
    Route::get('/manage-flower-request', [FlowerRequestController::class, 'showRequests'])->name('flower-request');
    Route::post('/save-order/{id}', [FlowerRequestController::class, 'saveOrder'])->name('admin.saveOrder');
    Route::post('/mark-payment/{id}', [FlowerRequestController::class, 'markPayment'])->name('admin.markPayment');


    Route::get('/flower-orders', [FlowerOrderController::class, 'showOrders'])->name('admin.orders.index');
    Route::get('/show-customer/{id}/details', [FlowerOrderController::class, 'showCustomerDetails'])->name('showCustomerDetails');

    Route::get('/flower-request-orders', [FlowerOrderController::class, 'showRequestOrders'])->name('admin.requestorder.index');

    Route::get('/active-subscriptions', [FlowerOrderController::class, 'showActiveSubscriptions'])->name('active.subscriptions');
    Route::get('/paused-subscriptions', [FlowerOrderController::class, 'showPausedSubscriptions'])->name('paused.subscriptions');
    Route::get('/orders-today', [FlowerOrderController::class, 'showOrdersToday'])->name('orders.today');
    
Route::get('/flower-orders/{id}', [FlowerOrderController::class, 'show'])->name('admin.orders.show');
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
        // Route::get('/savelocation', 'savelocation')->name('savelocation');
        Route::delete('/dltbooking/{id}', 'deleteBooking')->name('admin.booking.delete');
    });

    Route::controller(PodcastController::class)->group(function() {
        Route::get('/manage-podcast', 'managepodcast')->name('managepodcast');
        Route::get('/add-podcast', 'addpodcast')->name('addpodcast');
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
        Route::post('/updatelocality/{id}', 'updateLocality')->name('updatelocality');
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


    Route::controller(YoutubeController::class)->group(function() {
        Route::get('/youtube', 'youTube')->name('youTube');
        Route::post('/save-youtube-url', 'store')->name('saveYoutubeUrl');
        Route::get('/manage-youtube', 'manageYoutube')->name('manageYoutube');
        Route::get('/delete-youtube/{id}', 'destroy')->name('deleteYoutube');
        Route::get('/edit-youtube/{id}', 'edit')->name('editYoutube');
        Route::post('/update-youtube/{id}',  'update')->name('updateYoutube');
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

Route::controller(PanditLoginController::class)->group(function() {
    Route::get('/pandit/panditotp','showOtpForm')->name('pandit.otp');
});


Route::controller(PanditOtpController::class)->group(function() {
    Route::post('/send-otp',  'sendOtp');
    Route::post('/verify-otp',  'verifyOtp');
});

/// pandit routes
// Route::group(['prefix' => 'pandit'], function () {
//     Route::controller(PanditController::class)->group(function() {
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
        // Route::get('/managepoojadetails', 'managepoojadetails')->name('managepoojadetails');
       
    });
});

// pandit Address details
Route::group(['prefix' => 'pandit'], function () {
    Route::controller(AddressController::class)->group(function() {
        Route::get('/address', 'address')->name('address');
        Route::post('/saveaddress', 'saveaddress');
       
    });
});

// Route::group(['prefix' => 'pandit'], function () {
//     Route::controller(PoojaListController::class)->group(function() {
//         Route::get('/poojaitemlist', 'poojaitemlist')->name('poojaitemlist');
//         Route::get('/poojaitem', 'singlepoojaitem');
//         Route::post('/save-poojaitemlist', 'savePoojaItemList');
//         Route::get('/managepoojaitem', 'managepoojaitem')->name('managepoojaitem');
//         Route::delete('/delete-poojaitem/{id}','deletePoojaItem')->name('deletePoojaItem');
//         Route::get('/get-poojadetails/{pooja_id}', 'getPoojaDetails');
//         Route::put('/updatepoojalist', 'updatePoojalist');
//         Route::get('/get-variants/{listName}', 'getVariants');
//             Route::put('/pooja/{id}', 'updatePoojaItem');
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