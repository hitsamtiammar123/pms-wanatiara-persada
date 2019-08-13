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

use Illuminate\Http\Request;
use App\Imports\ArrayImport;

Route::get('/', 'PageController@index')->name('index');
Route::get('/auth-user',function(Request $request){
    $user=Auth::user();
    return $user;
});

Route::get('/excl-hehe',function(){
    $path='C:\\xampp\\htdocs\\pms-wanatiara-persada-v1-laravel\\storage\\requirement\\Target Managemen 2019.xlsx';
    Excel::import(new ArrayImport,$path);
});

Route::get('/pdf-hehe','PDFController@bacoba')->name('pdf.hehe');
Route::get('/app','PageController@app')->name('app');
Route::get('/javascript/app','JavascriptController@appJS')->name('js.app');
Route::get('/javascript/config','JavascriptController@configJS')->name('js.config');
Route::get('/javascript/routing','JavascriptController@routingJS')->name('js.routing');
Route::get('/javascript/user','JavascriptController@user')->name('js.user');
Route::get('/app/front-view','PageController@appfront')->name('app.frontview');


Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');
