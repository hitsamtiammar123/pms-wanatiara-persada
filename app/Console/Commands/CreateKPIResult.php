<?php

namespace App\Console\Commands;

use App\Model\KPIResult;
use Illuminate\Console\Command;

class CreateKPIResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:kpiresult:create {name} {employeeID} {unit="%"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini dibuat untuk membuat KPIResult yang baru';

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
        $name=$this->argument('name');
        $employeeID=$this->argument('employeeID');
        $unit=$this->argument('unit');
        try{
            KPIResult::create([
                'id'=>KPIResult::generateID($employeeID),
                'name'=>$name,
                'unit'=>$unit
            ]);
            $this->info('Data KPI Result dengan nama"'.$name.'" sudah berhasil dibuat');
        }catch(Exception $err){
            $this->error('Terdapat kesalahan saat membuat data KPIResult yang baru');
            $this->error('pesan error: '.$err->getMessage());
        }
    }
}
