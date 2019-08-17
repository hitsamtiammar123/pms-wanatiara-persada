<?php

if(!function_exists('send_406')){

    function send_406_error($message="Data yang tidak dimasukan tidak valid"){
        return response()->json(["error"=>406,"message"=>$message],406);
    }

    function send_404_error($message="Data yang anda cari tidak ada"){
        return response()->json(["error"=>404,"message"=>$message],404);
    }

    function send_403_error($message="Anda tidak memiliki hak untuk mengakses halaman ini"){
        return response()->json(["error"=>403,"message"=>$message],403);
    }


};
