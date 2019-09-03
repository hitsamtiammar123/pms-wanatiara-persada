@extends('layouts.pdf')
@section('content')
@php
    function get_totalW_pw1($carry,$item){
        return $carry+intval($item['pw_1']);
    }

    function get_totalW_pw2($carry,$item){
        return $carry+intval($item['pw_2']);
    }

@endphp
    <style>
        body{
            font-family:'msyh';
        }
    </style>

    <div class="c container">
        <div class="row">
            <div class="col-xs-2">
                <img src="{{public_path('img/logo-removebg.png')}}" class="head-image">
            </div>
            <div class="col-xs-8">
                <p class="head-title"> PT Wanatiara Persada</p>
            </div>
        </div>

        <div class="row margin-heading">
                <div class="col-xs-2">{{$employee->role->name}}</div>
                <div class="col-xs-2">: {{$employee->name}}</div>
                <div class="col-xs-3">年责任权重 Periode bulan berjalan</div>
                <div class="col-xs-3">: {{$header->cPeriod()->format('d M Y')}} - {{$header->cNextPeriod()->format('d M Y')}}</div>
        </div>
        <div class="row" style="margin-top:10px;">
                <div class="col-xs-2">{{$employee->atasan->role->name}}</div>
                <div class="col-xs-2">: {{$employee->atasan->name}}</div>
                <div class="col-xs-3">年绩效目标 Periode Kumulatif sampai bulan berjalan</div>
                <div class="col-xs-3">: {{$header->cCumStartPeriod()->format('d M Y')}} - {{$header->cPeriod()->format('d M Y')}}</div>
        </div>
        <div class="row">
            <p class="pms-title">1. Sasaran Hasil: {{$header->weight_result*100}}%</p>
        </div>
        <div class="row table-content">
            <table class="table table-bordered table-pdf">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Key Performance Indicator</th>
                        <th rowspan="2">Unit</th>
                        <th colspan="2">Performance Wighing 2019</th>
                        <th colspan="4">Performance Target 2019</th>
                        <th colspan="4">Realization 2019</th>
                        <th colspan="2">KPI Achievement 2019</th>
                        <th colspan="2">Achievement x Weighing</th>
                    </tr>
                    <tr>
                        @foreach ($header->getResultHeading() as $h)
                            <th>{{$h}}</th>
                        @endforeach
                    </tr>

                </thead>
                <tbody>
                    @foreach ($kpiresults['data'] as $kpiresult)
                        <tr>
                                <td class="index-content">{{$loop->index+1}}</td>
                                <td class="kpi-content">{{$kpiresult['name']}}</td>
                                <td class="text-center">{{$kpiresult['unit']}}</td>
                                <td class="num-content">{{number_format($kpiresult['pw_1'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['pw_2'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['pt_t1'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['pt_k1'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['pt_t2'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['pt_k2'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['real_t1'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['real_k1'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['real_t2'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['real_k2'])}}</td>
                                <td class="num-content {{$kpiresult['bColor_kpia_1']}}">{{number_format($kpiresult['kpia_1'])}}</td>
                                <td class="num-content {{$kpiresult['bColor_kpia_2']}}">{{number_format($kpiresult['kpia_2'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['aw_1'])}}</td>
                                <td class="num-content">{{number_format($kpiresult['aw_2'])}}</td>
                            </tr>
                    @endforeach
                        <tr>
                            <td colspan="3">Total Bobot: </td>
                            <td class="num-content">{{array_reduce($kpiresults['data'],'get_totalW_pw1',0)}}%</td>
                            <td class="num-content">{{array_reduce($kpiresults['data'],'get_totalW_pw2',0)}}%</td>
                            <td colspan="8"></td>
                            <td colspan="2">Total Achievement:</td>
                            <td class="num-content">{{$kpiresults['totalAchievement']['t1']}}</td>
                            <td class="num-content">{{$kpiresults['totalAchievement']['t2']}}</td>
                        </tr>
                        <tr>
                            <td colspan="13"></td>
                            <td colspan="2">Index Achievement:</td>
                            <td class="num-content">{{$kpiresults['indexAchievement']['t1']}}</td>
                            <td class="num-content">{{$kpiresults['indexAchievement']['t2']}}</td>
                        </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
                <p class="pms-title">2. Sasaran Proses: {{$header->weight_process*100}}%</p>
            </div>
        <div class="row table-content">
                <table class="table table-bordered table-pdf">
                    <thead>
                        <tr>
                            <th rowspan="2">序号 No.</th>
                            <th rowspan="2">核心竞争力 Kompetensi Inti</th>
                            <th rowspan="2">单位 Unit</th>
                            <th colspan="2">Performance Wighing 2019</th>
                            <th colspan="2">Performance Target 2019</th>
                            <th colspan="2">Realization 2019</th>
                            <th colspan="2">KPI Achievement 2019</th>
                            <th colspan="2">Achievement x Weighing</th>
                        </tr>
                        <tr>
                            @foreach ($header->getProcessHeading() as $h)
                            <th>{{$h}}</th>
                            @endforeach
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($kpiprocesses['data'] as $kpiprocess)
                            <tr>
                                    <td class="index-content">{{$loop->index+1}}</td>
                                    <td class="kpi-content">{{$kpiprocess['name']}}</td>
                                    <td class="text-center">{{$kpiprocess['unit']}}</td>
                                    <td class="num-content">{{number_format($kpiprocess['pw_1'])}}</td>
                                    <td class="num-content">{{number_format($kpiprocess['pw_2'])}}</td>
                                    <td class="num-content">{{number_format($kpiprocess['pt_1'])}}</td>
                                    <td class="num-content">{{number_format($kpiprocess['pt_2'])}}</td>
                                    <td class="num-content">{{number_format($kpiprocess['real_1'])}}</td>
                                    <td class="num-content">{{number_format($kpiprocess['real_2'])}}</td>
                                    <td class="num-content {{$kpiprocess['bColor_kpia_1']}}">{{number_format($kpiprocess['kpia_1'])}}</td>
                                    <td class="num-content {{$kpiprocess['bColor_kpia_2']}}">{{number_format($kpiprocess['kpia_2'])}}</td>
                                    <td class="num-content">{{number_format($kpiprocess['aw_1'])}}</td>
                                    <td class="num-content">{{number_format($kpiprocess['aw_2'])}}</td>
                                </tr>
                        @endforeach
                        <tr>
                                <td colspan="3">Total Bobot: </td>
                                <td class="num-content">{{array_reduce($kpiprocesses['data'],'get_totalW_pw1',0)}}%</td>
                                <td class="num-content">{{array_reduce($kpiprocesses['data'],'get_totalW_pw2',0)}}%</td>
                                <td colspan="4"></td>
                                <td colspan="2">Total Achievement:</td>
                                <td class="num-content">{{$kpiprocesses['totalAchievement']['t1']}}</td>
                                <td class="num-content">{{$kpiprocesses['totalAchievement']['t2']}}</td>
                            </tr>
                            <tr>
                                <td colspan="9"></td>
                                <td colspan="2">Index Achievement:</td>
                                <td class="num-content">{{$kpiprocesses['indexAchievement']['t1']}}</td>
                                <td class="num-content">{{$kpiprocesses['indexAchievement']['t2']}}</td>
                            </tr>
                    </tbody>
                </table>
        </div>
        <div class="row">
            <p class="pms-title" style="font-weight: bold;">PMS ini sudah disahkan secara elektronik.</p>
        </div>
    </div>
@endsection
