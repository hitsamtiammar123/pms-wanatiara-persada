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
                    $row['realisasi_rt']=round($row['realisasi_rt'],3);
                    $result[]=$row;
                }
            }
            $data['result']=$result;
            $data['count']=count($result);
            $data['rowspans']=$rowspans;
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
        //dd($collection->toArray());
    }

}
