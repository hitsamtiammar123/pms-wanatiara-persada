<?php

namespace App\Console\Commands;

use App\Model\Employee;
use App\Model\KPITag;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Faker\Factory as Faker;

class generateDummyEmployeeForTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:for-grouping {tagID} {roleID} {--num=20}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini bertujuan untuk men-generate data dummy untuk groupingKPI';

    protected $faker;

    private function makeEmployee($tag,$roleID){
        $employee=new Employee();
        $employee_id=Employee::generateID();
        $gender=rand(0,1)?'male':'female';

        $employee->id=$employee_id;
        $employee->name=$this->faker->name($gender);
        $employee->gender=$gender;
        $employee->role_id=$roleID;
        $employee->atasan_id=$tag->representative?$tag->representative->id:null;

        $employee->save();
        $employee->id=$employee_id;
        $tag->groupemployee()->attach($employee_id);

        $now=Carbon::now();
        $m=$now->month;
        $y=$now->year;
        $employee->createHeader($y,$m,$tag);

        return $employee;

    }

    private function makeDummy($tagID,$roleID,$num){
        $tag=KPITag::find($tagID);
        if(!$tag){
            $this->error('Tag tidak ditemukan');
            return;
        }
        for($i=0;$i<$num;$i++){
            $e=$this->makeEmployee($tag,$roleID);
            $this->info("Pegawai dengan nama \"{$e->name}\" dan id \"{$e->id}\" sudah dibuat dimasukan ke tag \"{$tag->name}\" ");
            //sleep(1);
        }


    }

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->faker=Faker::create();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tagID=$this->argument('tagID');
        $roleID=$this->argument('roleID');
        $num=intval($this->option('num'));
        $this->makeDummy($tagID,$roleID,$num);
    }
}
