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
        return view('app');
    }

    public function appfront(){
        return view('frontview');
    }

    public function printPms(){
        return view('print.pms');
    }
}
