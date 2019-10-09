<?php

if(!function_exists('send_404_error')){
    function send_404_error($message="Data yang anda cari tidak ada"){
        return response()->json(["error"=>404,"message"=>$message],404);
    }
}

if(!function_exists('send_403_error')){

    function send_403_error($message="Anda tidak memiliki hak untuk mengakses halaman ini"){
        return response()->json(["error"=>403,"message"=>$message],403);
    }
}

if(!function_exists('send_401_error')){
    function send_401_error($message="Anda belum melakukan autentikasi"){
        return response()->json(["error"=>401,"message"=>$message],401);
    }
}

if(!function_exists('send_400_error')){
    function send_400_error($message="format data yang anda kirimkan salah"){
        return response()->json(["error"=>401,"message"=>$message],400);
    }

}

if(!function_exists('send_406_error')){
    function send_406_error($message="Data yang tidak dimasukan tidak valid"){
        return response()->json(["error"=>406,"message"=>$message],406);
    }
};
