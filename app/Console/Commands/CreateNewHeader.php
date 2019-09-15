<?php

namespace App\Console\Commands;

use App\Model\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateNewHeader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:endmonth:create-header {--month=default} {--year=default} {--employee=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini dipakai untuk membuat KPIHeader yang baru pada bulan yang bersangkutan';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected function makeHeader($employee,$y,$m,$date){
        $n=$employee->createHeader($y,$m);
        if($n===1)
            printf("Header dari %s sudah berhasil dibuat\n",$employee->name);
        else if($n===-1)
            printf("Header dari %s pada periode %s sudah ada\n",$employee->name,$date->format('Y-m-d'));
        sleep(1);
    }
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
        $now=Carbon::now();

        $opt_month=$this->option('month');
        $opt_year=$this->option('year');
        $opt_employee=$this->option('employee');

        $m=$opt_month==='default'?intval($now->month)+1:$opt_month;
        $y=$opt_year==='default'?$now->year:$opt_year;
        $e=$opt_employee==='default'?null:$opt_employee;

        // $e=Employee::find('1915284162');
        // $e->createHeader($y,$m);

        if(!$e){
            $employees=Employee::all();
            $date=Carbon::create($y,$m,16);

            foreach($employees as $employee){
                $this->makeHeader($employee,$y,$m,$date);
            }
        }
        else{
            $employee=Employee::find($e);
            $date=Carbon::create($y,$m,16);
            if($employee){
                $this->makeHeader($employee,$y,$m,$date);
            }
        }
    }
}
