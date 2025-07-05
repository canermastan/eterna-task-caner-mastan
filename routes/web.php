<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEB Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

// Catch-all route for Vue Router SPA
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
