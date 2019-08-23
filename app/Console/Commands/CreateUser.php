<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Faker\Factory as Faker;
use App\Model\User;
use App\Model\Employee;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:user:create {employeeID?} {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini digunakan untuk membuat pengguna baru';
    protected $faker;

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
        //
        $data=$this->argument('employeeID');
        $d=$this->option('email');
        if($data)
            $this->createUser($data,$d);
        else
            $this->generateUser();
        $this->info('Pengguna sudah sukses dibuat');
    }

    protected function generateUser(){
        $employees=Employee::get();

        foreach($employees as $employee){
            if(!$employee->isUser()){
                $this->info("Membuat User untuk $employee->name");
                $this->createUser($employee->id);
                sleep(1);
            }
        }
    }

    protected function createUser($employee_id,$email=null){
        if(!$email){
            $email=$this->faker->unique()->email;
        }
        $user=new User();
        $user->id=User::generateID();
        $user->email=$email;
        $user->password=bcrypt("pms_{$user->id}");
        $user->employee_id=$employee_id;
        try{
            $user->save();
        }catch(Exception $err){
            $this->error('Terjadi kesalahan pada saat membuat data pengguna baru');
            $this->error($err->message());
        }
    }
}
