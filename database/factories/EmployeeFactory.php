<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model\Employee;
use Faker\Generator as Faker;

$factory->define(Employee::class, function (Faker $faker) {
    return [
        //
        'id'=>Employee::generateID(),
        'name'=>$faker->name('male'),
        'gender'=>'male',
        'dob'=>$faker->dateTimeBetween($startDate = '-30 years', $endDate = '-10 years', $timezone = null)
    ];
});
