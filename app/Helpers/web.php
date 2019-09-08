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

    function res_url($d){
       //return env('APP_RES').'/'.$d;
       return $d;
    }
}
