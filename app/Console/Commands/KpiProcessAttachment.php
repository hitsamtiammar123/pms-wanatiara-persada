<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\Traits\GetInput;
use App\Model\KPIProcess;
use App\Model\KPIHeader;

class KpiProcessAttachment extends Command
{

    use GetInput;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:kpiprocess:attach {header_id} {p_name?} {--pw=10} {--pt=3} {--real=3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini digunakan untuk melakukan penempelan ID KPIHeader dengan KPIProcess';

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
        $header_id=$this->argument('header_id');
        $p_name=$this->argument('p_name');

        $pw=$this->option('pw');
        $pt=$this->option('pt');
        $real=$this->option('real');

        if(!$p_name){
            $p_name=$this->getInput('Silakan masukan nama Sasaran Proses yang diinginkan');
        }


        try{
            $p_data=KPIProcess::select('id')->where('name',trim($p_name))->first();
            $header=KPIHeader::find($header_id);

            if(!$p_data || !$header){
                throw new \Exception('Data Header atau Sasaran Proses yang diminta tidak ditemukan');
            }

            $in=['pw'=>$pw,'pt'=>$pt,'real'=>$real];
            $d=[$p_data->id => $in];

            $header->kpiprocesses()->attach($d);
        }catch(\Exception $err){
            $this->error('Terjadi kesalahan pada saat memasukan data. Pesan Error:');
            $this->info($err->getMessage());
        }
        //dd([$header_id,$p_data,$pw,$pt,$real]);

    }
}
