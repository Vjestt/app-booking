<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('/home', HomeController::class);

Route::get('/home/{id}', [HomeController::class, 'detail_ticket']);

Route::post('/booking', [HomeController::class, 'store']);

Route::get('/booking/{bookingTrxId}/{phoneNumber}', [HomeController::class, 'getBookingDetail']);


