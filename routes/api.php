<?php

use App\Http\Controllers\Api\AppOnlineController;
use App\Http\Controllers\Api\CertConnetionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/users', function (Request $request){
    return $request->users;
});

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'],function () {
    Route::apiResource('applications' , ApplicationController::class);
    Route::apiResource('states' , StateController::class);
    Route::apiResource('cities' , CityController::class);
    Route::apiResource('factories' , FactoryController::class);
    Route::apiResource('companies' , CompanyController::class);
});

Route::post('login', [CertConnetionController::class, 'login']);

Route::middleware('auth:api')->group(function (){
    Route::get('cropName', [CertConnetionController::class, 'crop_name']);
    Route::get('cropType', [CertConnetionController::class, 'crop_type']);
    Route::post('org_compy_view', [CertConnetionController::class, 'org_compy_view']);
    Route::post('org_compy_edit', [CertConnetionController::class, 'org_compy_edit']);
    Route::post('organization_company', [CertConnetionController::class, 'organization_company']);
    Route::post('prepared_company', [CertConnetionController::class, 'prepared_company']);
    Route::post('app_add', [AppOnlineController::class, 'app_add']);
    Route::get('apps_user', [AppOnlineController::class, 'apps_user']);
    Route::post('app_view', [AppOnlineController::class, 'app_view']);
    Route::post('app_edit', [AppOnlineController::class, 'app_edit']);
    Route::delete('app_delete', [AppOnlineController::class, 'app_delete']);
    Route::post('app_file', [AppOnlineController::class, 'app_file']);
    Route::post('app_file_update', [AppOnlineController::class, 'app_file_update']);
    Route::get('app_file_find/{id}', [AppOnlineController::class, 'app_file_find']);
});
