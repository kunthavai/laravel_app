<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserModuleController;

Route::prefix('user-modules')->group(function () {    

    Route::middleware('auth:api')->group(function () {        
        Route::post('/createOrUpdate', [UserModuleController::class, 'createOrUpdate']);
        Route::get('/listSequentialModules/{course_id}', [UserModuleController::class, 'listSequentialModules']);
        Route::get('/getCourseProgress/{course_id}', [UserModuleController::class, 'getCourseProgress']);
        Route::get('/getLatestAccessedModule/{course_id}', [UserModuleController::class, 'getLatestAccessedModule']);
        Route::get('/getTopUsersByCourse/{course_id}', [UserModuleController::class, 'getTopUsersByCourse']);
    });

});