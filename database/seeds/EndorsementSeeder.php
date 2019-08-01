<?php

use Illuminate\Database\Seeder;
use App\Model\KPIEndorsement;
use App\Model\KPIHeader;

class EndorsementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $headers=DB::select("SELECT id FROM kpiheaders");

        if(!isset($headers)||is_null($headers)){
            printf("Data Header Tidak Ada\n");
            return;
        }

        foreach($headers as $header){
            $id=$header->id;
            $header_data=KPIHeader::find($id);
            $employee=$header_data->employee;
            $employee_id=$employee->id;
            $atasan=$employee->atasan;

            if(isset($atasan)){
                $endorse_2=new KPIEndorsement();
                $atasan_id=$atasan->id;
                $atasan_2=$atasan->atasan;

                if(isset($atasan_2)){
                    $endorse_3=new KPIEndorsement();
                    $atasan_2_id=$atasan_2->id;

                    $endorse_3->employee_id=$atasan_2_id;
                    $endorse_3->kpi_header_id=$id;
                    $endorse_3->id=KPIEndorsement::generateID($atasan_2_id);
                    $endorse_3->level=3;
                }

                $endorse_2->employee_id=$atasan_id;
                $endorse_2->kpi_header_id=$id;
                $endorse_2->id=KPIEndorsement::generateID($atasan_id);
                $endorse_2->level=2;

            }

            $endorse_1=new KPIEndorsement();
            $endorse_1->employee_id=$employee_id;
            $endorse_1->kpi_header_id=$id;
            $endorse_1->id=KPIEndorsement::generateID($employee_id);
            $endorse_1->level=1;

            try{
                $endorse_1->save();
                isset($endorse_2)?$endorse_2->save():'';
                isset($endorse_3)?$endorse_3->save():'';
            }catch(Exception $e){
                printf("Terjadi error saat memasukan data pada id {$id}");
            }
        }


    }
}
