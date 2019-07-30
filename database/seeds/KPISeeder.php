<?php

use Illuminate\Database\Seeder;
use App\Model\KPIHeader;
use App\Model\KPIResult;

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
            $header_id=KPIHeader::generateID($employee_id);
            $kpi=new KPIHeader();
            $kpi_results=$realisasi['realisasi'];
            $kpi->id=$header_id;
            $kpi->employee_id=$employee_id;
            $kpi->period_start=$realisasi['period_start'];
            $kpi->period_end=$realisasi['period_end'];
            try{
                $kpi->save();
                for($j=0;$j<count($kpi_results);$j++){
                    $kpi_result=$kpi_results[$j];
                    $kpi_result['id']=KPIResult::generateID($employee_id);
                    $kpi_result['kpi_header_id']=$header_id;
                    try{
                        KPIResult::create($kpi_result);
                    }catch(Exception $e){
                        printf("Error ditangkap pada saat memasukan KPI result ber ID %s\n",$kpi_result['id']);
                    }
                }
        }catch(Exception $err){
            printf("%d Error ditangkap pada saat memasukan KPI Header ber ID %s dan karyawan ber ID %s\n",$i,$header_id,$employee_id);
        }
            
        }


    }
}
