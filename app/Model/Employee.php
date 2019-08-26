<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Traits\Indexable;
use App\Model\Traits\DynamicID;
use App\Model\User;
use Carbon\Carbon;

class Employee extends Model
{
    //
    use SoftDeletes,Indexable,DynamicID;

    protected $dates=['deleted_at'];
    protected static $listID=[];


    const HIDDEN_PROPERTY=['created_at','updated_at','deleted_at','role_id','atasan_id'];

    protected $fillable = [
        'name', 'dob', 'gender',
    ];
    protected $casts=['id'=>'string'];


    public static function generateID(){
        $a=1;
        return self::_generateID($a);
    }

    public static function frontEndNotifications($notifications){
        $result=[];

        foreach($notifications as $notification){
            $r=[];
            $r['id']=$notification->id;
            $r['read_at']=$notification->read_at;
            $r['date']=Carbon::parse($notification->created_at)->format('d M Y');
            $r['subject']=$notification->data['subject'];
            $r['from']='Zhang Hehe';

            $result[]=$r;
        }
        return $result;
    }


    public function isUser(){
        return !is_null($this->user);
    }

    public function user(){
        return $this->hasOne(User::class);
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function kpiheaders(){
        return $this->hasMany(KPIHeader::class);
    }

    public function atasan(){
        return $this->belongsTo(self::class,'atasan_id','id');
    }

    public function bawahan(){
        return $this->hasMany(self::class,'atasan_id','id');
    }

    public function kpiendorsements(){
        return $this->hasMany(KPIEndorsement::class);
    }
}
