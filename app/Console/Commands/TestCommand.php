<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hehe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ini command Heheeheeheh';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }





    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$this->syncKPIHeader('2019-10-16');
        ob_start();
            system('git status -s');
            $str=ob_get_contents();
        ob_end_clean();

        $st_split=\explode("\n",$str);
        $r=[];
        foreach($st_split as $i => $s){
            $t=[];
            $st_split[$i]=trim($s);
            $s2=explode(' ',$st_split[$i]);
            if(count($s2)===1)
                continue;
            $t['flag']=$s2[0];
            $t['file']=$s2[1];

            $r[]=$t;
        }
        echo \json_encode($r,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
}
