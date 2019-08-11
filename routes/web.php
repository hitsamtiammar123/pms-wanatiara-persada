<?php

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

use  App\Library\TestLib;

Route::get('/', function (TestLib $test) {
    //echo style('app.css');
    return view('welcome');
});

Route::get('/pdf-hehe','PDFController@bacoba')->name('pdf.hehe');
Route::get('/app','PageController@app')->name('app');
Route::get('/javascript/app','JavascriptController@appJS')->name('js.app');
Route::get('/javascript/config','JavascriptController@configJS')->name('js.config');
Route::get('/javascript/routing','JavascriptController@routingJS')->name('js.routing');

Auth::routes();
