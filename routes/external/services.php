<?php

Route::get('invoices', 'InvoicesController@index');
Route::post('invoices', 'InvoicesController@store');
Route::get('invoices/{id}', 'InvoicesController@show');
Route::delete('invoices/{id}', 'InvoicesController@destroy');


Route::get('customers/{tax_id}/has-vehicles', 'CustomersController@hasVehicles');
Route::get('customers', 'CustomersController@index');
Route::post('customers/physical', 'CustomersController@storePhysical');
Route::post('customers/legal', 'CustomersController@storeLegal');

Route::get('vehicles', 'VehiclesController@index');
Route::get('customers/{customerIdentifier}/vehicles', 'VehiclesController@show');

Route::get('driver-licenses', 'DriverLicensesController@index');
Route::get('driver-licenses/{id}', 'DriverLicensesController@details');
Route::get('customers/{customerIdentifier}/driver-license', 'DriverLicensesController@show');


Route::post('uploads', 'FileUploadsController@store');

Route::get('common/regions', 'CommonController@regions');
Route::get('common/areas', 'CommonController@areas');
Route::get('common/ownership-forms', 'CommonController@ownershipForms');
Route::get('common/customer-categories', 'CommonController@customerCategories');
Route::post('agroin/letter', 'Uzagroin\ApplicationController@letter')->name('agroin.letter');
Route::post('agroin/appeal', 'Uzagroin\ApplicationController@appeal')->name('agroin.appeal');
Route::get('tm-agroteh/driver-licence', 'Uzagroin\ApplicationController@licence')->name('driver.licence');
