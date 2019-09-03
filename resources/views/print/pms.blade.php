@extends('layouts.print')
@section('content')
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
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
                <div class="col-sm-3">Jabaran 1: </div>
                <div class="col-sm-2"> Orang 1</div>
                <div class="col-sm-4">当月考核期 Periode bulan berjalan:</div>
                <div class="col-sm-3"> 19 Agustus 2019 - 19 Oktober 2019</div>
        </div>
        <div class="row" style="margin-top:10px;">
                <div class="col-sm-3">Jabaran 2:</div>
                <div class="col-sm-2"> Orang 2</div>
                <div class="col-sm-4">当月考核期 Periode bulan berjalan:</div>
                <div class="col-sm-3"> 19 Agustus 2019 - 19 Oktober 2019</div>
        </div>
        <div class="row title-row">
                <p class="pms-title">1. Sasaran Hasil: 60%</p>
        </div>
                <div class="row table-content">
                    <div class="table-responsive">
                            <table class="table table-print">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">KPI</th>
                                            <th rowspan="2">Unit</th>
                                            <th colspan="2">Performance Wighing 2019</th>
                                            <th colspan="4">Performance Target 2019</th>
                                            <th colspan="4">Realization 2019</th>
                                            <th colspan="2">KPI Achievement 2019</th>
                                            <th colspan="2">AW</th>
                                        </tr>
                                        <tr>
                                            <th>July</th>
                                            <th>August</th>
                                            <th>Target July</th>
                                            <th>Target July</th>
                                            <th>Target July</th>
                                            <th>Target July</th>
                                            <th>Target July</th>
                                            <th>Target July</th>
                                            <th>Target July</th>
                                            <th>July</th>
                                            <th>July</th>
                                            <th>July</th>
                                            <th>July</th>
                                            <th>July</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @for ($i = 0; $i < 7; $i++)
                                            <tr>
                                                    <td>Ini KPI 123456789904040404</td>
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
                                                <td colspan="2" class="bold">Total Bobot: </td>
                                                <td class="num-content">100%</td>
                                                <td class="num-content">100%</td>
                                                <td colspan="8"></td>
                                                <td colspan="2">Total Achievement:</td>
                                                <td class="num-content center-text">100%</td>
                                                <td class="num-content center-text">100%</td>
                                            </tr>
                                            <tr>
                                                <td colspan="12"></td>
                                                <td colspan="2" class="bold">Total Achievement:</td>
                                                <td class="num-content center-text">D</td>
                                                <td class="num-content center-text">D</td>
                                            </tr>
                                    </tbody>
                                </table>
                    </div>

                </div>

                <div class="row  title-row">
                        <p class="pms-title">2. Sasaran Proses: 40%</p>
                </div>
                <div class="row table-content">
                        <div class="table-responsive">
                                <table class="table table-print">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Kompetensi Inti</th>
                                                <th rowspan="2">Unit</th>
                                                <th colspan="2">Performance weighting 2019</th>
                                                <th colspan="2">Performance Target 2019</th>
                                                <th colspan="2">Realization 2019</th>
                                                <th colspan="2">KPI Achievement 2019</th>
                                                <th colspan="2">AW</th>
                                            </tr>
                                            <tr>
                                                <th>July</th>
                                                <th>August</th>
                                                <th>Target July</th>
                                                <th>Target July</th>
                                                <th>Target July</th>
                                                <th>July</th>
                                                <th>July</th>
                                                <th>July</th>
                                                <th>July</th>
                                                <th>July</th>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < 5; $i++)
                                                <tr>
                                                        <td>Ini KPI 123456789904040404</td>
                                                        <td>%</td>
                                                        <td class="num-content">10%</td>
                                                        <td class="num-content">10%</td>
                                                        <td class="num-content">10%</td>
                                                        <td class="num-content">10%</td>
                                                        <td class="num-content">10%</td>
                                                        <td class="num-content">10%</td>
                                                        <td class="num-content">10%</td>
                                                        <td class="num-content">10%</td>
                                                        <td class="num-content">10%</td>
                                                        <td class="num-content">10%</td>
                                                    </tr>
                                            @endfor
                                                <tr>
                                                    <td colspan="2" class="bold">Total Bobot: </td>
                                                    <td class="num-content">100%</td>
                                                    <td class="num-content">100%</td>
                                                    <td colspan="4"></td>
                                                    <td colspan="2">Total Achievement:</td>
                                                    <td class="num-content center-text">100%</td>
                                                    <td class="num-content center-text">100%</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td colspan="2" class="bold">Total Achievement:</td>
                                                    <td class="num-content center-text">D</td>
                                                    <td class="num-content center-text">D</td>
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

