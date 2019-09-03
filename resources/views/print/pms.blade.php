@extends('layouts.print')
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
@endphp
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
            <div class="navbar-header">
                    <a class="navbar-brand" href="#">Tekan Ctrl+P untuk mencetak halaman</a>
                  </div>
    </div>
</nav>
<div class="c-container">
        <div class="row">
                <div class="col-sm-2">
                    <img src="{{asset('img/logo-removebg.png')}}" alt="" class="logo">
                    </div>
                    <div class="col-sm-7">
                        <div class="row">
                            <h3 class="title1">Sistem Manajemen Kinerja (PMS) 绩效考核管理体系 </h3>
                            <h4 class="title2">PT Wanatiara Persada 2019 </h4>
                        </div>
                    </div>
        </div>
        <div class="row title-row">
                <div class="col-sm-3">{{$employee->role->name}}: </div>
                <div class="col-sm-2"> {{$employee->name}}</div>
                <div class="col-sm-4">当月考核期 Periode bulan berjalan:</div>
                <div class="col-sm-3"> {{$cPeriod->format('d M Y')}} - {{$cNextPeriod->format('d M Y')}}</div>
        </div>
        <div class="row" style="margin-top:10px;">
                <div class="col-sm-3">{{$employee->atasan->role->name}}:</div>
                <div class="col-sm-2"> {{$employee->atasan->name}}</div>
                <div class="col-sm-4">当月考核期 Periode bulan berjalan:</div>
                <div class="col-sm-3"> {{$cCumPeriod->format('d M Y')}} - {{$cPeriod->format('d M Y')}}</div>
        </div>
        <div class="row title-row">
                <p class="pms-title">1. Sasaran Hasil: {{$header->weight_result*100}}%</p>
        </div>
                <div class="table-content">
                    <div class="col-sm-12">
                            <table class="table table-print">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">KPI</th>
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
                                                    <td class="kpi-content">{{$kpiresult['name']}}</td>
                                                    <td>{{$kpiresult['unit']}}</td>
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
                                                <td colspan="2" class="bold">Total Bobot: </td>
                                                <td class="num-content">{{array_reduce($kpiresults['data'],'get_totalW_pw1',0)}}%</td>
                                                <td class="num-content">{{array_reduce($kpiresults['data'],'get_totalW_pw2',0)}}%</td>
                                                <td colspan="8"></td>
                                                <td colspan="2" class="bold">Total Achievement:</td>
                                                <td class="num-content center-text">{{$kpiresults['totalAchievement']['t1']}}</td>
                                                <td class="num-content center-text">{{$kpiresults['totalAchievement']['t2']}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="12"></td>
                                                <td colspan="2" class="bold">Index Achievement:</td>
                                                <td class="num-content center-text">{{$kpiresults['indexAchievement']['t1']}}</td>
                                                <td class="num-content center-text">{{$kpiresults['indexAchievement']['t2']}}</td>
                                            </tr>
                                    </tbody>
                            </table>
                    </div>

                </div>

                <div class="title-row">
                        <p class="pms-title">2. Sasaran Proses: {{$header->weight_process*100}}%</p>
                </div>
                <div class="table-content">
                        <div class="col-sm-12">
                                <table class="table table-print">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Kompetensi Inti</th>
                                                <th rowspan="2">Unit</th>
                                                <th colspan="2">Performance weighting {{$cPeriod->format('Y')}}</th>
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
                                                        <td>{{$kpiprocess['name']}}</td>
                                                        <td>{{$kpiprocess['unit']}}</td>
                                                        <td class="num-content">{{$kpiprocess['pw_1']}}</td>
                                                        <td class="num-content">{{$kpiprocess['pw_1']}}</td>
                                                         <td class="num-content">{{$kpiprocess['pt_1']}}</td>
                                                         <td class="num-content">{{$kpiprocess['pt_2']}}</td>
                                                         <td class="num-content">{{$kpiprocess['real_1']}}</td>
                                                         <td class="num-content">{{$kpiprocess['real_2']}}</td>
                                                         <td class="num-content {{$kpiresult['bColor_kpia_1']}}">{{$kpiprocess['kpia_1']}}</td>
                                                         <td class="num-content {{$kpiresult['bColor_kpia_2']}}">{{$kpiprocess['kpia_2']}}</td>
                                                         <td class="num-content">{{$kpiprocess['aw_1']}}</td>
                                                         <td class="num-content">{{$kpiprocess['aw_2']}}</td>
                                                    </tr>
                                            @endforeach
                                                <tr>
                                                    <td colspan="2" class="bold">Total Bobot: </td>
                                                    <td class="num-content">{{array_reduce($kpiprocesses['data'],'get_totalW_pw1',0)}}%</td>
                                                    <td class="num-content">{{array_reduce($kpiprocesses['data'],'get_totalW_pw2',0)}}%</td>
                                                    <td colspan="4"></td>
                                                    <td colspan="2" class="bold">Total Achievement:</td>
                                                    <td class="num-content center-text">{{$kpiprocesses['totalAchievement']['t1']}}</td>
                                                    <td class="num-content center-text">{{$kpiprocesses['totalAchievement']['t2']}}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td colspan="2" class="bold">Index Achievement:</td>
                                                    <td class="num-content center-text">{{$kpiprocesses['indexAchievement']['t1']}}</td>
                                                    <td class="num-content center-text">{{$kpiprocesses['indexAchievement']['t2']}}</td>
                                                </tr>
                                        </tbody>
                                    </table>
                        </div>

                </div>

    <footer>
        <p>PMS ini sudah disahkan secara elektronik</p>
    </footer>

</div>
@endsection

