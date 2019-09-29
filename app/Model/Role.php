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
    const HIDDEN_PROPERTY=['created_at','updated_at','deleted_at'];

    protected $dates=['deleted_at'];
    protected $fillable = [
        'name', 'can_have_child', 'level',
    ];
    protected $casts=['id'=>'string'];

    protected $hidden=['created_at','updated_at','deleted_at','tier','level','can_have_child'];

    public static function generateID(){
        $a=2;

        return self::_generateID($a);
    }

    public function employee(){
        return $this->hasMany(Employee::class);
    }

    public function tags(){
        return $this->belongsToMany(KPITag::class,'groupingkpi','role_id','tag_id')->withTimestamps();
    }
}
