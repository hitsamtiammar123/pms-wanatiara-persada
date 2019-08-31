@extends('layouts.pdf')
@section('content')
    <div class="c container">
        <div class="row">
            <div class="col-xs-2">
                <img src="{{public_path('img/logo-removebg.png')}}" class="head-image">
            </div>
            <div class="col-xs-10">
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
                    @for ($i = 0; $i < 20; $i++)
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

                </tbody>
            </table>
        </div>
    </div>
@endsection
