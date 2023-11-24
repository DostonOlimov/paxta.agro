<?php

Route::post('auction/requests', 'AuctionRequestsController');
Route::post('agriculture/requests', 'AgricultureRequestsController');
Route::post('minstroy/requests', 'MinstroyRequestsController');

Route::post('dsi/pdf', 'DsiController@store');
Route::get('dsi/pdf/{id}', 'DsiController@show');

Route::post('notary/tech-info', 'NotaryController@techInfo');
Route::post('notary/customer-info', 'NotaryController@customerInfo');
Route::post('notary/ban', 'NotaryController@ban');
Route::post('notary/unban', 'NotaryController@unban');
Route::post('notary/action', 'NotaryController@action');
Route::post('notary/cancel-action', 'NotaryController@cancelAction');

Route::post('uzauto/transactions', 'UzautoRequestsController@transactions');
