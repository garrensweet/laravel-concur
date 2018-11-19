<?php

use Illuminate\Support\Facades\Route;
use VdPoel\Concur\Http\Middleware\Authenticate;
use VdPoel\Concur\Http\Middleware\Concur;

Route::prefix('concur')->as('concur.')->namespace('VdPoel\\Concur\\Http\\Controllers')->middleware([Authenticate::class, Concur::class])->group(function () {
    Route::prefix('travel/profile')->as('travel.profile.')->group(function () {
        Route::get('lookup', 'ConcurController@lookupTravelProfile')->name('lookup');
        Route::get('create', 'ConcurController@createTravelProfile')->name('create');
    });

    Route::get('signin', 'ConcurController@signin')->name('signin');
});
