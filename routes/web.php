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


Route::get('/', 'PageController@index')->name('index')->middleware('guest');
Route::get('/auth-user',function(){
    if(Auth::check())
        return ['Udah masuk cukk'];
    else
        return ['Belum masuk'];
});

Route::get('/pdf-hehe','PDFController@bacoba')->name('pdf.hehe');
Route::get('/app','PageController@app')->name('app');
Route::get('/javascript/app','JavascriptController@appJS')->name('js.app');
Route::get('/javascript/config','JavascriptController@configJS')->name('js.config');
Route::get('/javascript/routing','JavascriptController@routingJS')->name('js.routing');
Route::get('/app/front-view','PageController@appfront')->name('app.frontview');

Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');
