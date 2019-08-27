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


Route::group(['prefix'=>'v1','middleware'=>['cors','web']
],function(){
    Route::resource('kpiheader','KPIHeaderController',[
        'only'=>['index','show','update']
    ]);

    Route::resource('employee', 'EmployeeController',[
        'only'=>['show']
    ]);

    Route::resource('kpiprocess', 'KPIProcessController',[
        'only'=>['index']
    ]);

    Route::resource('kpiendorsement', 'KPIEndorsementController',[
        'only'=>['update']
    ]);

    Route::get('/ikhtisar','EmployeeController@ikhtisar')
    ->name('employee.ikhtisar');

    Route::get('/search/autocomplete','SearchController@autocomplete')
    ->name('search.autocomplete');

    Route::get('/search/result','SearchController@result')
    ->name('search.result');

    Route::get('/kpicompany','KPICompanyController@getCurrentKPICompany')
    ->name('kpicompany.get');

    Route::post('/kpicompany','KPICompanyController@postKPICompany')
    ->name('kpicompany.post');

    Route::get('/notification/get/{employeeID}','NotificationController@getNotification')
    ->name('notification.get');

    Route::get('/notification/mark-as-read/{employeeID}/{id}','NotificationController@markAsRead')
    ->name('notification.mark-as-read');

    Route::get('/notification/requestableusers/{employeeID}','NotificationController@getRequestableUsers')
    ->name('notification.requestable-user');

});


