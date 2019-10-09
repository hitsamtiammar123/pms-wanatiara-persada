<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PMSLog extends Model
{
    protected $table='pmslogs';
    protected $fillable=[
        'user_id','type','message','ip'
    ];
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

}
