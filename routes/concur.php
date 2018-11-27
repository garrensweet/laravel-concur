<?php

use Illuminate\Support\Facades\Route;

Route::namespace('VdPoel\\Concur\\Http\\Controllers')->prefix('concur')->as('concur.')->group(function () {
    Route::get('/', 'ConcurController@index')->name('index');
});
