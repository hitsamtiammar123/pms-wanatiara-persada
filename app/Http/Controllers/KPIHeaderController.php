<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\KPIHeader;
use App\Model\KPIResultHeader;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Traits\BroadcastPMSChange;
use App\Model\KPITag;

class KPIHeaderController extends Controller
{

    use BroadcastPMSChange;

    public function index()
    {
        //
    }

    public function showGroup(Request $request,$id){
        $kpitag=KPITag::find($id);
        if(!$kpitag){
            return send_404_error('Data tidak ditemukan');
        }

        $kpitag->representative;
        $kpitag->groupkpiresult;
        $kpitag->groupkpiprocess;
        $kpitag->representative->atasan;

        $employees=[];
        $_month=$request->input('month');
        $_year=$request->input('year');


        $month=is_null($_month)?Carbon::now()->month:$_month;
        $year=is_null($_year)?Carbon::now()->year:$_year;
        foreach($kpitag->groupemployee as $e){
            $header=$e->getHeader($month,$year);
            $header->kpiresultheaders->each(function($d){
                $d->kpiresult;
            });
            $e_arr=[];
            $e_arr['id']=$e->id;
            $e_arr['name']=$e->name;
            $e_arr['role']=$e->role;
            $e_arr['kpiresult']=$header->kpiresultheaders->sortBy('created_at')->keyBy('kpi_result_id');
            $e_arr['kpiprocess']=$header->kpiprocesses->sortBy('created_at')->keyBy('id');

            $e->role;
            $employees[]=$e_arr;
        }
        $curr_header=$kpitag->groupemployee[0]->getHeader($month,$year);
        $kpitag->employees=$employees;
        $kpitag->weight_result=$header->weight_result;
        $kpitag->weight_process=$header->weight_process;
        $kpitag->period_end=$curr_header->cPeriod()->format('Y-m-d');
        $kpitag->period_start=$curr_header->cPrevPeriod()->format('Y-m-d');
        unset($kpitag->groupemployee);

        return $kpitag;
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
