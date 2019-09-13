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

    Route::apiResource('kpiheader','KPIHeaderController',[
        'only'=>['index','show','update']
    ]);

    Route::apiResource('employee', 'EmployeeController',[
        'only'=>['show']
    ]);

    Route::apiResource('kpiprocess', 'KPIProcessController',[
        'only'=>['index']
    ]);

    Route::apiResource('kpiendorsement', 'KPIEndorsementController',[
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

    Route::post('/kpicompany/upload','KPICompanyController@postKPICompany')
    ->name('kpicompany.post');

    Route::get('/notification/get/{employeeID}','NotificationController@getNotifications')
    ->name('notification.get');

    Route::get('/notification/get/{employeeID}/{id}','NotificationController@getNotification')
    ->name('notification.get-spesific');


    Route::get('/notification/mark-as-read/{employeeID}/{id}','NotificationController@markAsRead')
    ->name('notification.mark-as-read');

    Route::get('/notification/requestableusers/{employeeID}','NotificationController@getRequestableUsers')
    ->name('notification.requestable-user');

    Route::post('/notification/request-change/{employeeID}','NotificationController@requestChange')
    ->name('notification.request-change');

    Route::put('/kpiendorsement/{employeeID}/reset','KPIEndorsementController@reset')
    ->name('kpiendorsement.reset');

});


