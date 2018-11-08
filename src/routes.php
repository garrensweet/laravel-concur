<?php

use Illuminate\Support\Facades\Route;

Route::prefix('concur')->middleware('api:concur')->namespace('Vdpoel\\Concur')->group(function () {
    Route::get('/profile', 'ProfileController@show');
});
