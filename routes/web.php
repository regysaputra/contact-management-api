<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/docs', function () {
    return view('dist/docs');
});

Route::get('/phpmyinfo', function () {
    phpinfo();
})->name('phpmyinfo');
Route::post('/api/users', [\App\Http\Controllers\UserController::class, 'register']);
Route::post('/api/users/login', [\App\Http\Controllers\UserController::class, 'login']);

Route::middleware(\App\Http\Middleware\ApiAuthMiddleware::class)->group(function() {
    Route::get('/api/users/current', [\App\Http\Controllers\UserController::class, 'get']);
    Route::patch('/api/users/current', [\App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/api/users/logout', [\App\Http\Controllers\UserController::class, 'logout']);
    Route::post('/api/contacts', [\App\Http\Controllers\ContactController::class, 'create']);
    Route::get('/api/contacts', [\App\Http\Controllers\ContactController::class, 'search']);
    Route::get('/api/contacts/{id}', [\App\Http\Controllers\ContactController::class, 'get'])->where('id', '[0-9]+');
    Route::patch('/api/contacts/{id}', [\App\Http\Controllers\ContactController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/api/contacts/{id}', [\App\Http\Controllers\ContactController::class, 'destroy'])->where('id', '[0-9]+');
    Route::post('/api/contacts/{idContact}/addresses', [\App\Http\Controllers\AddressController::class, 'create'])->where('idContact', '[0-9]+');
    Route::get('/api/contacts/{idContact}/addresses', [\App\Http\Controllers\AddressController::class, 'index']);
    Route::get('/api/contacts/{idContact}/addresses/{idAddress}', [\App\Http\Controllers\AddressController::class, 'get'])
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
    Route::patch('/api/contacts/{idContact}/addresses/{idAddress}', [\App\Http\Controllers\AddressController::class, 'update'])
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
    Route::delete('/api/contacts/{idContact}/addresses/{idAddress}', [\App\Http\Controllers\AddressController::class, 'destroy'])
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
});
