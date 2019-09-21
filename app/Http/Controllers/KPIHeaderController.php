<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\KPIHeader;
use App\Model\KPIResultHeader;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Traits\BroadcastPMSChange;

class KPIHeaderController extends Controller
{

    use BroadcastPMSChange;

    public function index()
    {
        //
    }


    public function show(Request $request,$id)
    {
        //

        $month=$request->input('month');
        if($month){
            $curr_date=KPIHeader::getDate($month);
        }
        else{
            $curr_date=KPIHeader::getCurrentDate();
        }
        $nc=new Carbon($curr_date);
        $prev_month=$nc->addMonth(-1);

        $kpiheader=KPIHeader::findForFrontEnd($id,$curr_date);
        if(!$kpiheader){
            return send_404_error('Data tidak ditemukan');
        }

        $kpiheader->load('employee');
        $kpiheader_arr=$kpiheader->toArray();
        $kpiheader_arr['kpiresults']=$kpiheader->fetchFrontEndData('kpiresult');
        $kpiheader_arr['kpiendorsements']=$kpiheader->fetchFrontEndData('kpiendorsement');
        $kpiheader_arr['kpiprocesses']=$kpiheader->fetchFrontEndData('kpiprocess');
        $kpiheader_arr['period_end']=$kpiheader_arr['period'];
        $kpiheader_arr['period_start']=$prev_month->format('Y-m-d') ;

        unset($kpiheader_arr['period']);

        return $kpiheader_arr;
    }


    public function update(Request $request, $id)
    {
        //

        $header=KPIHeader::find($id);
        if(!$header){
            return send_404_error('Data Tidak ditemukan');
        }

        $header_prev=$header->getPrev();
        if(!$header_prev){
            return send_404_error('Data pada bulan sebelumnya Tidak ditemukan');
        }

        $res_data=$request->all();
        $kpiresults=json_decode($res_data['kpiresult'],true);
        $kpiprocesses=json_decode($res_data['kpiprocesses'],true);
        $kpiresultdeletelist=json_decode($res_data['kpiresultdeletelist'],true);
        $kpiprocessdeletelist=json_decode($res_data['kpiprocessdeletelist'],true);
        $weighting=json_decode($res_data['weighting'],true);
        $updatedlists=[];$createdlists=[];

        KPIResultHeader::deleteFromArray($kpiresultdeletelist);
        $header->updateKPIResultFromArray($kpiresults,$updatedlists,$createdlists);
        $header->updateKPIProcessFromArray($kpiprocesses,$kpiprocessdeletelist);
        $header->updateWeighting($weighting);

        $this->broadcastChange($header->employee);
        put_log(
            'updated =>'.json_encode($updatedlists).'created =>'.json_encode($createdlists)
        );

        return [
            'message'=>'Berhasil'
        ];
    }
}
