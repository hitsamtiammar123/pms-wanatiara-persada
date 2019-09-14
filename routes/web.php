<?php

use App\Model\Employee;
use App\Model\KPIHeader;
USE App\Model\KPIResultHeader;

Route::get('/test',function(){
    $kpiresultheaders=KPIResultHeader::get();
    // $kpiresultheaders->each(function($d){
    //     $d->kpiresult;
    // });

    $kpiresultheaders=$kpiresultheaders->filter(function($d){
        return $d->kpiresult->unit==='$';
    });
    return $kpiresultheaders;
});

Route::get('/', 'PageController@index')->name('index')->middleware('guest');
Route::get('/auth-user','Auth\\LoginController@auth_user')->name('authuser');
Route::get('/app','PageController@app')->name('app')->middleware('auth');
Route::get('/excl-hehe','KPICompanyController@exclHehe')->name('exclhehe');

Route::get('/pdf-hehe','PDFController@bacoba')->name('pdf.hehe');
Route::get('/pdf/pms/{employeeID}','PDFController@pms')->name('pdf.pms');


Route::get('/javascript/app','JavascriptController@appJS')->name('js.app');
Route::get('/javascript/config','JavascriptController@configJS')->name('js.config');
Route::get('/javascript/routing','JavascriptController@routingJS')->name('js.routing');
Route::get('/javascript/user','JavascriptController@user')->name('js.user');
Route::get('/javascript/provider','JavascriptController@provider')->name('js.provider');
Route::get('/javascript/csrf-token','JavascriptController@token')->name('js.token');
Route::get('/app/front-view','PageController@appfront')->name('app.frontview');

Route::get('/print/pms/{employeeID}','PageController@printPms')->name('print.pms');

Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');
