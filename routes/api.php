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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix'=>'v1','middleware'=>['cors']],function(){
    Route::resource('kpiheader','KPIHeaderController',[
        'only'=>['index','show']
    ]);

    Route::resource('employee', 'EmployeeController',[
        'only'=>['show']
    ]);

    Route::get('/ikhtisar','EmployeeController@ikhtisar')->name('employee.ikhtisar');
    Route::get('/search/autocomplete','SearchController@autocomplete')->name('search.autocomplete');
    Route::get('/search/result','SearchController@result')->name('search.result');
});


