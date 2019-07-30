<?php

namespace App\Model\Traits;

trait Indexable{
    
    public function getIndex(){
        $ids=self::select('id')->get()->toArray();
        $ids_map=array_map(function($d){return $d['id'].'';},$ids);
        $id=$this->id;
        $index=array_search($id,$ids_map);

        return $index;
    }
}

