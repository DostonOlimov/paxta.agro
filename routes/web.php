<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Customercontroller;
use App\Http\Controllers\LaboratoryOperatorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

//Dashboard
Route::get('/home', ['middleware' => 'auth', 'uses' => '\App\Http\Controllers\HomeController@dashboard']);
Route::get('/home2', ['middleware' => 'auth', 'uses' => '\App\Http\Controllers\HomeController@dashboard']);
//Route::get('/', ['middleware' => 'auth','\App\Http\Controllers\DashboardController@index'])->name('home');
Route::get('/', ['middleware' => 'auth', 'uses' => '\App\Http\Controllers\HomeController@dashboard']);
Route::post('/change-language', [\App\Http\Controllers\LanguageController::class, 'changeLanguage']);

//profile
Route::get('/full-report', '\App\Http\Controllers\ReportController@report')->name('report.full_report');
Route::get('/report', '\App\Http\Controllers\ReportController@myreport')->name('report.report');
Route::get('/export','\App\Http\Controllers\ReportController@excel_export')->name('excel.export');
Route::get('/export_company','\App\Http\Controllers\ReportController@export_company')->name('export.company');
Route::get('/organization-company-report', '\App\Http\Controllers\ReportController@company_report')->name('report.company_report');
Route::get('/prepared-company-report', '\App\Http\Controllers\ReportController@prepared_report')->name('report.prepared_report');

    Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
        Route::get('/list', 'Usercontroller@userslist');
        Route::get('/list/{id}', 'Usercontroller@usershow');
        Route::get('/get', 'Usercontroller@get_users');
    });
//employee modulea
Route::get('/attachments/{id}/download', '\App\Http\Controllers\AttachmentsController@download')->name('attachment.download');

Route::group(['prefix' => 'employee'], function () {
    Route::get('/list', ['as' => 'listemployeee', 'uses' => '\App\Http\Controllers\employeecontroller@employeelist']);
    Route::get('/add', ['as' => 'addemployeee', 'uses' => '\App\Http\Controllers\employeecontroller@addemployee']);
    Route::post('/store', ['as' => 'storeemployeee', 'uses' => '\App\Http\Controllers\employeecontroller@store']);
    Route::get('/edit/{id}', ['as' => 'editemployeee', 'uses' => '\App\Http\Controllers\employeecontroller@edit']);
    Route::patch('/edit/update/{id}', '\App\Http\Controllers\employeecontroller@update');
    Route::get('/view/{id}', '\App\Http\Controllers\employeecontroller@showemployer');
    Route::get('/list/delete/{id}', ['as' => '/employee/list/delete/{id}', 'uses' => '\App\Http\Controllers\employeecontroller@destory']);
    Route::get('/free_service', ['as' => '/employee/free_service', 'uses' => '\App\Http\Controllers\employeecontroller@free_service']);
    Route::get('/paid_service', ['as' => '/employee/paid_service', 'uses' => '\App\Http\Controllers\employeecontroller@paid_service']);
    Route::get('/repeat_service', ['as' => '/employee/repeat_service', 'uses' => '\App\Http\Controllers\employeecontroller@repeat_service']);
});

//Country City State ajax
    Route::get('/getstatefromcountry', '\App\Http\Controllers\CountryAjaxcontroller@getstate');
    Route::get('/getcityfromstate', '\App\Http\Controllers\CountryAjaxcontroller@getcity')->name('areas.list');
    Route::get('/getcities', '\App\Http\Controllers\CountryAjaxcontroller@getcitiesjson');
    Route::post('/edit-city', '\App\Http\Controllers\CountryAjaxcontroller@edit_city');
    Route::post('/add-city', '\App\Http\Controllers\CountryAjaxcontroller@add_city');
    Route::get('/getcityfromsearch', '\App\Http\Controllers\CountryAjaxcontroller@getcityfromsearch');
    Route::post('/update-state', '\App\Http\Controllers\CountryAjaxcontroller@update_state');

    //Craps ajax
    Route::get('/gettypefromname', '\App\Http\Controllers\CropAjaxController@gettype');
    Route::get('/getgenerationfromname', '\App\Http\Controllers\CropAjaxController@getgeneration');
    Route::get('/getkodtnved/{id}', '\App\Http\Controllers\CropAjaxController@getkodtnved');
    Route::get('/getcompany', '\App\Http\Controllers\CropAjaxController@getcompany')->name('get.company');

    Route::get('/process-excel', '\App\Http\Controllers\CropAjaxController@processExcel');

// Cities
    Route::group(['prefix' => 'cities', 'middleware' => 'auth'], function () {
    Route::get('/add', '\App\Http\Controllers\CitiesController@index');
    Route::get('/list', '\App\Http\Controllers\CitiesController@list');
    Route::post('/store', '\App\Http\Controllers\CitiesController@store');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\CitiesController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\CitiesController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\CitiesController@update');
    });

    //States
    Route::group(['prefix' => 'states', 'middleware' => 'auth'], function () {
    Route::get('/add', '\App\Http\Controllers\StatesController@index');
    Route::get('/list', '\App\Http\Controllers\StatesController@list');
    Route::post('/store', '\App\Http\Controllers\StatesController@store');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\StatesController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\StatesController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\StatesController@update');
    });

    //Organization Companies
    Route::group(['prefix' => 'organization', 'middleware' => 'auth'], function () {
    Route::get('/add/{id}', '\App\Http\Controllers\OrganizationCompaniesController@add');
    Route::get('/view/{id}', '\App\Http\Controllers\OrganizationCompaniesController@show');
    Route::get('/list', '\App\Http\Controllers\OrganizationCompaniesController@list');
    Route::post('/store', '\App\Http\Controllers\OrganizationCompaniesController@store');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\OrganizationCompaniesController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\OrganizationCompaniesController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\OrganizationCompaniesController@update');
    Route::get('/search_by_name', '\App\Http\Controllers\OrganizationCompaniesController@search');
    });
    //Prepared Companies
    Route::group(['prefix' => 'prepared', 'middleware' => 'auth'], function () {
    Route::get('/add/{id}', '\App\Http\Controllers\PreparedCompaniesController@add');
    Route::get('/list', '\App\Http\Controllers\PreparedCompaniesController@list');
    Route::post('/store', '\App\Http\Controllers\PreparedCompaniesController@store');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\PreparedCompaniesController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\PreparedCompaniesController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\PreparedCompaniesController@update');
    Route::get('/search_by_name', '\App\Http\Controllers\PreparedCompaniesController@search');

    });
    //Laboratories
    Route::group(['prefix' => 'laboratories', 'middleware' => 'auth'], function () {
        Route::get('/add', '\App\Http\Controllers\LaboratoriesController@add');
        Route::get('/view', '\App\Http\Controllers\LaboratoriesController@show');
        Route::get('/list', '\App\Http\Controllers\LaboratoriesController@list');
        Route::post('/store', '\App\Http\Controllers\LaboratoriesController@store');
        Route::get('/list/delete/{id}', '\App\Http\Controllers\LaboratoriesController@destory');
        Route::get('/list/edit/{id}', '\App\Http\Controllers\LaboratoriesController@edit');
        Route::post('/list/edit/update/{id}', '\App\Http\Controllers\LaboratoriesController@update');
        Route::get('/search_by_name', '\App\Http\Controllers\LaboratoriesController@search');
    });
    //In xaus
    Route::group(['prefix' => 'in_xaus', 'middleware' => 'auth'], function () {
        Route::get('/list', '\App\Http\Controllers\InXausController@in_xaus_list');
        Route::get('/add', '\App\Http\Controllers\InXausController@add');
        Route::get('/list/delete/{id}', '\App\Http\Controllers\InXausController@destory');
        Route::get('/edit/{id}', '\App\Http\Controllers\InXausController@edit');
        Route::post('/edit/update/{id}', '\App\Http\Controllers\InXausController@update');
        Route::get('/view/{id}', '\App\Http\Controllers\InXausController@view');
        Route::get('/view2/{id}/{i}', '\App\Http\Controllers\InXausController@view2');
        Route::post('/store', '\App\Http\Controllers\InXausController@store');
    });
    //Klassiyor
    Route::group(['prefix' => 'klassiyor', 'middleware' => 'auth'], function () {
        Route::get('/add', '\App\Http\Controllers\KlassiyorController@index');
        Route::get('/list', '\App\Http\Controllers\KlassiyorController@list');
        Route::post('/store', '\App\Http\Controllers\KlassiyorController@store');
        Route::get('/list/delete/{id}', '\App\Http\Controllers\KlassiyorController@destory');
        Route::get('/list/edit/{id}', '\App\Http\Controllers\KlassiyorController@edit');
        Route::post('/list/edit/update/{id}', '\App\Http\Controllers\KlassiyorController@update');
    });
    //Crops name
    Route::group(['prefix' => 'crops_name', 'middleware' => 'auth'], function () {
    Route::get('/add', '\App\Http\Controllers\CropsNameController@index');
    Route::get('/list', '\App\Http\Controllers\CropsNameController@list');
    Route::post('/store', '\App\Http\Controllers\CropsNameController@store');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\CropsNameController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\CropsNameController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\CropsNameController@update');
    });
    //Crops type
    Route::group(['prefix' => 'crops_type', 'middleware' => 'auth'], function () {
    Route::get('/add', '\App\Http\Controllers\CropsTypeController@index');
    Route::get('/list', '\App\Http\Controllers\CropsTypeController@list');
    Route::post('/store', '\App\Http\Controllers\CropsTypeController@store');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\CropsTypeController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\CropsTypeController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\CropsTypeController@update');
    });
    //Crops generation
    Route::group(['prefix' => 'crops_generation', 'middleware' => 'auth'], function () {
    Route::get('/add', '\App\Http\Controllers\CropsGenerationController@index');
    Route::get('/list', '\App\Http\Controllers\CropsGenerationController@list');
    Route::post('/store', '\App\Http\Controllers\CropsGenerationController@store');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\CropsGenerationController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\CropsGenerationController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\CropsGenerationController@update');
    });
    //Crops selection
    Route::group(['prefix' => 'crops_selection', 'middleware' => 'auth'], function () {
    Route::get('/add', '\App\Http\Controllers\CropsSelectionController@index');
    Route::get('/list', '\App\Http\Controllers\CropsSelectionController@list');
    Route::post('/store', '\App\Http\Controllers\CropsSelectionController@store');
    Route::get('/search_by_name', '\App\Http\Controllers\CropsSelectionController@search_by_name');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\CropsSelectionController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\CropsSelectionController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\CropsSelectionController@update');
    });
    //Nds
    Route::group(['prefix' => 'nds', 'middleware' => 'auth'], function () {
    Route::get('/add', '\App\Http\Controllers\NdsController@index');
    Route::get('/list', '\App\Http\Controllers\NdsController@list');
    Route::post('/store', '\App\Http\Controllers\NdsController@store');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\NdsController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\NdsController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\NdsController@update');
    });
    //Indicators
    Route::group(['prefix' => 'indicator', 'middleware' => 'auth'], function () {
    Route::get('/add', '\App\Http\Controllers\IndicatorController@index');
    Route::get('/list', '\App\Http\Controllers\IndicatorController@list');
    Route::post('/store', '\App\Http\Controllers\IndicatorController@store');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\IndicatorController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\IndicatorController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\IndicatorController@update');
    });
    //applications
    Route::group(['prefix' => 'application'], function () {
    Route::get('/list', ['as' => 'listapplication', 'uses' => '\App\Http\Controllers\ApplicationController@applicationlist']);
    Route::get('/my-applications', ['as' => 'myapplications', 'uses' => '\App\Http\Controllers\ApplicationController@myapplications']);

    Route::get('/add', ['as' => 'addapplication', 'uses' => '\App\Http\Controllers\ApplicationController@addapplication']);
    Route::get('/my-application-add', ['as' => 'myapplicationadd', 'uses' => '\App\Http\Controllers\ApplicationController@myapplicationadd']);

    Route::post('/store', ['as' => 'storeapplication', 'uses' => '\App\Http\Controllers\ApplicationController@store']);
    Route::post('/my-application-store', ['as' => 'myapplicationstore', 'uses' => '\App\Http\Controllers\ApplicationController@myapplicationstore']);

    Route::get('/edit/{id}', ['as' => 'editapplication', 'uses' => '\App\Http\Controllers\ApplicationController@edit']);
    Route::get('/my-application-edit/{id}', ['as' => 'myapplicationedit', 'uses' => '\App\Http\Controllers\ApplicationController@myapplicationedit']);

    Route::patch('/edit/update/{id}', '\App\Http\Controllers\ApplicationController@update');
    Route::patch('/my-application-edit/update/{id}', '\App\Http\Controllers\ApplicationController@myapplicationupdate');

    Route::get('/view/{id}', '\App\Http\Controllers\ApplicationController@showapplication');

    Route::get('/list/delete/{id}', ['as' => '/application/list/delete/{id}', 'uses' => '\App\Http\Controllers\ApplicationController@destory']);

    Route::get('/my-application-delete/{id}', ['as' => '/application/myapplicationdelete', 'uses' => '\App\Http\Controllers\ApplicationController@myapplications_delete']);
    Route::get('/accept/{id}', ['as' => '/application/accept', 'uses' => '\App\Http\Controllers\ApplicationController@accept']);
    Route::get('/reject/{id}', ['as' => '/application/reject', 'uses' => '\App\Http\Controllers\ApplicationController@reject']);
    Route::post('/reject/store', ['as' => '/application/rejectstore', 'uses' => '\App\Http\Controllers\ApplicationController@reject_store']);
    });
//Decision
    Route::group(['prefix' => 'decision', 'middleware' => 'auth'], function () {
    Route::get('search','App\Http\Controllers\DecisionController@search');
    Route::get('/add/{id}', '\App\Http\Controllers\DecisionController@add');
    Route::get('/list', '\App\Http\Controllers\DecisionController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\DecisionController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\DecisionController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\DecisionController@update');

    });
    Route::get('decision/create', '\App\Http\Controllers\DecisionController@create')->name('decision.create');
    Route::post('decision/store', '\App\Http\Controllers\DecisionController@store')->name('decision.store');
    Route::get('decision/report', '\App\Http\Controllers\DecisionController@report')->name('decision.report');
    Route::get('decision/report/export', '\App\Http\Controllers\DecisionController@export')->name('decision.report.export');
    Route::resource('decision/payments', '\App\Http\Controllers\PaymentsController', ['as' => 'decision']);
    Route::get('decision/{invoice_id}/serve', '\App\Http\Controllers\DecisionController@serve')->name('decision.serve');
    Route::get('decision/{id}/redo', '\App\Http\Controllers\DecisionController@redo')->name('decision.redo');
    Route::get('decision/view/{id}', '\App\Http\Controllers\DecisionController@view')->name('decision.view');
    Route::get('decision/show/{id}', '\App\Http\Controllers\DecisionController@my_view')->name('decision.show');
    Route::get('decision/send/{id}', '\App\Http\Controllers\DecisionController@send')->name('decision.send');
//Test programs
    Route::group(['prefix' => 'tests', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\TestProgramsController@search');
    Route::get('/add/{id}', '\App\Http\Controllers\TestProgramsController@add');
    Route::get('/list', '\App\Http\Controllers\TestProgramsController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\TestProgramsController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\TestProgramsController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\TestProgramsController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\TestProgramsController@view')->name('tests.view');
    Route::post('/store', '\App\Http\Controllers\TestProgramsController@store')->name('tests.store');
    });
    Route::get('tests/show/{id}', '\App\Http\Controllers\TestProgramsController@my_view')->name('tests.show');

//Dalolatnoma
    Route::group(['prefix' => 'dalolatnoma', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\DalolatnomaController@search');
    Route::get('/myadd', '\App\Http\Controllers\DalolatnomaController@myadd');
    Route::get('/add/{id}', '\App\Http\Controllers\DalolatnomaController@add');
    Route::get('/list', '\App\Http\Controllers\DalolatnomaController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\DalolatnomaController@destory');
    Route::get('/edit/{id}', '\App\Http\Controllers\DalolatnomaController@edit');
    Route::post('/edit/update/{id}', '\App\Http\Controllers\DalolatnomaController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\DalolatnomaController@view')->name('dalolatnoma.view');
    Route::post('/store', '\App\Http\Controllers\DalolatnomaController@store')->name('dalolatnoma.store');

    Route::get('/tara_edit/{id}', '\App\Http\Controllers\DalolatnomaController@tara_edit');
        Route::post('tara_edit/update/{id}', '\App\Http\Controllers\DalolatnomaController@tara_store');
    });
//Akt amount
Route::group(['prefix' => 'akt_amount', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\AktAmountController@search');
    Route::get('/add/{id}', '\App\Http\Controllers\AktAmountController@add');
    Route::get('/list', '\App\Http\Controllers\AktAmountController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\AktAmountController@destory');
    Route::get('/edit/{id}', '\App\Http\Controllers\AktAmountController@edit');
    Route::post('/edit/update/{id}', '\App\Http\Controllers\AktAmountController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\AktAmountController@view')->name('akt_amount.view');
    Route::post('/store', '\App\Http\Controllers\AktAmountController@store')->name('akt_amount.store');
    Route::post('/save-amount', [\App\Http\Controllers\AktAmountController::class, 'save_amount'])->name('save.amount');

});
//Akt laboratory
Route::group(['prefix' => 'akt_laboratory', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\AktLaboratoryController@search')->name('akt_laboratory.search');
    Route::get('/add/{id}', '\App\Http\Controllers\AktLaboratoryController@add');
    Route::get('/list', '\App\Http\Controllers\AktLaboratoryController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\AktLaboratoryController@destory');
    Route::get('/edit/{id}', '\App\Http\Controllers\AktLaboratoryController@edit');
    Route::post('/edit/update/{id}', '\App\Http\Controllers\AktLaboratoryController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\AktLaboratoryController@view')->name('akt_laboratory.view');
    Route::post('/store', '\App\Http\Controllers\AktLaboratoryController@store')->name('akt_laboratory.store');
});
//HVI data
Route::group(['prefix' => 'hvi', 'middleware' => 'auth'], function () {
    Route::get('/add/{id}', '\App\Http\Controllers\HviController@add');
    Route::get('/list', '\App\Http\Controllers\HviController@list');
    Route::get('/view/{id}', '\App\Http\Controllers\HviController@view');
    Route::post('/store', '\App\Http\Controllers\HviController@store')->name('hvi.store');
});
    //Final results
    Route::group(['prefix' => 'final_results', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\FinalResultsController@search');
    Route::get('/add/{id}', '\App\Http\Controllers\FinalResultsController@add');
    Route::get('/add/view/{id}', '\App\Http\Controllers\FinalResultsController@add_view');
    Route::get('/add2/{id}', '\App\Http\Controllers\FinalResultsController@add2');
    Route::get('/list', '\App\Http\Controllers\FinalResultsController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\FinalResultsController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\FinalResultsController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\FinalResultsController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\FinalResultsController@view');
    Route::post('/store', '\App\Http\Controllers\FinalResultsController@store');
    Route::get('/update/{id}', '\App\Http\Controllers\FinalResultsController@update');
    });
//Sertificates
Route::group(['prefix' => 'sertificate', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\SertificateController@search');
    Route::get('/add/{id}', '\App\Http\Controllers\SertificateController@add');
    Route::get('/list', '\App\Http\Controllers\SertificateController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\SertificateController@destory');
    Route::get('/list/edit/{id}', '\App\Http\Controllers\SertificateController@edit');
    Route::post('/list/edit/update/{id}', '\App\Http\Controllers\SertificateController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\SertificateController@view');
    Route::post('/store', '\App\Http\Controllers\SertificateController@store');
});
//final decisions
Route::group(['prefix' => 'final_decision', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\FinalDecisionController@search');
    Route::get('/list', '\App\Http\Controllers\FinalDecisionController@list');
    Route::get('/view/{id}', '\App\Http\Controllers\FinalDecisionController@view');
});
//Namlik
Route::group(['prefix' => 'humidity', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\HumidityController@search');
    Route::get('/add/{id}', '\App\Http\Controllers\HumidityController@add');
    Route::get('/list', '\App\Http\Controllers\HumidityController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\HumidityController@destory');
    Route::get('/edit/{id}', '\App\Http\Controllers\HumidityController@edit');
    Route::post('/edit/update/{id}', '\App\Http\Controllers\HumidityController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\HumidityController@view')->name('humidity.view');
    Route::post('/store', '\App\Http\Controllers\HumidityController@store')->name('humidity.store');
});
//Namlik natijasi
Route::group(['prefix' => 'humidity_result', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\HumidityResultController@search');
    Route::get('/add/{id}', '\App\Http\Controllers\HumidityResultController@add');
    Route::get('/list', '\App\Http\Controllers\HumidityResultController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\HumidityResultController@destory');
    Route::get('/edit/{id}', '\App\Http\Controllers\HumidityResultController@edit');
    Route::post('/edit/update/{id}', '\App\Http\Controllers\HumidityResultController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\HumidityResultController@view')->name('humidity_result.view');
    Route::post('/store', '\App\Http\Controllers\HumidityResultController@store')->name('humidity_result.store');
});
//measurement mistake
Route::group(['prefix' => 'measurement_mistake', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\MeasurementMistakeController@search');
    Route::get('/add/{id}', '\App\Http\Controllers\MeasurementMistakeController@add');
    Route::get('/list', '\App\Http\Controllers\MeasurementMistakeController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\MeasurementMistakeController@destory');
    Route::get('/edit/{id}', '\App\Http\Controllers\MeasurementMistakeController@edit');
    Route::post('/edit/update/{id}', '\App\Http\Controllers\MeasurementMistakeController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\MeasurementMistakeController@view');
    Route::post('/store', '\App\Http\Controllers\MeasurementMistakeController@store');
});
//measurement mistake
Route::group(['prefix' => 'laboratory_protocol', 'middleware' => 'auth'], function () {
    Route::get('/search', '\App\Http\Controllers\LaboratoryProtocolController@search');
    Route::get('/add/{id}', '\App\Http\Controllers\LaboratoryProtocolController@add');
    Route::get('/list', '\App\Http\Controllers\LaboratoryProtocolController@list');
    Route::get('/list/delete/{id}', '\App\Http\Controllers\LaboratoryProtocolController@destory');
    Route::get('/edit/{id}', '\App\Http\Controllers\LaboratoryProtocolController@edit');
    Route::post('/edit/update/{id}', '\App\Http\Controllers\LaboratoryProtocolController@update');
    Route::get('/view/{id}', '\App\Http\Controllers\LaboratoryProtocolController@view');
    Route::post('/store', '\App\Http\Controllers\LaboratoryProtocolController@store');
});
//Laboratory results
Route::group(['prefix' => 'laboratory-protocol', 'middleware' => 'auth'], function () {
    Route::get('/list', '\App\Http\Controllers\LaboratoryProtocolController@list');
    Route::get('/add/{id}', '\App\Http\Controllers\LaboratoryProtocolController@add');
    Route::get('/view/{id}', '\App\Http\Controllers\LaboratoryProtocolController@view')->name('lab.view');
    Route::post('/store', '\App\Http\Controllers\LaboratoryProtocolController@store');
    Route::get('/change/{id}', '\App\Http\Controllers\LaboratoryProtocolController@change_status');
});

//Operators
Route::middleware(['auth'])->group(function() {
    Route::get('/laboratory_operators', [LaboratoryOperatorController::class, 'index'])->name('laboratory_operators.index');

    Route::get('/laboratory_operators/create', [LaboratoryOperatorController::class, 'create'])->name('laboratory_operators.create');

    Route::post('/laboratory_operators', [LaboratoryOperatorController::class, 'store'])->name('laboratory_operators.store');

    Route::get('/laboratory_operators/{laboratoryOperator}/edit', [LaboratoryOperatorController::class, 'edit'])->name('laboratory_operators.edit');

    Route::put('/laboratory_operators/{laboratoryOperator}', [LaboratoryOperatorController::class, 'update'])->name('laboratory_operators.update');

    Route::delete('/laboratory_operators/{laboratoryOperator}', [LaboratoryOperatorController::class, 'destroy'])->name('laboratory_operators.destroy');
});
