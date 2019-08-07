<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\KPIProcess;

class KpiProcessCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:create:kpiprocess {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini berfungsi untuk membuat kpiprocess baru';

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
        $name=$this->argument('name');
        if(!$name){
            $name=$this->ask('Tolong masukan nama dari sasaran proses: ');
        }


        try{
            $p=new KPIProcess();
            $p->id=KPIProcess::generateID();
            $p->name=$name;
            $p->save();
        }catch(Exception $e){
            $this->error('Terdapat kesalahan saat memasukan data');
        }
        $this->info("Data sasaran proses dengan nama \"$name\" sudah berhasil dimasukan");
    }
}
