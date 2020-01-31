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
        $file='D:\document\document_creo\trade\money record.txt';

        $content=file_get_contents($file);
        $split_arr=explode("\n",$content);
        $total=0;
        $i=0;
        foreach($split_arr as  $num){
            echo $i." -> ".$num."\n";
            $total+=intval($num);
            $i++;
        }

        echo "Total is ".$total;

    }
}
