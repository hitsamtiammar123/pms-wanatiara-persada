<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Traits\Indexable;
use App\Model\Traits\DynamicID;

class Role extends Model
{
    //
    use SoftDeletes,Indexable,DynamicID;
    protected $dates=['deleted_at'];
    protected $fillable = [
        'name', 'can_have_child', 'level',
    ];
    protected $casts=['id'=>'string'];

    public static function generateID(){
        $a=2;
        
        return self::_generateID($a);
    }

    public function employee(){
        return $this->hasMany(Employee::class);
    }
}
