<?php

use Illuminate\Support\Facades\Route;

Route::prefix('concur')->middleware('api:concur')->namespace('VdPoel\\Concur\\Http\\Controllers')->group(function () {
    Route::get('/profile', 'ProfileController@show');
    Route::get('/redirect', 'ProfileController@redirect');
});
