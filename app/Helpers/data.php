<?php

use Carbon\Carbon;

if(!function_exists('add_zero')){

    function add_zero($number,$count=2){
        $check_number=pow(10,$count);
        if($number>$check_number)
            return $number.'';
        $result='';
        $counter=1;

        if($number!==0)
            $counter*=$number;
        else
            $counter=1;
        for($i=1;$i<=$count;$i++){
            $r=$counter/$check_number;
            if($r<1){
                $result.='0';
                $counter*=10;
            }
            else{
                break;
            }
        }
        $result.=$number;
        return $result;
    }
}

if(!function_exists('generate_id')){

    function generate_id($code,$numb=null){
        $str=date("ys");
        $b=substr(microtime(),5,3);
        $n='';
        if(is_null($numb))
            $n=rand(10,99);
        else
            $n=$numb;
        $result=$str.$b.$code.$n;

        return $result;

    }
}

if(!function_exists('kpi_company')){

    function kpi_company($year=null,$month=null){
        $disk=Storage::disk('resource');

        $_year=is_null($year)?date('Y'):$year;
        $_month=is_null($month)?date('n'):$month;
        $c=Carbon::now();
        $c->year=$_year;
        $c->month=$_month;
        $file="kpicompany/kpicompany_{$c->year}_{$c->shortEnglishMonth}.json";

        if($disk->exists($file)){
            $content=$disk->get($file);
            return json_decode($content,true);
        }
        else
            return [];

    }
}

if(!function_exists('auth_user')){

    function auth_user(){
        $user=Auth::user();
        return $user;
    }
}

if(!function_exists('filter_is_number')){

    function filter_is_number($data,$keys,$default=null){

        foreach($keys as $key){
            if(!array_key_exists($key,$data))
                continue;
            $d=$data[$key];
            if(!is_numeric($d))
                $d=$default;
            $data[$key]=$d;
        }
        return $data;
    }

}

if(!function_exists('sanitize_to_number')){

    function sanitize_to_number(array &$data,$keys,$precision=2){

        foreach($keys as $key){
            if(array_key_exists($key,$data)){
                $value=$data[$key];
                $data[$key]=round(floatval($value),$precision);
            }
        }
    }

}

if(!function_exists('str_name_compare')){

    /**
     *
     * Fungsi ini digunakan untuk membandingkan satu string dgn yang lainnya per karakter
     * @param string $str String yang ingin di-compare
     * @param string $compareTo String pembanding
     * @return boolean
     */
    function str_name_compare($str,$compareTo){
        $delimiter='/\s+/';
        $str_split=preg_split($delimiter,$str);
        $str_compare_to=preg_split($delimiter,$compareTo);

        if(count($str_split)!==count($str_compare_to))
            return false;

        for($i=0;$i<count($str_split);$i++){
            $c1=$str_split[$i];
            $c2=$str_compare_to[$i];
            if($c1!==$c2)
                return false;
        }
        return true;

    }

}



