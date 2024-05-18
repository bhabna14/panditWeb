<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\sebayatregisterController;
use App\Http\Controllers\User\userController;
use App\Http\Controllers\Superadmin\SuperAdminController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PujaController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\TitleController;
use App\Http\Controllers\Admin\OrderController;

use App\Http\Controllers\Pandit\PanditController;

## user login
Route::controller(userController::class)->group(function() {
    Route::get('/register', 'userregister')->name('user-register');
    Route::post('store', 'store')->name('store');
    Route::get('/', 'userindex')->name('userindex');
    Route::get('/book-pandit', 'bookpandit')->name('bookpandit');
    Route::get('/pooja-list', 'poojalist')->name('poojalist');
    Route::get('/puja-details', 'poojadetails')->name('poojadetails');
    Route::get('/pandit-details', 'panditdetails')->name('panditdetails');
    Route::get('/book-now', 'booknow')->name('booknow');
    Route::get('/about-us', 'aboutus')->name('aboutus');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/my-profile', 'myprofile')->name('myprofile');
    Route::get('/manage-address', 'mngaddress')->name('mngaddress');
    Route::get('/add-address', 'addaddress')->name('addaddress');
    Route::get('/order-history', 'orderhistory')->name('orderhistory');
    Route::get('/rate-pooja', 'ratepooja')->name('ratepooja');
    Route::get('/view-ordered-pooja-details', 'viewdetails')->name('viewdetails');
    Route::get('/userprofile', 'settings')->name('settings');
    Route::get('/coupons', 'coupons')->name('coupons');
    Route::get('/login', 'userlogin')->name('userlogin');
    // Route::get('/demo', 'demo')->name('demo');

    Route::post('user/authenticate', 'userauthenticate')->name('userauthenticate');
    Route::post('user/logout', 'userlogout')->name('userlogout');

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
});


##super admin routes
Route::prefix('superadmin')->middleware(['superadmin'])->group(function () {
    Route::controller(SuperAdminController::class)->group(function() {
        
        Route::get('superadmin/dashboard', 'dashboard')->name('dashboard');
    });
    Route::get('/addadmin', [SuperAdminController::class, 'addadmin']);
    Route::post('/saveadmin', [SuperAdminController::class, 'saveadmin']);
    Route::get('/editadmin/{id}', [SuperAdminController::class, 'editadmin'])->name('editadmin');
    Route::post('/update/{id}', [SuperAdminController::class, 'update'])->name('update');
    Route::get('/dltadmin/{id}', [SuperAdminController::class, 'dltadmin'])->name('dltadmin');

    Route::get('/adminlist', [SuperAdminController::class, 'adminlist']);
});

## admin routes
Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::controller(AdminController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/manage-pandits', 'managepandit')->name('managepandit');
        Route::get('/pandit-profile', 'panditprofile')->name('panditprofile');
        Route::get('/manage-users', 'manageuser')->name('manageuser');
        Route::get('/user-profile', 'userprofile')->name('userprofile');

    
    });
    Route::controller(PujaController::class)->group(function() {
        Route::get('/manage-puja', 'managePuja')->name('managepuja');
        Route::get('/add-puja', 'addpuja')->name('addpuja');
        Route::get('/savepuja', 'savepuja')->name('savepuja');

        Route::get('/manage-puja-list', 'managePujaList')->name('managePujaList');
        Route::get('/add-puja-list', 'addpujalist')->name('addpujalist');
    });
    Route::controller(LocationController::class)->group(function() {
        Route::get('/manage-location', 'managelocation')->name('managelocation');
        Route::get('/add-location', 'addlocation')->name('addlocation');
        Route::get('/savelocation', 'savelocation')->name('savelocation');
    });

    Route::controller(LanguageController::class)->group(function() {
        Route::get('/manage-lang', 'managelang')->name('managelang');
        Route::get('/add-language', 'addlang')->name('addlang');
        // Route::get('/savelocation', 'savelocation')->name('savelocation');
    });

    Route::controller(TitleController::class)->group(function() {
        Route::get('/manage-title', 'managetitle')->name('managetitle');
        Route::get('/add-title', 'addtitle')->name('addtitle');
        // Route::get('/savelocation', 'savelocation')->name('savelocation');
    });

    Route::controller(OrderController::class)->group(function() {
        Route::get('/manage-orders', 'manageorders')->name('manageorders');
        // Route::get('/savelocation', 'savelocation')->name('savelocation');
    });
   
});



// user routes
Route::prefix('user')->middleware(['user'])->group(function () {
   
    Route::controller(userController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('user.dashboard');
        
    });
});
/// pandit routes
Route::group(['prefix' => 'pandit'], function () {
    Route::controller(PanditController::class)->group(function() {
        Route::get('/panditlogin', 'panditlogin');
        Route::get('/career', 'profilecareer')->name('profilecareer');
        Route::get('/profiles', 'panditprofiles')->name('panditprofiles');
        Route::get('/profile', 'panditprofile')->name('panditprofile');
        Route::get('/dashboard', 'panditdashboard')->name('panditdashboard');
        Route::get('/poojarequest', 'poojarequest')->name('poojarequest');
        Route::get('/poojahistory', 'poojahistory')->name('poojahistory');
        Route::get('/poojaexperties', 'poojaexperties')->name('poojaexperties');
        Route::get('/poojadetails', 'poojadetails')->name('poojadetails');
        Route::get('/poojalist', 'poojalist')->name('poojalist');
        Route::get('/bank', 'bank')->name('bank');
        Route::get('/address', 'panditaddress')->name('panditaddress');
        Route::get('/get-states/{countryId}', 'getStates');
        Route::get('/get-city/{stateId}', 'getCity');
        Route::get('/savea', 'storeMultipleLocations')->name('storeMultipleLocations');
    });
});
// pandit profile crud operation

Route::group(['prefix' => 'pandit'], function () {
    Route::controller(PanditController::class)->group(function() {
        Route::post('/save-profile', 'saveprofile');
    });
});
// pandit career crud operation
Route::group(['prefix' => 'pandit'], function () {
    Route::controller(PanditController::class)->group(function() {
        Route::post('/save-career', 'savecareer');
    });
});