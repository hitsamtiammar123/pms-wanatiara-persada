<?php

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
        $disk=Storage::disk('local');

        $_year=is_null($year)?date('Y'):$year;
        $_month=is_null($month)?date('M'):$month;
        $file="kpicompany/kpicompany_{$_year}_{$_month}";

        $real_path=$disk->getAdapter()->getPathPrefix().'/'.$file;
        $kpi_company=file_get_contents($real_path);

        return json_decode($kpi_company,true);
    }
}
