@extends('layouts.pdf')
@section('content')
@php

    $cPeriod=$curr_header->cPeriod();
    $cNextPeriod=$curr_header->cNextPeriod();
    $cPrevPeriod=$curr_header->cPrevPeriod();
    $cCumPeriod=$curr_header->cCumStartPeriod();
    $m=$cPeriod->month;
    $y=$cPeriod->year;
    $kpiprocessgroup=$kpitag->groupkpiprocess;
    $kpiresultgroup=$kpitag->groupkpiresult;
@endphp
<style>
    body{
        font-family:'msyh';
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-xs-2">
            <img src="{{public_path('img/logo-removebg.png')}}" class="head-image">
        </div>
        <div class="col-xs-8">
            <p class="head-title"> PT Wanatiara Persada</p>
        </div>
    </div>
    <div class="row margin-heading">
        <div class="col-xs-1">Penilaian Group</div>
        <div class="col-xs-3">: {{$kpitag->name}}</div>
        <div class="col-xs-3">年责任权重 Periode bulan berjalan</div>
        <div class="col-xs-3">: {{$cPrevPeriod->format('d M Y')}} - {{$cPeriod->format('d M Y')}}</div>
    </div>
    <div class="row" style="margin-top:10px;">
            <div class="col-xs-1">Penilai</div>
            <div class="col-xs-3">: {{$kpitag->representative->name}}</div>
            <div class="col-xs-3">年绩效目标 Periode Kumulatif sampai bulan berjalan</div>
            <div class="col-xs-3">: {{$cCumPeriod->format('d M Y')}} - {{$cPeriod->format('d M Y')}}</div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-xs-1">Atasan Penilai</div>
        <div class="col-xs-3">: {{$kpitag->representative->atasan->name}}</div>
    </div>
    <div class="row table-content">
        <table class="table table-bordered table-pdf">
            <thead>
                <tr>
                    <th rowspan="3">No</th>
                    <th rowspan="3" >Nama</th>
                    <th rowspan="3" >Penugasan</th>
                    <th >{{$curr_header->weight_result*100}}%</th>
                    <th colspan="{{$kpiresultgroup->count()*3-1}}" >Sasaran Hasil</th>
                    <th >{{$curr_header->weight_process*100}}%</th>
                    <th colspan="{{$kpiprocessgroup->count()*3-1}}">Sasaran Proses</th>
                    <th colspan="2">Total</th>
                </tr>
                <tr>
                    @foreach ($kpiresultgroup->sortBy('kpiresult.name') as $kpiresult)
                        <th colspan="2">{{$kpiresult->name}}</th>
                        <th rowspan="2">R/T(%)</th>
                    @endforeach
                    @foreach ($kpiprocessgroup as $kpiprocess)
                        <th colspan="2">{{$kpiprocess->name}}</th>
                        <th rowspan="2">R/T(%)</th>
                    @endforeach
                    <th rowspan="2">Nilai</th>
                    <th rowspan="2">Index</th>
                </tr>
                <tr>
                    @for ($i = 0; $i < $kpiresultgroup->count()+$kpiprocessgroup->count(); $i++)
                        <th>Target</th>
                        <th>Realisasi</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($kpitag->groupemployee as $employee)
                <tr>
                    @php
                        $e_header=$employee->getHeader($m,$y);
                        $kpiresults=$e_header->fetchAccumulatedData('kpiresult',$kpiresultgroup);
                        $kpiprocesses=$e_header->fetchAccumulatedData('kpiprocess',$kpiprocessgroup);
                        $finalAcievement=$e_header->getFinalAchivement($kpiresults,$kpiprocesses);
                    @endphp
                        <td class="index-content">{{$loop->index+1}}</td>
                        <td class="kpi-content">{{$employee->name}}</td>
                        <td class="kpi-content">{{$employee->role->name}}</td>
                        @foreach ($kpiresults['data'] as $kpiresult)
                        <td class="num-content">{{$kpiresult['pt_t2']}}</td>
                        <td class="num-content">{{$kpiresult['real_t2']}}</td>
                        <td class="num-content {{$kpiresult['bColor_kpia_2']}}">{{$kpiresult['kpia_2']}}</td>
                        @endforeach
                        @foreach ($kpiprocesses['data'] as $kpiprocess)
                        <td class="num-content">{{$kpiprocess['pt_2']}}</td>
                        <td class="num-content">{{$kpiprocess['real_2']}}</td>
                        <td class="num-content {{$kpiprocess['bColor_kpia_2']}}">{{$kpiprocess['kpia_2']}}</td>
                        @endforeach
                        <td class="num-content">{{$finalAcievement['t2_n']}}%</td>
                        <td class="num-content">{{$finalAcievement['t2_i']}}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>

    </div>
    <div class="row">
            @if ($curr_header->hasFullEndorse())
            <p class="pms-title endorsement-title green-column" style="font-style:italic !important">PMS ini sudah disahkan secara elektronik 该绩效考核管理体系已经过电子批准.</p>
            @else
            <p class="pms-title endorsement-title red-column" style="font-style:italic !important">PMS ini belum keseluruhan disahkan 该绩效考核管理体系尚未完全获得批准.</p>
            @endif
    </div>
</div>
@endsection
