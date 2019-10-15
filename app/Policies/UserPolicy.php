<?php

namespace App\Policies;

use App\Model\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function tier_except_0($user){
        $tiers=[1,2,3];
        $t=$user->employee->role?$user->employee->role->tier:3;
        if(in_array($t,$tiers)){
            return true;
        }
        return false;
    }
}
