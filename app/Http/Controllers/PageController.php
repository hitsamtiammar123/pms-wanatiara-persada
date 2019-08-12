<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{

    public function index(){
        return view('index');
    }


    //
    public function app(Request $request){
        $appJS=env('APP_RES').'/web/app.js';
        return view('app',['appJS'=>$appJS]);
    }
}
