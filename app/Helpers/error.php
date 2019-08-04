<?php

if(!function_exists('send_406')){

    function send_406_error($message="Data yang tidak dimasukan tidak valid"){
        return response()->json(["error"=>406,"message"=>$message],406);
    }
};
