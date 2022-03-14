<?php

use EscolaLms\StationaryEvents\Http\Controllers\StationaryEventAdminApiController;
use EscolaLms\StationaryEvents\Http\Controllers\StationaryEventApiController;
use Illuminate\Support\Facades\Route;

// admin endpoints
Route::group(['middleware' => ['auth:api'], 'prefix' => 'api/admin/stationary-events'], function () {
    Route::get(null, [StationaryEventAdminApiController::class, 'index']);
    Route::post(null, [StationaryEventAdminApiController::class, 'store']);
    Route::post('{id}', [StationaryEventAdminApiController::class, 'update']);
    Route::get('{id}', [StationaryEventAdminApiController::class, 'show']);
    Route::delete('{id}', [StationaryEventAdminApiController::class, 'delete']);
});

// public routes
Route::group(['prefix' => 'api/stationary-events'], function () {
    Route::get(null, [StationaryEventApiController::class, 'index']);
    Route::get('{id}', [StationaryEventApiController::class, 'show']);
});
