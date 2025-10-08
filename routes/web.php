<?php
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;


Route::get('/', [BookingController::class, 'index']);
Route::post('/check-rooms', [BookingController::class, 'checkRooms']);
Route::post('/confirm', [BookingController::class, 'confirm']);
Route::get('/thank-you/{id}', [BookingController::class, 'thankYou']);
Route::get('/disabled-dates', [BookingController::class, 'disabledDates']);