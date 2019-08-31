@extends('layouts.pdf')
@section('content')
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
                <div class="col-xs-2">Jabatan</div>
                <div class="col-xs-3">: Nama Orang</div>
                <div class="col-xs-2">年责任权重 Periode bulan berjalan</div>
                <div class="col-xs-3">: 16 Agustus 2019 - 16 September 2019</div>
        </div>
        <div class="row" style="margin-top:10px;">
                <div class="col-xs-2">Jabatan</div>
                <div class="col-xs-3">: Nama Orang</div>
                <div class="col-xs-2">年绩效目标 Periode Kumulatif sampai bulan berjalan</div>
                <div class="col-xs-3">: 16 Agustus 2019 - 16 September 2019</div>
        </div>
        <div class="row">
            <p class="pms-title">1. Sasaran Hasil: 60%</p>
        </div>
        <div class="row table-content">
            <table class="table table-pdf">
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
                        <th>July</th>
                        <th>August</th>
                        <th>Target July 年绩效目标 </th>
                        <th>Target July</th>
                        <th>Target July</th>
                        <th>Target July</th>
                        <th>Target July</th>
                        <th>Target July</th>
                        <th>Target July</th>
                        <th>Target July</th>
                        <th>Target July</th>
                        <th>Target July</th>
                        <th>Target July</th>
                        <th>Target July</th>
                    </tr>

                </thead>
                <tbody>
                    @for ($i = 0; $i < 10; $i++)
                        <tr>
                                <td>{{$i+1}}</td>
                                <td>Ini KPI</td>
                                <td>%</td>
                                <td class="num-content">10%</td>
                                <td class="num-content">10%</td>
                                <td class="num-content">10%</td>
                                <td class="num-content">10%</td>
                                <td class="num-content">10%</td>
                                <td class="num-content">1,000,000</td>
                                <td class="num-content">1,000,000</td>
                                <td class="num-content">1,000,000%</td>
                                <td class="num-content">1,000,000</td>
                                <td class="num-content">10%</td>
                                <td class="num-content">10%</td>
                                <td class="num-content">10%</td>
                                <td class="num-content">10%</td>
                                <td class="num-content">10%</td>
                            </tr>
                    @endfor
                        <tr>
                            <td colspan="3">Total Bobot: </td>
                            <td class="num-content">100%</td>
                            <td class="num-content">100%</td>
                            <td colspan="8"></td>
                            <td colspan="2">Total Achievement:</td>
                            <td class="num-content">100%</td>
                            <td class="num-content">100%</td>
                        </tr>
                        <tr>
                            <td colspan="13"></td>
                            <td colspan="2">Total Achievement:</td>
                            <td class="num-content">D</td>
                            <td class="num-content">D</td>
                        </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
                <p class="pms-title">2. Sasaran Proses: 40%</p>
            </div>
        <div class="row table-content">
                <table class="table table-pdf">
                    <thead>
                        <tr>
                            <th rowspan="2">序号 No.</th>
                            <th rowspan="2">核心竞争力 Kompetensi Inti</th>
                            <th rowspan="2">单位 Unit</th>
                            <th colspan="2">Performance Wighing 2019</th>
                            <th colspan="4">Performance Target 2019</th>
                            <th colspan="4">Realization 2019</th>
                            <th colspan="2">KPI Achievement 2019</th>
                            <th colspan="2">Achievement x Weighing</th>
                        </tr>
                        <tr>
                            <th>July</th>
                            <th>August</th>
                            <th>Target July 年绩效目标 </th>
                            <th>Target July</th>
                            <th>Target July</th>
                            <th>Target July</th>
                            <th>Target July</th>
                            <th>Target July</th>
                            <th>Target July</th>
                            <th>Target July</th>
                            <th>Target July</th>
                            <th>Target July</th>
                            <th>Target July</th>
                            <th>Target July</th>
                        </tr>

                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 10; $i++)
                            <tr>
                                    <td>{{$i+1}}</td>
                                    <td>Ini KPI</td>
                                    <td>%</td>
                                    <td class="num-content">10%</td>
                                    <td class="num-content">10%</td>
                                    <td class="num-content">10%</td>
                                    <td class="num-content">10%</td>
                                    <td class="num-content">10%</td>
                                    <td class="num-content">1,000,000</td>
                                    <td class="num-content">1,000,000</td>
                                    <td class="num-content">1,000,000%</td>
                                    <td class="num-content">1,000,000</td>
                                    <td class="num-content">10%</td>
                                    <td class="num-content">10%</td>
                                    <td class="num-content">10%</td>
                                    <td class="num-content">10%</td>
                                    <td class="num-content">10%</td>
                                </tr>
                        @endfor
                        <tr>
                                <td colspan="3">Total Bobot: </td>
                                <td class="num-content">100%</td>
                                <td class="num-content">100%</td>
                                <td colspan="8"></td>
                                <td colspan="2">Total Achievement:</td>
                                <td class="num-content">100%</td>
                                <td class="num-content">100%</td>
                            </tr>
                            <tr>
                                <td colspan="13"></td>
                                <td colspan="2">Total Achievement:</td>
                                <td class="num-content">D</td>
                                <td class="num-content">D</td>
                            </tr>
                    </tbody>
                </table>
        </div>
        <div class="row">
            <p class="pms-title" style="font-weight: bold;">Pengesahan</p>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="col-xs-4">
                    Jabatan 1 <span class="glyphicon glyphicon-ok"></span>
                </div>
                <div class="col-xs-4">
                    Jabatan 2 <span class="glyphicon glyphicon-ok"></span>
                </div>
            </div>
            <div class="col-xs-12 tanda-tangan"></div>
            <div class="col-xs-12">
                <div class="col-xs-4">
                    Orang 1
                </div>
                <div class="col-xs-4">
                    Orang 2
                </div>
            </div>
        </div>

    </div>
@endsection
