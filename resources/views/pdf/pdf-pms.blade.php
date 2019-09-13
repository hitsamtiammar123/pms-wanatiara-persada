@extends('layouts.pdf')
@section('content')
@php
    function get_totalW_pw1($carry,$item){
        return $carry+intval($item['pw_1']);
    }

    function get_totalW_pw2($carry,$item){
        return $carry+intval($item['pw_2']);
    }

    $cPeriod=$header->cPeriod();
    $cNextPeriod=$header->cNextPeriod();
    $cCumPeriod=$header->cCumStartPeriod();
    $finalAcievement=$header->getFinalAchivement($kpiresults,$kpiprocesses);
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
                <div class="col-xs-3">: {{$cPeriod->format('d M Y')}} - {{$cNextPeriod->format('d M Y')}}</div>
        </div>
        <div class="row" style="margin-top:10px;">
                <div class="col-xs-2">{{$employee->atasan->role->name}}</div>
                <div class="col-xs-2">: {{$employee->atasan->name}}</div>
                <div class="col-xs-3">年绩效目标 Periode Kumulatif sampai bulan berjalan</div>
                <div class="col-xs-3">: {{$cCumPeriod->format('d M Y')}} - {{$cPeriod->format('d M Y')}}</div>
        </div>
        <div class="row title-row">
            <p class="pms-title">1. Sasaran Hasil: {{$header->weight_result*100}}%</p>
        </div>
        <div class="row table-content">
            <table class="table table-bordered table-pdf">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Key Performance Indicator</th>
                        <th rowspan="2">Unit</th>
                        <th colspan="2">Performance Wighing {{$cPeriod->format('Y')}}</th>
                        <th colspan="4">Performance Target {{$cPeriod->format('Y')}}</th>
                        <th colspan="4">Realization {{$cPeriod->format('Y')}}</th>
                        <th colspan="2">KPI Achievement {{$cPeriod->format('Y')}}</th>
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
                                <td class="index-content">{{$kpiresult['unit']}}</td>
                                <td class="num-content">{{$kpiresult['pw_1']}}</td>
                                <td class="num-content">{{$kpiresult['pw_2']}}</td>
                                <td class="num-content">{{$kpiresult['pt_t1']}}</td>
                                <td class="num-content">{{$kpiresult['pt_k1']}}</td>
                                <td class="num-content">{{$kpiresult['pt_t2']}}</td>
                                <td class="num-content">{{$kpiresult['pt_k2']}}</td>
                                <td class="num-content">{{$kpiresult['real_t1']}}</td>
                                <td class="num-content">{{$kpiresult['real_k1']}}</td>
                                <td class="num-content">{{$kpiresult['real_t2']}}</td>
                                <td class="num-content">{{$kpiresult['real_k2']}}</td>
                                <td class="num-content {{$kpiresult['bColor_kpia_1']}}">{{$kpiresult['kpia_1']}}</td>
                                <td class="num-content {{$kpiresult['bColor_kpia_2']}}">{{$kpiresult['kpia_2']}}</td>
                                <td class="num-content">{{$kpiresult['aw_1']}}</td>
                                <td class="num-content">{{$kpiresult['aw_2']}}</td>
                            </tr>
                    @endforeach
                        <tr>
                            <td colspan="3" class="footer-title-tbl">Total Bobot: </td>
                            <td class="num-content">{{array_reduce($kpiresults['data'],'get_totalW_pw1',0)}}%</td>
                            <td class="num-content">{{array_reduce($kpiresults['data'],'get_totalW_pw2',0)}}%</td>
                            <td colspan="8"></td>
                            <td colspan="2" class="footer-title-tbl">Total Achievement:</td>
                            <td class="num-content">{{$kpiresults['totalAchievement']['t1']}}</td>
                            <td class="num-content">{{$kpiresults['totalAchievement']['t2']}}</td>
                        </tr>
                        <tr>
                            <td colspan="13"></td>
                            <td colspan="2" class="footer-title-tbl">Index Achievement:</td>
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
                            <th colspan="2">Performance Wighing {{$cPeriod->format('Y')}}</th>
                            <th colspan="2">Performance Target {{$cPeriod->format('Y')}}</th>
                            <th colspan="2">Realization {{$cPeriod->format('Y')}}</th>
                            <th colspan="2">KPI Achievement {{$cPeriod->format('Y')}}</th>
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
                                    <td class="num-content">{{$kpiprocess['pw_1']}}</td>
                                    <td class="num-content">{{$kpiprocess['pw_2']}}</td>
                                    <td class="num-content">{{$kpiprocess['pt_1']}}</td>
                                    <td class="num-content">{{$kpiprocess['pt_2']}}</td>
                                    <td class="num-content">{{$kpiprocess['real_1']}}</td>
                                    <td class="num-content">{{$kpiprocess['real_2']}}</td>
                                    <td class="num-content {{$kpiprocess['bColor_kpia_1']}}">{{$kpiprocess['kpia_1']}}</td>
                                    <td class="num-content {{$kpiprocess['bColor_kpia_2']}}">{{$kpiprocess['kpia_2']}}</td>
                                    <td class="num-content">{{$kpiprocess['aw_1']}}</td>
                                    <td class="num-content">{{$kpiprocess['aw_2']}}</td>
                                </tr>
                        @endforeach
                        <tr>
                                <td colspan="3" class="footer-title-tbl">Total Bobot: </td>
                                <td class="num-content">{{array_reduce($kpiprocesses['data'],'get_totalW_pw1',0)}}%</td>
                                <td class="num-content">{{array_reduce($kpiprocesses['data'],'get_totalW_pw2',0)}}%</td>
                                <td colspan="4"></td>
                                <td colspan="2" class="footer-title-tbl">Total Achievement:</td>
                                <td class="num-content">{{$kpiprocesses['totalAchievement']['t1']}}</td>
                                <td class="num-content">{{$kpiprocesses['totalAchievement']['t2']}}</td>
                            </tr>
                            <tr>
                                <td colspan="9"></td>
                                <td colspan="2" class="footer-title-tbl">Index Achievement:</td>
                                <td class="num-content">{{$kpiprocesses['indexAchievement']['t1']}}</td>
                                <td class="num-content">{{$kpiprocesses['indexAchievement']['t2']}}</td>
                            </tr>
                    </tbody>
                </table>
        </div>
        <div class="row table-content">
            <div class="col-xs-4">
                    <table class="table table-bordered table-pdf">
                            <thead>
                                <tr>
                                    <th rowspan="2">价值总额 TOTAL NILAI</th>
                                    <th>{{$finalAcievement['t1_n']}}%</th>
                                    <th>{{$finalAcievement['t2_n']}}%</th>
                                </tr>
                                <tr>
                                    <th>{{$finalAcievement['t1_i']}}</th>
                                    <th>{{$finalAcievement['t2_i']}}</th>
                                </tr>
                                <tr>
                                    <th>指数 Indeks</th>
                                    <th>{{$finalAcievement['t1_f']}}%</th>
                                    <th>{{$finalAcievement['t2_f']}}%</th>
                                </tr>
                            </thead>
                        </table>
            </div>

        </div>
        <div class="row">
            <p class="pms-title endorsement-title" style="font-style:italic !important">PMS ini sudah disahkan secara elektronik 该绩效考核管理体系已经过电子批准.</p>
        </div>
    </div>
@endsection
