<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/// controllers for pandit dashboards
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\BankController;
// use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CareersController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PoojaListController;
use App\Http\Controllers\Api\UserLoginController;
use App\Http\Controllers\Api\PoojaSkillController;
use App\Http\Controllers\Api\YoutubeUrlController;
use App\Http\Controllers\Api\PoojaDetailsController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\PanditLoginController;
use App\Http\Controllers\Api\CheckController;
use App\Http\Controllers\Api\PoojaStatusController;
use App\Http\Controllers\Api\FlowerBookingController;
use App\Http\Controllers\Api\OfferDetailsApiController;
use App\Http\Controllers\Api\FlowerCalendarApiController;

use App\Http\Controllers\Admin\NotificationController;

/// controllers for frontend pages 
use App\Http\Controllers\Api\PanditController;
use App\Http\Controllers\Api\PujaController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\RiderApi\OrderController;
use App\Http\Controllers\Api\ProductApiController;

use App\Http\Controllers\RiderApi\RiderLoginController;
use App\Http\Controllers\Notification\PushNotificationController;


Route::post('/save-token', [PushNotificationController::class, 'saveToken']);

// Routes for Rider Login
// Route::prefix('rider')->group(function () {
//     Route::post('/send-otp', [RiderLoginController::class, 'sendOtp'])->name('rider.sendOtp');
//     Route::post('/verify-otp', [RiderLoginController::class, 'verifyOtp'])->name('rider.verifyOtp');
// });

Route::middleware('auth:rider-api')->group(function () {
    Route::get('rider/details', [RiderLoginController::class, 'getRiderDetails']);
    Route::get('rider/get-assign-pickup', [OrderController::class, 'getAssignPickup']);
    Route::post('/rider/update-flower-prices/{pickup_id}', [OrderController::class, 'updateFlowerPrices']);
    Route::post('/start-delivery', [OrderController::class, 'startDelivery']);

   // assign order to rider
   Route::get('rider/get-assign-orders', [OrderController::class, 'getAssignedOrders'])->name('rider.assignedOrders');
   Route::post('/rider/deliver/{order_id}', [OrderController::class, 'markAsDelivered'])
   ->middleware('auth:rider-api');

   // Route for fetching today's requested orders
    Route::get('/rider/requested-today-orders', [OrderController::class, 'getTodayRequestedOrders'])
    ->middleware('auth:rider-api');

    // Route for marking an order as delivered
    Route::post('/rider/requested-deliver/{order_id}', [OrderController::class, 'markAsRequestedDelivered'])
    ->middleware('auth:rider-api');

    Route::post('/rider/flower-pickup-request', [OrderController::class, 'savePickupRequest']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/current-orders', [ProductController::class, 'getCurrentOrders']);

Route::get('/products', [ProductController::class, 'getActiveProducts']);
Route::controller(PanditLoginController::class)->group(function() {
    Route::post('/pandit-send-otp',  'sendOtp');
    Route::post('/pandit-verify-otp', 'verifyOtp');
    Route::middleware('auth:sanctum')->post('/panditlogout','panditLogout');
});

Route::get('/check-new-notifications', [NotificationController::class, 'checkNewNotifications']);

Route::controller(AreaController::class)->group(function() {
    Route::get('/get-districts/{stateCode}', 'getDistrict');
    Route::get('/get-subdistricts/{districtCode}', 'getSubdistrict');
    Route::get('/get-village/{subdistrictsCode}', 'getVillage');
    Route::post('/save-poojaArea', 'saveForm');
    Route::get('/manage-poojaArea', 'manageArea');
    Route::post('/update-pooja-area/{id}', 'updatePoojaArea');
    Route::post('/delet-pooja-area/{id}','deletePoojaArea');

});

Route::controller(ProfileController::class)->group(function() {
    Route::get('/pandit-titles', 'panditTitles');
    Route::post('/profile/save', 'saveProfile');
    Route::post('/profile/updatepanditprofile','updateProfile');
    Route::get('/show-profile-details', 'showProfileDetails');
    Route::middleware('auth:sanctum')->get('/edit-profile',  'editProfile');
    Route::middleware('auth:sanctum')->post('/update-photo','updatePhoto');


});

Route::controller(CareersController::class)->group(function() {
    Route::post('/career/save', 'saveCareer');
});

Route::controller(FlowerCalendarApiController::class)->group(function() {
    Route::get('/festivals', 'getFestivalCalendar');
});

Route::controller(OfferDetailsApiController::class)->group(function() {
    Route::get('/offer-details', 'getOfferDetails');
});

Route::controller(PoojaSkillController::class)->group(function() {
    Route::get('/managepoojaskill',  'manageSkill');
    Route::post('/poojaskill/save', 'saveSkillPooja');
});

Route::controller(PoojaDetailsController::class)->group(function() {
    Route::get('/poojadetails','getPoojaDetails');
    Route::post('/save-pooja-details',  'savePoojadetails');
    Route::get('/managepoojadetails',  'managePoojaDetails');
    // Route::post('/updatePoojadetails', 'updatePoojadetails');
    Route::get('/get-pooja-details/{id}','getSinglePoojadetails');
    Route::post('/update-pooja-details/{id}', 'updatePoojadetails');
    Route::post('/delete-pooja/{pooja_id}', 'deletePoojaDetails');


});

Route::controller(PoojaListController::class)->group(function() {
    Route::get('/all-pooja-list', 'AllPoojaList');
   
    Route::middleware('auth:sanctum')->get('/pooja-item-list', 'poojaitemlist');
    
    Route::get('/approved-pooja', 'approvedPoojaList');
    
    // did by bhabna
    Route::get('/list-pooja-item', 'listofitem');
    Route::get('/pooja-item-list/{pooja_id}', 'poojaitemlist');
    Route::post('/save-pooja-item-list', 'savePoojaItemList');
    Route::post('/update-pooja-items/{id}',  'updatePoojaitem');
    Route::delete('/delete-pooja-items/{id}', 'deletePoojaItem');
    Route::get('/manageunit',  'manageUnitApi');
   

});

Route::controller(PoojaStatusController::class)->group(function() {
    Route::middleware('auth:sanctum')->post('/bookings/{id}/approve', 'approveBooking');
    Route::middleware('auth:sanctum')->post('/bookings/{id}/reject', 'rejectBooking');
    Route::middleware('auth:sanctum')->post('/bookings/start', 'start');
    Route::middleware('auth:sanctum')->post('/bookings/end',  'end');
});

Route::controller(BankController::class)->group(function() {
    Route::post('/pandit/savebankdetails', 'saveBankDetails');
    Route::get('/pandit/get-bank-details','getBankDetails');

});

Route::controller(AddressController::class)->group(function() {
    Route::post('/pandit/saveaddress', 'saveAddress');
    Route::middleware('auth:sanctum')->get('pandit/show-address','address');

});

Route::controller(PodcastController::class)->group(function() {
    Route::get('podcasts', 'podcasts');
    Route::get('/podcasthomepage',  'podcasthomepage');
    Route::get('/podcastcategory',  'podcastCategory');
});
Route::controller(YoutubeUrlController::class)->group(function() {

Route::get('/manage-youtube','manageYoutube')->name('api.manageYoutube');

});
Route::controller(CheckController::class)->group(function() {
    Route::get('/check-panditid', 'checkPanditProfile');

});


///home page apis
Route::controller(PujaController::class)->group(function() {
    Route::get('poojalists', 'poojalists');
    Route::get('upcomingpoojalists', 'upcomingpoojalists'); 
    Route::get('homepage', 'homepage'); 
    Route::get('panditlists', 'panditlist'); 
    Route::get('/app-banners', 'manageAppBanner');
});

// single page apis
Route::get('/our-pandit/{slug}', [PanditController::class, 'singlePanditDetails']);
Route::get('/pooja/{slug}', [PanditController::class, 'poojadetails']);

//home page user login api

Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('api.send-otp');
Route::post('/verify-otpless', [OtpController::class, 'verifyOtp'])->name('api.verify-otp');
Route::middleware('auth:sanctum')->post('/userLogout', [OtpController::class, 'userLogout']);
Route::post('/login-mobile', [OtpController::class, 'loginWithMobile']);


Route::middleware('auth:sanctum')->get('/order-history', [UserProfileController::class, 'orderHistory']);

//Booking confirm
Route::middleware('auth:sanctum')->post('/booking/confirm', [BookingController::class, 'confirmBooking']);
Route::middleware('auth:sanctum')->post('/process-payment/{booking_id}', [BookingController::class, 'processPayment']);
Route::middleware('auth:sanctum')->post('/process-remaining-payment/{booking_id}', [BookingController::class, 'processRemainingPayment']);

Route::middleware('auth:sanctum')->post('/booking/cancel/{booking_id}', [BookingController::class, 'cancelBooking']);

Route::middleware('auth:sanctum')->get('/mngaddress', [UserProfileController::class, 'manageAddress']);
Route::get('/localities', [UserProfileController::class, 'getActiveLocalities']);

Route::get('/promonations', [UserProfileController::class, 'managepromonation'])->name('api.managepromonations');
Route::middleware('auth:sanctum')->post('/saveaddress', [UserProfileController::class, 'saveAddress']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update-address', [UserProfileController::class, 'updateAddress']);
});
Route::middleware('auth:sanctum')->post('/update-profile', [UserProfileController::class, 'updateUserProfile']);
Route::middleware('auth:sanctum')->post('/update-userphoto', [UserProfileController::class, 'updateUserPhoto']);
Route::middleware('auth:sanctum')->post('/deletePhoto', [UserProfileController::class, 'deletePhoto']);

Route::delete('/user/address/{id}', [UserProfileController::class, 'removeAddress']);

Route::middleware('auth:sanctum')->get('/user/details', [UserProfileController::class, 'getUserDetails']);


Route::middleware('auth:sanctum')->get('/user/details', [UserProfileController::class, 'getUserDetails']);
Route::post('/search', [UserProfileController::class, 'combinedSearch']);

Route::middleware('auth:sanctum')->post('/addresses/{id}/set-default', [UserProfileController::class, 'setDefault'])->name('addresses.setDefault');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/submit-rating', [RatingController::class, 'submitRating']);
    Route::post('/update-rating', [RatingController::class, 'updateRating']);
    Route::get('/rating/{id}', [RatingController::class, 'showRating']);
});

Route::middleware('auth:sanctum')->post('/purchase-subscription', [FlowerBookingController::class, 'purchaseSubscription']);
Route::middleware('auth:sanctum')->post('/subscription/pause/{order_id}', [FlowerBookingController::class, 'pause'])->name('subscription.pause');
Route::middleware('auth:sanctum')->post('/subscription/resume/{order_id}', [FlowerBookingController::class, 'resume'])->name('subscription.resume');

Route::middleware('auth:sanctum')->post('/make-payment/{id}', [FlowerBookingController::class, 'markPaymentApi']);


Route::middleware('auth:sanctum')->post('/flower-requests', [FlowerBookingController::class, 'storerequest']);
Route::middleware('auth:sanctum')->get('/orders-list', [FlowerBookingController::class, 'ordersList']);


// product api

Route::middleware('auth:sanctum')->post('/product-subscription', [ProductApiController::class, 'productSubscription']);
Route::middleware('auth:sanctum')->post('/product-requests', [ProductApiController::class, 'productRequest']);
Route::middleware('auth:sanctum')->post('/make-request-payment/{id}', [ProductApiController::class, 'makeRequestPayment']);
Route::middleware('auth:sanctum')->get('/product-orders-list', [ProductApiController::class, 'ProductOrdersList']);




