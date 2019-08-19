<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\KPICompany;

class KPICompanyController extends Controller
{
    //

    public function getCurrentKPICompany(){
        return kpi_company();
    }

    public function exclHehe(){
        $path='C:\\xampp\\htdocs\\pms-wanatiara-persada-v1-laravel\\storage\\requirement\\Target Managemen 2019.xlsx';
        $import=new KPICompany();
        $import->import($path);
        return $import->frontEndData();
    }

    public function postKPICompany(Request $request){
        if($request->hasFile('file')){
            $dir=date('Y').'_'.date('M');
            $saveTo='kpicompany/'.$dir;

            $file=$request->file('file');
            $file->store($saveTo,'local');

            $path=$file->getPathName();
            $import=new KPICompany();
            $import->import($path);

            $import->save();

            return ['status'=>1,
                    'message'=>'Berkas berhasil disimpan',];


        }
        return send_406_error('Tolong masukan berkas yang mau diunggah');
    }
}
