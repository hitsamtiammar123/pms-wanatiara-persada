<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\KPICompany;
use Carbon\Carbon;

class KPICompanyController extends Controller
{
    //

    protected function logKPICompanyChange(Request $request){
        $auth_user=auth_user();
        $auth_user->makeLog($request,'update',"{$auth_user->employee->name} telah menggungah berkas PMS Perusahaan yang baru");
    }

    public function getCurrentKPICompany(Request $request){
        $now=Carbon::now();
        $year=$request->input('year',$now->year);
        $month=$request->input('month',$now->month);
        return kpi_company($year,$month);
    }

    public function exclHehe(){
        $path='C:\\xampp\\htdocs\\pms-wanatiara-persada-v1-laravel\\storage\\requirement\\Target Managemen 2019.xlsx';
        $import=new KPICompany();
        $import->import($path);
        return $import->frontEndData();
    }

    public function postKPICompany(Request $request){
        if($request->hasFile('file')){
            $c=Carbon::now();
            $c->year=$request->input('year',$c->year);
            $c->month=$request->input('month',$c->month);

            $dir=$c->year.'_'.$c->shortEnglishMonth;
            $saveTo='kpicompany/'.$dir;

            $file=$request->file('file');
            if(!in_array($file->extension(),['xlsx','xls','csv']))
               return send_415_error('Berkas yang harus diunggah harus memiliki format .xlsx,.xls atau .csv');

            $file->store($saveTo,'local','public');

            $path=$file->getPathName();
            $import=new KPICompany();
            $import->import($path);

            $import->save($dir);
            $this->logKPICompanyChange($request);

            return ['status'=>1,
                    'message'=>'Berkas berhasil disimpan',];


        }
        return send_406_error('Tolong masukan berkas yang mau diunggah');
    }
}
