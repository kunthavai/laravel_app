<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth')->group(function () {
        //Route::get('/me', fn() => response()->json(auth()->user()));
        Route::post('/logout', [AuthController::class, 'logout']);
    });

});