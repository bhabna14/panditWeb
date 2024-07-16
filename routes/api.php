<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\PujaController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CareersController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PoojaListController;
use App\Http\Controllers\Api\PoojaSkillController;
use App\Http\Controllers\Api\PoojaDetailsController;
use App\Http\Controllers\Api\PanditController;
use App\Http\Controllers\Api\UserLoginController;

use App\Http\Controllers\Api\UserProfileController;


use App\Http\Controllers\Api\BookingController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(LoginController::class)->group(function() {
    Route::post('/store-login-data','storeLoginData');
    Route::post('/check-otp','checkOtp');
});

Route::controller(AreaController::class)->group(function() {
    Route::get('/get-districts/{stateCode}', 'getDistrict');
    Route::get('/get-subdistricts/{districtCode}', 'getSubdistrict');
    Route::get('/get-village/{subdistrictsCode}', 'getVillage');
    Route::post('/save-poojaArea', 'saveForm');
    Route::get('/manage-poojaArea', 'manageArea');
});

Route::controller(ProfileController::class)->group(function() {
    Route::post('/profile/save', 'saveProfile');
    Route::post('/profile/update/{id}','updateProfile');
});

Route::controller(CareerController::class)->group(function() {
    Route::post('/career/save', 'savecareer');
});

Route::middleware('auth:api')->post('/career', [CareerController::class, 'savecareer']);


Route::controller(PoojaSkillController::class)->group(function() {
    Route::get('/poojaskill',  'index');
    Route::post('/poojaskill/save', 'saveSkillPooja');
});

Route::controller(PoojaDetailsController::class)->group(function() {
    Route::get('/poojadetails','getPoojaDetails');
    Route::post('/poojadetails/save', 'savePoojadetails');
    Route::get('/managepoojadetails', 'managePoojaDetails');
    Route::post('/updatePoojadetails', 'updatePoojadetails');
});

Route::controller(PoojaListController::class)->group(function() {
    Route::get('/poojaitemlist', 'poojaItemList');
    Route::get('/singlepoojaitem','singlePoojaItem');
    Route::delete('/delet-pooja-items/{id}', 'deletePoojaItem');
    Route::post('/save-pooja-item-list',  'savePoojaItemList');
    Route::post('/update-pooja-items/{id}',  'updatePoojaitem');

});

Route::controller(BankController::class)->group(function() {
    Route::post('/pandit/savebankdetails', 'saveBankDetails');
});

Route::controller(AddressController::class)->group(function() {
    Route::post('/pandit/saveaddress', 'saveAddress');
});

Route::controller(PodcastController::class)->group(function() {
    Route::get('podcasts', 'podcasts');
});


///home page apis
Route::controller(PujaController::class)->group(function() {
    Route::get('poojalists', 'poojalists');
    Route::get('upcomingpoojalists', 'upcomingpoojalists'); 
    Route::get('homepage', 'homepage'); 
    Route::get('panditlists', 'panditlist'); 
});

// single page apis
Route::get('/our-pandit/{slug}', [PanditController::class, 'singlePanditDetails']);
Route::get('/pooja/{slug}', [PanditController::class, 'poojadetails']);

//user login api
Route::controller(UserLoginController::class)->group(function() {
    Route::post('/login','storeLoginData');
    Route::post('/verify-otp','checkUserOtp');
});

Route::middleware('auth:sanctum')->get('/order-history', [UserProfileController::class, 'orderHistory']);

//Booking confirm
Route::middleware('auth:sanctum')->post('/booking/confirm', [BookingController::class, 'confirmBooking']);
