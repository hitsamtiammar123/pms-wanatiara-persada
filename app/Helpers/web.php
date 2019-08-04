<?php


if(!function_exists('style')){
    function style($file,$dir='/css',$disk='public_html'){
        $disk=Storage::disk($disk);
        $filename=$dir.'/'.$file;
        $content=$disk->get($filename);
        $template="<style>$content</style>";
        return $template;

    }
}
