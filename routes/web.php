<?php

use App\Http\Controllers\FCMController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/register-token', [ FCMController::class, 'registerToken'])->name('register-token');
