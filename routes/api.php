<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix'=>'v1','middleware'=>['cors']],function(){
    Route::resource('kpiheader','KPIHeaderController',[
        'only'=>['index','show','update']
    ]);

    Route::resource('employee', 'EmployeeController',[
        'only'=>['show']
    ]);

    Route::resource('kpiprocess', 'KPIProcessController',[
        'only'=>['index']
    ]);

    Route::get('/ikhtisar','EmployeeController@ikhtisar')->name('employee.ikhtisar');
    Route::get('/search/autocomplete','SearchController@autocomplete')->name('search.autocomplete');
    Route::get('/search/result','SearchController@result')->name('search.result');
    Route::get('/kpicompany','KPICompanyController@getCurrentKPICompany')->name('kpicompany.get');
});


