<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Faker\Factory as Faker;
use App\Model\User;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:user:create {employeeID} {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini digunakan untuk membuat pengguna baru';

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
        $data=$this->argument('employeeID');
        $d=$this->option('email');
        if(!$d){
            $faker=Faker::create();
            $d=$faker->unique()->email;
        }
        $user=new User();
        $user->id=User::generateID();
        $user->email=$d;
        $user->password=bcrypt("pms_{$user->id}");
        $user->employee_id=$data;
        try{
            $user->save();
        }catch(Exception $err){
            $this->error('Terjadi kesalahan pada saat membuat data pengguna baru');
            $this->error($err->message());
        }
        $this->info('Pengguna sudah sukses dibuat');
    }
}
