<?php

use Illuminate\Database\Seeder;
use App\Model\Employee;

class AtasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $filename='/seed/employee.json';
        $disk=Storage::disk('local');
        $raw_data=$disk->get($filename);

        $employees=json_decode($raw_data,true);

        foreach($employees as $employee){
            $id=$employee['id'];
            if(array_key_exists('atasan_id',$employee)){
                $employee_data=Employee::find($id);
                $employee_data->atasan_id=$employee['atasan_id'];
                try{
                    $employee_data->save();
                }catch(Exception $e){
                    printf("Terjadi kesalahan pada saat memasukan data pengguna dengan id %s\n",$id);
                }
            }
        }
    }
}
