<?php

use Illuminate\Support\Facades\Route;

// admin endpoints
Route::group(['middleware' => ['auth:api'], 'prefix' => 'api/admin'], function () {
});

// user endpoints
Route::group(['middleware' => ['auth:api'], 'prefix' => 'api'], function () {
});
