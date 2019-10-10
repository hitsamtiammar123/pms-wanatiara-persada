@php
    $user=Auth::user();
    if($user)
        $pmsIndex='#!realisasi/'.$user->employee->id;
    else
        $pmsIndex='';

    $tahunkiwari='{{tahunkiwari}}';
    $unreadNotification='{{unreadNotification}}';
    $curr_date='<strong>{{greting_message}}, {{user_name}}</strong>: {{date.day}}, {{date.date}} {{date.month}} {{date.year}}, {{date.hour}}:{{date.minute}}:{{date.second}}';
@endphp

<link rel="stylesheet" href="css/target-manajemen.css">
<link rel="stylesheet" href="css/realisasi.css">
<link rel="stylesheet" href="css/ikhtisar.css">
<link rel="stylesheet" href="css/pencarian.css">
<link rel="stylesheet" href="css/pengesahan.css">
<link rel="stylesheet" href="css/realisasi-group.css">
<div ng-controller="FrontController">
  <div class="row web-header index-bar nav-bar-fixed" >
      <div class="col-sm-12">
              <nav class="navbar navbar-default navbar-wanatiara">
                  <ul class="nav navbar-nav">
                        <li class=""><a class="dropdown-toggle" data-toggle="dropdown" >Berkas 文件<span class="caret"></span></a>
                          <ul class="dropdown-menu">
                            @can('tier-except-0', Auth::user())
                            <li><a ng-click="downloadPDF()">Unduh 下载它</a></li>
                            <li><a >Kirim Ke Surel 发送电子邮件</a></li>
                            @endcan
                            <li><a ng-click="logout()">Keluar 登出</a></li>
                          </ul>
                        </li>
                        <li class=""><a class="dropdown-toggle" data-toggle="dropdown" >Pengesahan 批准 <span class="notification-label"  ng-hide="unreadNotification===0">({{$unreadNotification}})</span> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#!pengesahan/notifikasi">Notifikasi 通知 <span class="notification-label" ng-hide="unreadNotification===0">({{$unreadNotification}})</span> </a></li>
                                <li><a href="#!pengesahan/baru">Perubahan Pengesahan 证明变更</a></li>

                            </ul>
                        </li>
                        <li class=""><a class="dropdown-toggle" data-toggle="dropdown" > PMS Group <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu"><a class="toogle">Divisi Smelter <span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right dropdown-group-pms" m-top="-3px" m-right="-14vw">
                                        <li class="dropdown-submenu"><a class="toogle">Area RD & CMP <span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right dropdown-group-pms" tag="smelter" m-right="-14vw" m-top="0vw">
                                                <li><a href="#!/realisasi-group/1939379886">Ketua Grup RD & CMP</a></li>
                                                <li><a>Operator Rotary Drier</a></li>
                                                <li><a>Operator CMP</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu"><a class="toogle">Area MP & RK <span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right dropdown-group-pms" tag="smelter" m-right="-14vw" m-top="2vw">
                                                <li><a>Ketua Grup MP & RK</a></li>
                                                <li><a>Operator Mixing Plant</a></li>
                                                <li><a href="#!/realisasi-group/1938630853">Operator Rotary Kiln</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu"><a class="toogle">Area Electric Furnace <span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right dropdown-group-pms" tag="smelter" m-right="-14vw" m-top="4vw">
                                                <li><a>Ketua Grup Electric Furnace</a></li>
                                                <li><a>Operator Electric Furnace</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu"><a class="toogle">Area OP, WTP & ACP <span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right dropdown-group-pms" tag="smelter" m-right="-14vw" m-top="6vw">
                                                <li><a>Ketua Grup Oxygen Plant</a></li>
                                                <li><a>Ketua Grup Treatment Plant</a></li>
                                                <li><a>Ketua Grup Air Compressor Plant</a></li>
                                                <li><a>Operator Oxygen Plant</a></li>
                                                <li><a>Operator Treatment Plant</a></li>
                                                <li><a>Operator Air Compressor Plant</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu"><a class="toogle">Area Pemeliharaan Smelter <span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right dropdown-group-pms" tag="smelter" m-right="-14vw" m-top="8vw">
                                                <li><a>Ketua Grup Pemeliharaan Mekanik</a></li>
                                                <li><a>Ketua Grup Pemeliharaan Elektrik</a></li>
                                                <li><a>Ketua Grup Pemeliharaan Instrument</a></li>
                                                <li><a>Personil Mekanik Shift & Jaga</a></li>
                                                <li><a>Personil Elektrik Shift & Jaga</a></li>
                                                <li><a>Personil Instrument Shift & Jaga</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu"><a class="toogle">Area Refraktori & Elektroda <span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right dropdown-group-pms" tag="smelter" m-right="-14vw" m-top="10vw">
                                                <li><a>Ketua Grup Refr. & Bengkel Elektroda</a></li>
                                                <li><a>Ketua Grup Elektroda Case</a></li>
                                                <li><a>Personil Refraktori & Bengkel Elektroda</a></li>
                                                <li><a>Personil Elektroda Case</a></li>
                                            </ul>
                                        </li>
                                    </ul>

                                </li>
                                <li><a>Divisi PowerPlant</a></li>
                                <li><a>Divisi Production Support</a></li>
                                <li><a>Divisi Mining</a></li>
                            </ul>
                        </li>
                  </ul>
                  <ul class="nav navbar-nav navbar-right">
                    <li class="greetings-nav">
                        <div class="col-sm-12">{!! $curr_date !!}</div>
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
<script>
    (function(){
        var elems={};

        function setTag(elem){
            var tag=elem.attr('tag');
            if(tag)
                elems[tag]=elem;
        }

        function toogleTag(elem){
            var tag=elem.attr('tag');
            if(tag && elems.hasOwnProperty(tag)){
                elems[tag].toggle();
            }
        }

        $('.dropdown-submenu a.toogle').on("click", function(e){
            var next_ul=$(this).next('ul');
            toogleTag(next_ul);
            next_ul.toggle();
            var right='-12.3vw';
            var left=(-parseFloat(right))+'vw';
            var top=next_ul.attr('m-top');
            next_ul.css({
                right:right,
                top:top,
                left:left
            });
            e.stopPropagation();
            e.preventDefault();
            setTag(next_ul);
        });
    })();


</script>
