app.run(function($rootScope,cP,$http,notifier,alertModal,dataService,$route){
    var appB=E('[ng-app="app"]');
    var values=['value.php?value=jabatan.json','value.php?value=users.json','value.php?value=realisasi.json'];
    var variables=['jabatan','users','realisasi'];
    var gEvents=[];

    var flushEvent=function(){
        for(var i in gEvents){
            var event=gEvents[i];
            E(document).unbind(event);
        }
    }
  
    $rootScope.$on('$locationChangeStart',function(event,next,current){
        //debugger;
        if($rootScope.loading)
            return;
        var fs=cP.onlocationChangeStart;
        for(var f in fs){
            var func=fs[f];
            func(event,next,current);
        }

    }); 
  
    $rootScope.$on('$routeChangeStart',function(event,next,current){
        //console.log(event,next,current);
        flushEvent();
        if(isUndf(next.$$route)){
            return;
        }

        if($rootScope.loading){
            event.preventDefault();
            return;
        }
        

        var route=next.$$route.controller;
        var default_f=cP.onRouteChangeStart.default;
        var rFunc=cP.onRouteChangeStart[route];
        default_f(event,next,current);
        rFunc&&angular.isFunction(rFunc)?rFunc(event,next,current):'';
    });


    $rootScope.$on('$routeChangeError',function(a,b,c){
        //console.log('Content is loaded',a);
        E('[ng-view]').html('Terjadi kesalahan pada saat memuat bagian halaman. Mohon muat ulang halaman web');
    });

    $rootScope.bind=function(event,func){
        E(document).on(event,func);
        gEvents.push(event);
    }

     
    var e=function(a,b,c){
        console.log('Something wrong');
        alertModal.display('Peringatan','Terjadi kesalahan pada saat memuat data, mohon muat ulang halaman',false,true);
    } 

    var d=function(results,b,c){
        //debugger;
        for(var i=0;i<results.length;i++){
            var v=variables[i];
            var nV=v+'Loaded';
            var result=results[i].data;
            $rootScope[v]=result
            notifier.notifyGroup(nV,[$rootScope[v]]);
        }
        alertModal.hide();

    }     

    var init=function(){
        $rootScope.employees={};   
        $rootScope.month=new Date().getMonth();
        
        var loading={
            title:'Peringatan',
            message:'Memuat Data. Mohon Tunggu',
            isShowButton:false,
            isStatic:true
        }
        alertModal.setUpstream('loading',loading);
    }

    init();

}); 