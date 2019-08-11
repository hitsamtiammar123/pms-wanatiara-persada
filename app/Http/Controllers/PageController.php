<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    //
    public function app(Request $request){
        $js_vendors=[
            '/vendor/js/jquery.min.js',
            '/vendor/js/bootstrap.min.js',
            '/vendor/js/popper.min.js',
            '/vendor/js/angular.min.js',
            '/vendor/js/angular-route.min.js',
            '/vendor/js/angular-animate.min.js',
            '/vendor/js/angular-aria.min.js',
            '/vendor/js/angular-messages.min.js',
            '/vendor/js/angular-material.min.js',
            '/prototype.js'
        ];

        $css_list=[
            '/vendor/css/bootstrap.min.css',
            "/vendor/css/bootstrap-theme.min.css",
            "/vendor/css/angular-material.min.css",
            "/css/style.css"
        ];

        $js_vendors=array_map(function($d){
            return env('APP_RES').$d;
        },$js_vendors);

        $css_list=array_map(function($d){
            return env('APP_RES').$d;
        },$css_list);

        $appJS=env('APP_RES').'/web/app.js';

        return view('app',[
        'js_vendors'=>$js_vendors,
        'appJS'=>$appJS,
        'css_list'=>$css_list]);
    }
}
