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
        $micro=microtime(true).'';
        preg_match('/\.(\d{1,3})/',$micro,$arr);
        $b;
        if(array_key_exists(1,$arr)){
            if(strlen($arr[1])===3)
                    $b=$arr[1];
            else
                $b=add_zero($arr[1]);
        }
        else
            $b=rand(100,999);

        $n;
        if(is_null($numb))
            $n=rand(10,99);
        else
            $n=$numb;
        $result=$str.$b.$code.$n;

        return $result;

    }
}