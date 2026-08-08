<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return view('hello');
});

Route::get('/farkle', function () {
    return view('farkle');
});

// Route::get('/farkle', [GameController::class, 'roll']);