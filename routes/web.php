<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\userController;
use App\Http\Controllers\Admin\PujaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TitleController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PodcastController;


use App\Http\Controllers\Pandit\SkillController;
use App\Http\Controllers\Pandit\CareerController;
use App\Http\Controllers\Pandit\PanditController;
use App\Http\Controllers\Pandit\ProfileController;
use App\Http\Controllers\Pandit\PoojaDetailsController;
use App\Http\Controllers\Pandit\BankController;
use App\Http\Controllers\Pandit\AddressController;



use App\Http\Controllers\sebayatregisterController;
use App\Http\Controllers\Auth\LoginRegisterController;


use App\Http\Controllers\Pandit\PoojaListController;
use App\Http\Controllers\Superadmin\SuperAdminController;

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
    Route::get('/userprofile', 'userprofile')->name('userprofile');
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

    Route::controller(PodcastController::class)->group(function() {
        Route::get('/manage-podcast', 'managepodcast')->name('managepodcast');
        Route::get('/add-podcast', 'addpodcast')->name('addpodcast');
        Route::post('/savepodcast', 'savepodcast')->name('savepodcast');
        Route::get('/editpodcast/{podcast}', 'editpodcast')->name('editpodcast');
        Route::post('/updatepodcast/{podcast}', 'updatepodcast')->name('updatepodcast');
        Route::get('/dltpodcast/{podcast}', 'destroy')->name('destroy');
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
        Route::get('/poojaitemlist', 'poojaitemlist')->name('poojaitemlist');
        Route::get('/poojaarea', 'poojaarea')->name('poojaarea');
        Route::get('/poojahistory', 'poojahistory')->name('poojahistory');
        Route::get('/poojarequest', 'poojarequest')->name('poojarequest');
        Route::get('/dashboard', 'dashboard')->name('dashboard');
    });
});

// pandit profile crud operation
Route::group(['prefix' => 'pandit'], function () {
    Route::controller(ProfileController::class)->group(function() {
        Route::get('/profiles', 'panditprofiles')->name('panditprofiles');
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
        Route::put('/updateCareer/{id}', 'updateCareer')->name('updateCareer');
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

Route::group(['prefix' => 'pandit'], function () {
    Route::controller(PoojaListController::class)->group(function() {
        Route::get('/poojaitemlist', 'poojaitemlist')->name('poojaitemlist');
        Route::get('/poojaitem', 'poojaitem')->name('poojaitem');
        Route::post('/save-poojaitemlist', 'savePoojaItemList');
        Route::get('/managepoojaitem', 'managepoojaitem')->name('managepoojaitem');
        Route::get('/delete-poojaitem/{id}','deletePoojaItem')->name('deletePoojaItem');
    });
});
