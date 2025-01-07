<?php

use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
Route::post('refresh', [AuthController::class, 'refresh'])->middleware('jwt.auth');
Route::get('me', [AuthController::class, 'me'])->middleware('jwt.auth');