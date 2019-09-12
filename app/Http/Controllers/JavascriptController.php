<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use MatthiasMullie\Minify;

class JavascriptController extends Controller
{
    //

    protected $web;
    protected $angular;
    protected $res_url='http://localhost/pms-wanatiara-persada-v1-angular';
    protected $header_arr=['Content-Type'=>'text/javascript'];
    protected $except_files;
    protected $list_files;

    public function __construct()
    {
        $this->web=Storage::disk('web');
        $this->angular=Storage::disk('angular');
        $this->except_files=config('frontend.except');
        $this->list_files=[
            'service'=>[],
            'factory'=>[],
            'directive'=>[],
            'filter'=>[],
            'values'=>['javascript/user','javascript/csrf-token'],
            'provider'=>['web/provider/formModal.js'],
            'boot'=>[
                'web/boot/resolve.js',
                '{routing}',
                "web/boot/providerConf.js",
                "web/boot/config.js",
                "web/boot/run.js",
            ]
        ];
    }

    public function mapNameDFiles($data){
        //return env('APP_RES')."/web/$data";
        return "web/$data";
    }

    protected function getFileWithDirRes($list,$dir){
        $r=[];
        foreach($list as $l){
            if(!filter_var($l,FILTER_VALIDATE_URL)){
                $file='web/'.$dir.'/'.$l;
                $r[]=res_url($file);
            }
            else
                $r[]=$l;
        }
        return $r;
    }


    protected function loadDynamically($dir){
        $disk=$this->web;
        $except_files=$this->except_files;
        $collection_files=collect($disk->allfiles($dir));
        $collection_files=$collection_files->filter(function($data)use($dir,$except_files){
            if(!in_array($data,$except_files))
                return $data;
        })->map(array($this,'mapNameDFiles'));

        return $collection_files->toArray();

    }

    protected function loadControllers(){
        $user=auth_user();
        $tier=$user->employee->role->tier;
        $controllers=[];
        $defaults=config('frontend.controllers.default');
        $tier_controllers=[];
        switch($tier){
            case 0:
            $tier_controllers=config('frontend.controllers.t0');
            break;
            case 1:
            $tier_controllers=config('frontend.controllers.t1');
            break;
            case 2:
            $tier_controllers=config('frontend.controllers.t2');
            break;
            case 3:
            $tier_controllers=config('frontend.controllers.t3');
            break;
        }

        $controllers=collect(array_merge($controllers,$defaults,$tier_controllers))
        ->map(function($d){
            return "controller/$d";
        });

        return $controllers->map([$this,'mapNameDFiles']);
    }


    public function appJS(Request $request){
        $listfiles=$this->list_files;

        $dynamic_list=config('frontend.dynamic');
        $appJS=$this->angular->get('main.js');
        $count=0;
        $countDynamic=0;
        $countStatic=0;

        foreach($listfiles as $key=>$d){

            if(in_array($key,$dynamic_list)){
                $d=array_merge($listfiles[$key],$this->loadDynamically($key));
                $countDynamic+=count($d);
                $key_a='{'.$key.'_key}';
                $appJS=str_replace($key_a,json_encode($d,JSON_UNESCAPED_SLASHES),$appJS);

                if($key!=='boot')
                    $count+=count($d);
            }

        }

        $controllers=json_encode($this->loadControllers(),JSON_UNESCAPED_SLASHES);

        $appJS=str_replace('{controller_key}',$controllers,$appJS);
        $appJS=str_replace('{count}',$count,$appJS);
        $appJS=str_replace('{countDynamic}',$countDynamic,$appJS);
        $appJS=str_replace('{frontview}', "'app/front-view'",$appJS);
        $appJS=str_replace('{pfile}','javascript/provider',$appJS);

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

        $user=auth_user();
        if($user){
            $routingJS=str_replace('{routelist}',$routelist,$routingJS);

            return response($routingJS,200,$this->header_arr);
        }

        return response('');


    }



    public function user(Request $request){
        $userJS=$this->angular->get('user.js');

        $auth_user=auth_user();

        if($auth_user){
            if($auth_user->employee->atasan){
                $auth_user->employee->atasan;
                $auth_user->employee->atasan->role;
            }
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

    public function token(){
        $tokenJS=$this->angular->get('csrf_token.js');

        $tokenJS=str_replace('{token}',csrf_token(),$tokenJS);
        return response($tokenJS,200,$this->header_arr);
    }

    public function provider(){

        $provider=$this->list_files['provider'];
        $bootlist=$this->list_files['boot'];

        $user=auth_user();

        if($user){
            $tier=$user->employee->role->tier;

            switch($tier){
                case 0:
                  $bootlist[1]=str_replace('{routing}','web/boot/routing_t0.js',$bootlist[1]);
                break;
                default:
                $bootlist[1]=str_replace('{routing}','web/boot/routing.js',$bootlist[1]);
                break;
            }

            $providers_list=array_merge($provider,$bootlist);
            $providerJS='';

            $minifier=new Minify\JS;

            foreach($providers_list as $js){
                $providerJS.=file_get_contents($js);
            }

            $minifier->add($providerJS);
            $mJS=$minifier->minify();

            return response($providerJS,200,$this->header_arr);
        }
        return send_401_error();

    }
}
