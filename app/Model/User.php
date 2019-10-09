<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use App\Model\Traits\Indexable;
use App\Model\Traits\DynamicID;
use Exception;

class User extends Authenticatable
{
    use Notifiable,SoftDeletes,Indexable,DynamicID;

    const HIDDEN_PROPERTY_NOTIFICATION=[
        'created_at',
        'updated_at',
        'notifiable_type',
        'type'
    ];


    protected $fillable = [
        'id','email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token','created_at','updated_at','deleted_at'
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'id'=>'string'
    ];

    protected $dates=['deleted_at'];


    public static function generateID(){
        $a=0;
        return self::_generateID($a);
    }

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id','id');
    }

    public function getChannel(){
        return 'user-'.$this->id;
    }

    public function getLatestNotification(){
        $notifications=$this->notifications;

        $n=$notifications->sortByDesc('created_at')->first();
        return $n;
    }

    public function makeLog(Request $request,$type,$message){
        try{
            PMSLog::create([
                'user_id' => $this->id,
                'type'=>$type,
                'message'=>$message,
                'ip'=>$request->ip()
            ]);

        }catch(Exception $err){
            put_error_log($err);
        }
    }

}
