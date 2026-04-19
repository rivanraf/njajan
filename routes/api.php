<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Route ini diekspos di /api/... dan melewati middleware session/CSRF.
| Cocok untuk menerima webhook eksternal seperti dari Midtrans.
|
*/

Route::post('/midtrans-callback', [OrderController::class, 'handleNotification']);
