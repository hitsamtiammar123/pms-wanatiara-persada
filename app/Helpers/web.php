<?php


if(!function_exists('style')){

    function style($file,$dir='/css',$disk='angular_resource',$type='inline'){
        $disk=Storage::disk($disk);
        $filename=$dir.'/'.$file;
        $content=$disk->get($filename);
        $template='';
        if($type==='inline')
            $template="<style>$content</style>";
        else if($type==='print'){
            $template='<style>@Media Print{'.$content.'}</style>';
        }

        return $template;

    }
}

if(!function_exists('res_url')){
    function res_url($d){
        return url('/'.$d);
        //return $d;
     }
}

if(!function_exists('log_path')){
    function log_path($file=''){
        return base_path('storage/logs/'.$file);
    }
}

if(!function_exists('put_log')){

    function put_log($log,$filename='temp.log'){
        $filename=log_path($filename);
        $file=fopen($filename,'a+');

        $message= date('Y-M-d h:i:s').' >>> '.$log."\n";
        fputs($file,$message);
        fclose($file);
    }
}

if(!function_exists('put_error_log')){

    function put_error_log(\ Exception $err){
        $c=get_class($err);
        $m=$err->getMessage();
        put_log(
            "Error => {$c} message=>{$m}",'error.log'
        );
    }
}

