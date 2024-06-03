<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\PujaController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ProfileController::class)->group(function() {
    Route::post('save-profile', 'store');
});

Route::controller(ProfileController::class)->group(function() {
    Route::post('save-career', 'savecareer');
});

Route::controller(PodcastController::class)->group(function() {
    Route::get('podcasts', 'podcasts');
});




Route::controller(PujaController::class)->group(function() {
    Route::get('poojalists', 'poojalists');
    Route::get('upcomingpoojalists', 'upcomingpoojalists'); 
});

Route::controller(PujaController::class)->group(function() {
    Route::get('poojalists', 'poojalists');
    Route::get('upcomingpoojalists', 'upcomingpoojalists'); 
});

