<?php

namespace App\Model\Traits;

trait DynamicID{
    protected static $listID=[];


    protected static function _generateID($a,$code=null){
        $id=generate_id($a,$code);
        while(in_array($id,self::$listID))
            $id=generate_id(++$a,$code);

        self::$listID[]=$id;
        return $id;
    }

    public static function getRandomID(){
        $ids=self::select('id')->get()->toArray();
        $ids_map=array_map(function($d){return $d['id'].'';},$ids);


        return $ids_map[rand(0,count($ids_map)-1)];
    }

    public function getIDForTheFirstTime(){
        if($this->wasRecentlyCreated)
            return self::select('id')->orderBy('created_at','desc')->first()->id;
        return null;
    }

}
