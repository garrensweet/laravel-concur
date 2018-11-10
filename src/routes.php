<?php

use Illuminate\Support\Facades\Route;

Route::prefix('concur')->as('concur.')->namespace('VdPoel\\Concur\\Http\\Controllers')->middleware(['concur'])->group(function () {
    Route::get('authenticate', 'ConcurController@authenticate')->name('authenticate');
    Route::get('signin', 'ConcurController@signin')->name('signin');
    Route::get('register', 'ConcurController@register')->name('register');
});
