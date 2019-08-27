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
    protected $hidden=['created_at','updated_at','deleted_at'];

    protected $sendToRoleList=[
        0=>'1915282279',
        1=>'1915282265',
        2=>'1915282223',
        3=>'1915282288'
    ];


    public static function generateID(){
        $a=1;
        return self::_generateID($a);
    }

    public static function frontEndNotification($notification){
        $r=[];

        $data=$notification->data;
        $r['id']=$notification->id;
        $r['read_at']=$notification->read_at;
        $r['date']=Carbon::parse($notification->created_at)->format('d M Y');
        $r['subject']=$data['subject'];
        $r['type']=$data['type'];
        if($r['type']==='redirect'){
            $r['redirectTo']=array_key_exists('redirectTo',$data)?
            $notification->data['redirectTo']:'/';
        }
        if(array_key_exists('from',$data))
            $r['from']=$data['from'];
        else
            $r['from']='System';

        return $r;
    }

    public static function frontEndNotifications($notifications){
        $result=[];

        foreach($notifications as $notification){
            $r=self::frontEndNotification($notification);
            $result[]=$r;
        }
        return $result;
    }

    public function getCurrentHeader(){
        $headers=$this->kpiheaders;
        $currDate=KPIHeader::getCurrentDate();
        $curr_header=$headers->where('period',$currDate)->first();

        return $curr_header;
    }

    public function getSendToUser(){
        $level=$this->role->level;
        $index_r=-1;

        if($level===2){
            $index_r=2;
        }
        else if($level===1){
            if($this->role->id===$this->sendToRoleList[1])
                $index_r=0;
            else
                $index_r=1;

        }
        else if($level<=0){
            $index_r=1;
        }
        else if($level>2){
            $index_r=3;
        }

        $request_send_to=Employee::where('role_id',$this->sendToRoleList[$index_r])->first();
        $request_send_to->role;

        return $request_send_to;

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
