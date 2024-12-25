<?php

use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;



Route::post('login', LoginController::class);
Route::post('register', RegisterController::class);
Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('links', LinkController::class);
});
