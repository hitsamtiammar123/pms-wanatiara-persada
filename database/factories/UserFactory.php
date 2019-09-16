<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Employee;
use App\Model\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    $employee=factory(Employee::class)->create();
    return [
        'id'=>User::generateID(),
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('password'), // password
        'remember_token' => Str::random(10),
        'employee_id'=>$employee->id
    ];
});
