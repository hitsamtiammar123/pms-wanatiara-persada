<?php

namespace App\Model\Interfaces;

use App\Model\Employee;

interface Endorseable{

    public function hasEndorse(Employee $employee);

}
