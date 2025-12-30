<?php
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('bookings')->group(function()
{
        // display user bookings
        Route::get('/',[BookingController::class,'index']);
         // user add a new booking
        Route::post('/' ,[BookingController::class ,'store']);
        // display spesofic booking
        Route::get('/{booking}' ,[BookingController::class ,'show']);
        // cancel the booking befor appointment
        Route::delete('/{booking}' ,[BookingController::class , 'cancel']);

});