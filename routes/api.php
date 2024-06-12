<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\PujaController;
use App\Http\Controllers\Api\PoojaSkillController;
use App\Http\Controllers\Api\PoojaDetailsController;
use App\Http\Controllers\Api\PoojaListController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ProfileController::class)->group(function() {
    Route::post('/profile/save', 'saveProfile');
    Route::post('/career/save', 'saveCareer');
});
Route::controller(CareerController::class)->group(function() {
    Route::post('/savecareer', 'saveCareer');

});

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

Route::controller(PodcastController::class)->group(function() {
    Route::get('podcasts', 'podcasts');
});

Route::controller(PujaController::class)->group(function() {
    Route::get('poojalists', 'poojalists');
    Route::get('upcomingpoojalists', 'upcomingpoojalists'); 
    Route::get('homepage', 'homepage'); 

});