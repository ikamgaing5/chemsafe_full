<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/', function () {
    return view('welcome');
});
Route::get( '/login', [UserController::class, 'essaie'])->name('login');
Route::get('v1/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
