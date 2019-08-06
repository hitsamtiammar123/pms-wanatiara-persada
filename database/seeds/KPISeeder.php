<?php

use Illuminate\Database\Seeder;
use App\Model\KPIHeader;
use App\Model\KPIResult;
use App\Model\KPIResultHeader;

class KPISeeder extends Seeder
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
        $realisasi_data=json_decode($disk->get($dir.'/realisasi.json'),true);

        for($i=0;$i<count($realisasi_data);$i++){
            $realisasi=$realisasi_data[$i];
            $employee_id=$realisasi['employee_id'];
            $header1_id=KPIHeader::generateID($employee_id);
            $header2_id=KPIHeader::generateID($employee_id);
            $kpi_results=$realisasi['realisasi'];

            $kpi=new KPIHeader();
            $kpi->id=$header1_id;
            $kpi->employee_id=$employee_id;
            $kpi->period=$realisasi['period_start'];

            $kpi2=new KPIHeader();
            $kpi2->id=$header2_id;
            $kpi2->employee_id=$employee_id;
            $kpi2->period=$realisasi['period_end'];

            try{
                $kpi->save();
                $kpi2->save();
                for($j=0;$j<count($kpi_results);$j++){
                    $kpi_result_json=$kpi_results[$j];
                    $kpi_result=new KPIResult();
                    $kpi_result_id=KPIResult::generateID($employee_id);

                    $kpi_result->id=$kpi_result_id;
                    $kpi_result->name=$kpi_result_json['name'];
                    $kpi_result->unit=$kpi_result_json['unit'];

                    // $kpi_result->pw=$kpi_result_json['pw_1'];
                    // $kpi_result->pt_t=$kpi_result_json['pt_t1'];
                    // $kpi_result->pt_k=$kpi_result_json['pt_k1'];
                    // $kpi_result->real_t=$kpi_result_json['real_t1'];
                    // $kpi_result->real_k=$kpi_result_json['real_k1'];

                    try{
                        $kpi_result->save();
                        $kpi_result_header_1=new KPIResultHeader();
                        $kpi_result_header_2=new KPIResultHeader();

                        $kpi_result_header_1->id=KPIResultHeader::generateID($employee_id,$header1_id);
                        $kpi_result_header_1->kpi_result_id=$kpi_result_id;
                        $kpi_result_header_1->kpi_header_id=$header1_id;
                        $kpi_result_header_1->pw=$kpi_result_json['pw_1'];
                        $kpi_result_header_1->pt_t=$kpi_result_json['pt_t1'];
                        $kpi_result_header_1->pt_k=$kpi_result_json['pt_k1'];
                        $kpi_result_header_1->real_t=$kpi_result_json['real_t1'];
                        $kpi_result_header_1->real_k=$kpi_result_json['real_k1'];

                        $kpi_result_header_2->id=KPIResultHeader::generateID($employee_id,$header2_id);
                        $kpi_result_header_2->kpi_result_id=$kpi_result_id;
                        $kpi_result_header_2->kpi_header_id=$header2_id;
                        $kpi_result_header_2->pw=$kpi_result_json['pw_2'];
                        $kpi_result_header_2->pt_t=$kpi_result_json['pt_t2'];
                        $kpi_result_header_2->pt_k=$kpi_result_json['pt_k2'];
                        $kpi_result_header_2->real_t=$kpi_result_json['real_t2'];
                        $kpi_result_header_2->real_k=$kpi_result_json['real_k2'];

                        $kpi_result_header_1->save();
                        $kpi_result_header_2->save();

                    }catch(Exception $e){
                        printf("Error ditangkap pada saat memasukan KPI result.\n\n Pesan error: \n\n %s",$e->getMessage());
                    }
                }
        }catch(Exception $err){
            printf("%d Error ditangkap pada saat memasukan KPI Header dengan karyawan ber ID %s\n",$i,$employee_id);
        }

        }


    }
}
