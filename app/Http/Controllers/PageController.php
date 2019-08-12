<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{

    protected function loadFrontEndVendor(){
        $js_vendors=config('frontend.js_vendor');

        $css_list=config('frontend.css_vendor');

        $js_vendors=array_map(function($d){
            return env('APP_RES').$d;
        },$js_vendors);

        $css_list=array_map(function($d){
            return env('APP_RES').$d;
        },$css_list);

        return ['js_vendor'=>$js_vendors,'css_vendor'=>$css_list];
    }

    public function index(){
        $vendor=$this->loadFrontEndVendor();
        return view('index',['js_vendors'=>$vendor['js_vendor'],'css_list'=>$vendor['css_vendor']]);
    }


    //
    public function app(Request $request){
        $vendor=$this->loadFrontEndVendor();

        $appJS=env('APP_RES').'/web/app.js';

        return view('app',[
        'js_vendors'=>$vendor['js_vendor'],
        'appJS'=>$appJS,
        'css_list'=>$vendor['css_vendor']]);
    }
}
