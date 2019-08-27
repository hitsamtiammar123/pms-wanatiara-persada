<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Traits\Indexable;
use App\Model\Traits\DynamicID;

class User extends Authenticatable
{
    use Notifiable,SoftDeletes,Indexable,DynamicID;

    const HIDDEN_PROPERTY_NOTIFICATION=['created_at','updated_at','notifiable_type','type'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
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
}
