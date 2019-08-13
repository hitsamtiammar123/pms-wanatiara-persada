<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;

class JavascriptController extends Controller
{
    //

    protected $web;
    protected $angular;
    protected $res_url='http://localhost/pms-wanatiara-persada-v1-angular';
    protected $header_arr=['Content-Type'=>'text/javascript'];
    protected $except_files;

    public function __construct()
    {
        $this->web=Storage::disk('web');
        $this->angular=Storage::disk('angular');
        $this->except_files=config('frontend.except');
    }

    public function mapNameDFiles($data){
        return env('APP_RES')."/web/$data";
    }



    protected function loadDynamically($dir){
        if($dir==='values'){
            $j=0;
        }
        $disk=$this->web;
        $except_files=$this->except_files;
        $collection_files=collect($disk->allfiles($dir));
        $collection_files=$collection_files->filter(function($data)use($dir,$except_files){
            if(!in_array($data,$except_files))
                return $data;
        })->map(array($this,'mapNameDFiles'));

        return $collection_files->toArray();

    }

    protected function loadStatiscally($dir,$list){
        $r=[];
        foreach($list as $l){
            if(!filter_var($l,FILTER_VALIDATE_URL))
                $l=env('APP_RES')."/web/$dir/$l";
            $r[]=$l;
        }
        return $r;
    }

    public function appJS(Request $request){
        $listfiles=[
            'controller'=>[],
            'service'=>[],
            'factory'=>[],
            'directive'=>[],
            'filter'=>[],
            'values'=>[route('js.user')],
            'provider'=>['formModal.js'],
            'boot'=>['resolve.js','routing.js',"providerConf.js",'config.js',"run.js"]
        ];

        $dynamic_list=['controller','filter','service','factory','values','directive'];
        $appJS=$this->angular->get('main.js');
        $count=0;

        foreach($listfiles as $key=>$d){

            if(in_array($key,$dynamic_list))
                $d=array_merge($listfiles[$key],$this->loadDynamically($key));
            else
                $d=$this->loadStatiscally($key,$d);

            $key_a='{'.$key.'_key}';
            $appJS=str_replace($key_a,json_encode($d,JSON_UNESCAPED_SLASHES),$appJS);

            if($key!=='boot')
                $count+=count($d);
        }

        $appJS=str_replace('{count}',$count,$appJS);
        $appJS=str_replace('{countBoot}',count($listfiles['boot']),$appJS);
        $appJS=str_replace('{frontview}', "'".route('app.frontview')."'",$appJS);

        return response($appJS,200,$this->header_arr);

    }

    public function configJS(Request $request){
        $providerBI=config('frontend.angular_provider');
        $providerUI=array_map(function($data){
            $str=str_replace('.js','',$data).'Provider';
            $filter_str=preg_replace('/(\w+\/)+/','',$str);
            return  $filter_str;
        },
        $this->web->files('provider'));

        $providers=array_merge($providerBI,$providerUI);
        $configJS=$this->angular->get('config.js');
        $configJS=str_replace('{providers}',implode(',',$providers),$configJS);

        return response($configJS,200,$this->header_arr);
    }

    public function routingJS(Request $request){
        $routelist=$this->angular->get('route.js');
        $routingJS=$this->angular->get('routing.js');

        $routingJS=str_replace('{routelist}',$routelist,$routingJS);

        return response($routingJS,200,$this->header_arr);
    }

    public function user(Request $request){
        $userJS=$this->angular->get('user.js');

        $auth_user=$request->session()->get('auth_user');

        if($auth_user){
            $auth_user->employee->atasan;
            $auth_user->employee->bawahan;
            $auth_user->employee->role;
            $auth_user->employee->bawahan->each(function($d){
                $d->load('role');
            });
            $userJS=str_replace('{user}',$auth_user->toJSON(),$userJS);
        }
        else{
            $userJS=str_replace('{user}','{}',$userJS);
        }

        return response($userJS,200,$this->header_arr);


    }
}
