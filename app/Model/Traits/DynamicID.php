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
}