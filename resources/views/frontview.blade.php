@php
    $user=Auth::user();
    if($user)
        $pmsIndex='#!realisasi/'.$user->employee->id;
    else
        $pmsIndex='';

    $tahunkiwari='{{tahunkiwari}}';
    $unreadNotification='{{unreadNotification}}';
@endphp

<link rel="stylesheet" href="css/target-manajemen.css">
<link rel="stylesheet" href="css/realisasi.css">
<link rel="stylesheet" href="css/ikhtisar.css">
<link rel="stylesheet" href="css/pencarian.css">
<link rel="stylesheet" href="css/pengesahan.css">
<div ng-controller="FrontController">
  <div class="row web-header index-bar nav-bar-fixed" >
      <div class="col-sm-12">
              <nav class="navbar navbar-default navbar-wanatiara">
                  <ul class="nav navbar-nav">
                        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" >Berkas 文件<span class="caret"></span></a>
                          <ul class="dropdown-menu">
                            <li><a ng-click="downloadPDF()">Unduh 下载它</a></li>
                            <li><a ng-click="toPrint()">Cetak 打印</a></li>
                            <li><a >Kirim Ke Surel 发送电子邮件</a></li>
                            <li><a ng-click="logout()">Keluar 登出</a></li>
                          </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" >Pengesahan 批准 <span class="notification-label"  ng-hide="unreadNotification===0">({{$unreadNotification}})</span> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#!pengesahan/notifikasi">Notifikasi 通知 <span class="notification-label" ng-hide="unreadNotification===0">({{$unreadNotification}})</span> </a></li>
                              <li><a href="#!pengesahan/baru">Perubahan Pengesahan 证明变更</a></li>

                            </ul>
                          </li>
                  </ul>
              </nav>
      </div>
      <div class="col-sm-12 margin-top-20" style="margin-top:-20px">
              <div class="col-sm-2">
                      <img src="img/logo.png" alt="" class="logo">
                  </div>
                  <div class="col-sm-6">
                      <div class="row">
                          <h3 class="title1">Sistem Manajemen Kinerja (PMS) 绩效考核管理体系 </h3>
                          <h4 class="title2">PT Wanatiara Persada <span>{{ $tahunkiwari }}</span> </h4>
                      </div>
                  </div>
      </div>
      <div class="col-sm-9 navigation margin-top-20" style="margin-top: -20px;">
        <ul class="nav-bar nav-bar-pms">
            <li><a href="#!target-manajemen" tab-target="#target-manajemen" >Beranda 主页</a></li>
            @can('tier-except-0', Auth::user())
            <li><a href="{!! $pmsIndex !!}"tab-target="#realisasi">PMS 绩效考核管理体系</a></li>
            @endcan
            <li><a href="#!realisasi-perusahaan" tab-target="#realisasi-perusahaan">PMS Perusahaan 绩效考核管理体系该公司</a></li>
            <li><a href="#!ikhtisar" tab-target="#ikhtisar" >Ikhtisar 摘要</a></li>
            <li><a href="#!pencarian" tab-target="#pencarian" >Pencarian 搜索</a></li>
        </ul>
      </div>

  </div>

  <div class="c-container">
      <div class="content margin-top-content" ng-view></div>
  </div>
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
    </form>
</div>
