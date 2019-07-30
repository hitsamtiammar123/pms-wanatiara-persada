<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model\Role as Role;
use Faker\Generator as Faker;

$factory->define(App\Model\Role::class, function (Faker $faker) {
    return [
        'id'=>Role::generateID(), 
        'name'=>Str::random(10),
        'can_have_child'=>true,
        'level'=>0
    ];
});
