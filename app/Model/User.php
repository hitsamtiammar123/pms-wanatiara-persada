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
use App\Events\NewLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Notifications\ResetPasswordNotification;

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
        'password', 'remember_token','created_at','updated_at','deleted_at',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'id'=>'string'
    ];

    protected $with = ['employee'];

    protected $dates=['deleted_at'];
    protected $table='users';

    protected function broadCastLog(){
        $log=$this->logs->sortByDesc('created_at')->load('user.employee')->first();
        $cCarbon=Carbon::parse($log->created_at);
        $log->created_at=$cCarbon->format('d F Y H:i:s');
        event(new newLog($log->toArray()));

    }

    public static function generateID(){
        $a=0;
        return self::_generateID($a);
    }

    public static function getTierZeroUsers(){
        $data=static::whereHas('employee.role',function(Builder $query){
            $query->where('roles.tier','0');
        })->get();
        return $data;
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

    public function logs(){
        return $this->hasMany(PMSLog::class,'user_id','id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function makeLog(Request $request,$type,$message){
        try{
            PMSLog::create([
                'user_id' => $this->id,
                'type'=>$type,
                'message'=>$message,
                'ip'=>$request->ip()
            ]);
            $this->broadCastLog();

        }catch(Exception $err){
            put_error_log($err);
        }
    }

}
