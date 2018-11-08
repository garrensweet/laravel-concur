<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api:concur'], 'prefix' => 'concur', 'namespace' => 'Vdpoel\Concur'], function() {
    Route::get('/profile', 'ProfileController@show');
});