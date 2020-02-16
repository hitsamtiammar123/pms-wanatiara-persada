<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateGitUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:git:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini berfungsi untuk membuat update git ke dalam file json';

    protected function getGitStatusFile(){
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

        return $r;
    }

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
        //
        $listUpdated=$this->getGitStatusFile();
        $file_storage=storage_path('app/update.json');

        foreach($listUpdated as $index => $list){
            $f=base_path('/'.$list['file']);
            $content=file_get_contents($f);
            $listUpdated[$index]['content']=$content;
        }

        $data=[
            'date_create'=>Carbon::now()->format('Y M d h:i:s'),
            'data'=>$listUpdated
        ];
        file_put_contents($file_storage,json_encode($data,JSON_UNESCAPED_SLASHES));
        echo 'berhasil';
    }
}
