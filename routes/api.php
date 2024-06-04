<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/v1'], function () {

    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::group(['prefix' => '/order', 'middleware'=>['auth:sanctum' , 'throttle:3600,3' ]], function () {
        Route::post('/create', [App\Http\Controllers\OrderController::class, 'create']);
    });
});


