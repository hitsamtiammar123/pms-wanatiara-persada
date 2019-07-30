<?php

use Illuminate\Database\Seeder;
use App\Model\Employee;
use App\Model\Role;

class BasicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $disk=Storage::disk('local');
        $dir='/seed';
        $jabatan_data=json_decode($disk->get($dir.'/jabatan.json'),true);
        $users_data=json_decode($disk->get($dir.'/employee.json'),true);

        foreach($jabatan_data as $jabatan){
            $r=new Role();
            $r->id=$jabatan['id'];
            $r->name=$jabatan['nama_jabatan'];
            $r->can_have_child=$jabatan['bisa_punya_anak'];
            $r->level=$jabatan['level'];
            $r->save();

        }

        foreach($users_data as $user){
            $u=new Employee();
            $u->id=$user['id'];
            $u->name=$user['name'];
            $u->gender='male';
            $u->role_id=array_key_exists('jabatan_id',$user)?$user['jabatan_id']:null;
            $u->save();

        }
    }
}
