<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/v1'  , 'middleware'=> ['allow.cors']], function () {

    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::group(['prefix' => '/orders', 'middleware'=>['auth:sanctum' , 'throttle:3600,3' ]], function () {
        Route::post('/', [App\Http\Controllers\OrderController::class, 'create']);
    });
});


