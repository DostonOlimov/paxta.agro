<?php

Route::resource('ownership-forms', 'OwnershipFormsController');
Route::put('vehicle-prohibitions/unlock', 'VehicleProhibitionsController@unlock')->name('vehicle-prohibitions.unlock');
Route::get('vehicle-registrations', 'VehicleRegistrationsController@createRegistration')
    ->name('vehicle-registrations.create');
Route::post('vehicle-registrations', 'VehicleRegistrationsController@appendRegistration')
    ->name('vehicle-registrations.store');

Route::get('unregistered-vehicles', 'VehiclesController@unregistered')->name('unregistered-vehicles.list');
