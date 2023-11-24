<?php

Route::get('dsi-request/{pdfId}', [\App\Http\Controllers\Api\Common\DsiController::class, 'show'])->name('.dsi-request');
Route::get('dsi-request/{pdfId}/download', [\App\Http\Controllers\Api\Common\DsiController::class, 'download'])->name('.download-dsi-pdf');

Route::get('/vehicles/{vehicleId}/tm/{tmId}/', [\App\Http\Controllers\Api\Common\TmController::class, 'show'])->name('.tm1');
Route::get('/agroin-appeal/{appealId}/letter{letterId}', [\App\Http\Controllers\Api\Common\AgroinController::class, 'download'])->name('.letter');
Route::post('check-prohibition', [\App\Http\Controllers\Api\Common\ProhibitionController::class, 'check'])->name('.check-prohibition');

Route::get('vehicle-registration/{id}', [\App\Http\Controllers\Api\Common\ProhibitionController::class, 'check'])->name('.vehicle-registration');
Route::get('/technical-passport/id{passportId}', [\App\Http\Controllers\Api\Common\TechnicalController::class, 'download'])->name('.technical');
Route::get('/technical-certificate/id{certificateID}', [\App\Http\Controllers\Api\Common\TechnicalController::class, 'download2'])->name('.certificate');
