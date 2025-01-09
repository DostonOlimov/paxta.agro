<?php

use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'v1',
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'auth:sanctum' // Add your middleware here
], function () {
    Route::apiResource('applications', ApplicationController::class);
    Route::apiResource('states', StateController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('factories', FactoryController::class);
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('crop_types', CropTypeController::class);
    Route::apiResource('crop_generations', CropGenerationController::class);
    Route::apiResource('crop_names', CropNameController::class);
    Route::apiResource('crop_data', CropDataController::class);

    Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login'])->withoutMiddleware('auth:sanctum');
});

Route::group([
    'prefix' => 'v1',
    'namespace' => 'App\Http\Controllers\Api\V1\Vue',
], function () {
    Route::get('/get-state-report', [App\Http\Controllers\Api\V1\Vue\ReportsController::class, 'getReportByState'])->name('reports.getReportByState');
});
