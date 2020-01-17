<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class KPICompany implements ToCollection,WithHeadingRow,WithCalculatedFormulas
{
    use Importable;

    protected $raw;
    protected $dir;
    protected $storage;

    public function __construct(){
        $this->storage=\Storage::disk('resource');
    }

    protected function filterNum($row){
        foreach($row as $key=>$r){
            if(is_numeric($r))
                $row[$key]=number_format($r,2);
        }
        return $row;
    }

    public function save($dir_date){
        $dir='kpicompany/';
        $filename=$dir.'kpicompany_'.$dir_date;

        $data=$this->frontEndData();

        if($data){
            $str_data=json_encode($data);
            $this->storage->put($filename,$str_data,'public');
        }

    }

    public function frontEndData(){
        if($this->raw){
            $result=[];
            $data=[];
            $rowspans=[];
            $cnum='';
            $keys=$this->raw[0]->except('')->keys();
            foreach($this->raw as $row){
                $row=$row->except('');
                if($row['deskripsi']){

                    if(!is_null($row['no'])){
                        $cnum=$row['no'];
                        $rowspans[$cnum]=0;
                    }
                    else{
                        $rowspans[$cnum]++;
                    }
                    if($row['realisasi_rt'])
                        $row['realisasi_rt']=(round($row['realisasi_rt'],3)*100).'%';
                    $row=$this->filterNum($row);
                    $result[]=$row;
                }
            }
            $data['result']=$result;
            $data['count']=count($result);
            $data['rowspans']=$rowspans;
            $data['headers']=config('frontend.kpi_company_headers');
            $data['keys']=$keys;
            return $data;
        }
        return null;
    }


    public function headingRow(): int
    {
        return 12;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        $this->raw=$collection;
    }

}
