<?php

namespace App\Console\Commands;

use App\Model\Employee;
use App\Model\Role;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Faker\Factory as Faker;

class CreateEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:employee:create {--name=} {--gender=male} {--roleID=} {--user} {--atasanID=} {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini digunakan untuk membuat Data Karyawan baru';


    /**
     * Saatnya membuat User Baru hehe
     *
     * @param array $data Data dari user yang mau dimasukan
     * @return App\Model\Employee
     */
    protected function makeUser(array $data){

        $faker=Faker::create();

        $employee=new Employee();
        $employee_id=Employee::generateID();
        $employee->id=$employee_id;
        $employee->gender=array_key_exists('gender',$data) && $data['gender']==='female'?'female':'male';
        $employee->name=array_key_exists('name',$data)?$data['name']:$faker->name;
        $employee->role_id=array_key_exists('roleID',$data)?$data['roleID']:Role::getRandomID();
        $employee->atasan_id=array_key_exists('atasanID',$data)?$data['atasanID']:null;

        $employee->save();
        $uID=User::generateID();
        $employee->id=$employee_id;

        array_key_exists('isUser',$data) && $data['isUser']?$employee->user()->create([
            'id'=>$uID,
            'email'=>array_key_exists('email',$data)?$data['email']:$faker->email,
            'password'=>bcrypt("pms_$uID")
        ]):null;

        $now=Carbon::now();
        $o=$employee->createHeader($now->year,$now->month);
        if($o!==1)
            put_log('Terdapat error pada saat membuat Header Employee dgn nama "'.$employee->name.'" Pesan error: '.$o);

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
        $name=$this->option('name');
        $gender=$this->option('gender');
        $roleID=$this->option('roleID');
        $atasanID=$this->option('atasanID');
        $isUser=$this->option('user');
        $this->makeUser([
            'name'=>$name,
            'gender'=>$gender,
            'roleID'=>$roleID,
            'atasanID'=>$atasanID,
            'isUser'=>$isUser
        ]);
       $this->info('Karyawan baru berhasil dibuat');
    }
}
