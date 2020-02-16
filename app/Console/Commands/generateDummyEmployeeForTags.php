<?php

namespace App\Console\Commands;

use App\Model\Employee;
use App\Model\KPITag;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Faker\Factory as Faker;

class generateDummyEmployeeForTags extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:for-grouping {tagID} {roleID} {--num=20} {--isUser}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini bertujuan untuk men-generate data dummy untuk groupingKPI';

    protected $faker;

    private function createUser($isUser,$employee){
        if($isUser){
            $user=new User();
            $user_id=User::generateID();
            $user->id=$user_id;
            $user->employee_id=$employee->id;
            $user->email=$this->faker->unique()->email;
            $user->password=bcrypt("pms_{$user->id}");
            try{
                $user->save();
            }catch(Exception $err){
                $this->error('Terjadi kesalahan pada saat membuat data pengguna baru');
                $this->error($err->message());
            }
        }
    }

    private function makeEmployee($tag,$roleID,$isUser){
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
        $this->createUser($isUser,$employee);

        return $employee;

    }

    private function makeDummy($tagID,$roleID,$num,$isUser){
        $tag=KPITag::find($tagID);
        if(!$tag){
            $this->error('Tag tidak ditemukan');
            return;
        }
        for($i=0;$i<$num;$i++){
            $e=$this->makeEmployee($tag,$roleID,$isUser);
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
        $this->faker=Faker::create('id_ID');

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
        $isUser=$this->option('isUser');
        $num=intval($this->option('num'));
        $this->makeDummy($tagID,$roleID,$num,$isUser);
    }
}
