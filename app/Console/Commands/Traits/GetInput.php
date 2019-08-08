<?php

namespace App\Console\Commands\Traits;

trait GetInput{

    protected function getInput($q){
        $input=null;

        do{
            $input=$this->ask($q);
        }while(!$input);

        return $input;
    }
}
