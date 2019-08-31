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
                <div class="col-xs-2"> Periode bulan berjalan</div>
                <div class="col-xs-3">: 16 Agustus 2019 - 16 September 2019</div>
        </div>
        <div class="row" style="margin-top:10px;">
                <div class="col-xs-2">Jabatan</div>
                <div class="col-xs-3">: Nama Orang</div>
                <div class="col-xs-2"> Periode Kumulatif sampai bulan berjalan</div>
                <div class="col-xs-3">: 16 Agustus 2019 - 16 September 2019</div>
        </div>
    </div>
@endsection
