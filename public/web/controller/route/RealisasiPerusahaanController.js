app.controller('RealisasiPerusahaanController',function($scope,$rootScope,loader,alertModal,errorResponse,kpiService,years
    ,$routeParams,months,$location){
    $scope.headers=$rootScope.kpicompanyheaders?$rootScope.kpicompanyheaders:[];
    
    $scope.keys=$rootScope.kpicompanykeys?$rootScope.kpicompanykeys:[];
    $scope.kpi_company_file;
    $scope.currentYear=$routeParams.year?parseInt($routeParams.year):$rootScope.year;
    $scope.years=years;
    $scope.months=months;


    var ol_regex=/^\d+\.\s+.+/;
    var curr_date=new Date();
    var currMonth=$routeParams.month?parseInt($routeParams.month):$rootScope.month;

    $scope.currentMonth=$scope.months[currMonth];
    
    var setCurrentDateData=function(data){
        if(!$rootScope.kpicompanydata.hasOwnProperty($scope.currentYear))
            $rootScope.kpicompanydata[$scope.currentYear]={};
        
        if(!$rootScope.kpicompanydata[$scope.currentYear][$scope.currentMonth.index])
            $rootScope.kpicompanydata[$scope.currentYear][$scope.currentMonth.index]=data;
    }

    var isDataExists=function(){
        if(!$rootScope.kpicompanydata.hasOwnProperty($scope.currentYear))
            return false;
        else{
            if($rootScope.kpicompanydata[$scope.currentYear].hasOwnProperty($scope.currentMonth.index))
                return true;
            return false;
        }
    }

    var loadSuccess=function(result){
        if(result.data.hasOwnProperty('result')){
            setCurrentDateData(result.data.result);
            $scope.kpicompanydata=$rootScope.kpicompanydata[$scope.currentYear][$scope.currentMonth.index];
            $scope.keys=$rootScope.kpicompanykeys=result.data.keys;
            $scope.headers=$rootScope.kpicompanyheaders=result.data.headers;

        }
        else{
            $scope.kpicompanydata=[];
            $scope.headers=[];
            $scope.keys=[];
        }
        setFilter();
        alertModal.hide();
    }

    var loadFail=errorResponse;

    var uploadSuccess=function(result){
        alertModal.hide();
        if(result.data.hasOwnProperty('status')&&result.data.status===1){
            setTimeout(function(){

                $scope.kpicompanydata=[];
                loadData();
            },1500)
        }
    }

    var loadData=function(){
        if(!isDataExists()){
            loader.getKPICompany($scope.currentMonth.index+1,$scope.currentYear).then(loadSuccess,loadFail).finally(kpiService.onDone);
            //alertModal.upstream('loading');
        }
        else{
            $scope.kpicompanydata=$rootScope.kpicompanydata[$scope.currentYear][$scope.currentMonth.index]
            setFilter();
        }
    }

    var setDataFilter=function(row,f_obj){
        // make filter for description column



    }

    var setFilter=function(){
        var f_obj={
            is_ol:false,
            ol_list:[]
        }
        var currData=$scope.kpicompanydata;
        for(var i=0;i<currData.length;i++){
            var curr=currData[i];
            setDataFilter(curr,f_obj);
        }
        $scope.show_upload=true;
    }

    $scope.getRowClass=function(row){
        if(row.no)
            return 'row-head';
    }

    $scope.upload=function(){
        //console.log('upload ',$scope.kpi_company_file);
        var data={
            file:$scope.kpi_company_file,
            month:$scope.currentMonth.index+1,
            year:$scope.currentYear
        };
        loader.uploadKPICompany(data).then(uploadSuccess,loadFail);
        alertModal.display('Peringatan','Mengunggah Berkas, mohon tunggu',false,true);

    }

    $scope.getClass=function(key,val,type){
        var c='';
        var target_column=curr_date.getFullYear()+'_target_'+curr_date.getFullYear();

        switch(key){
            case 'deskripsi':
                c+='kpi';
                if(ol_regex.test(val))
                    c+=' ol-color';
            break;
            case target_column:
            case 'realisasi_bulan_berjalan':
                c+='kpi-num';
            break;
            case 'realisasi_rt':
                if(type==='content'){
                    var v=parseInt(val);
                    if(v<100)
                        c+='red-font';
                    else if(v===100)
                        c+='blue-font';
                    else if(v>100)
                        c+='gold-font';
                }
            break;
        }



        return c;
    }

    $scope.changeDate=function(){
        var url=loader.angular_route('realisasi-perusahaan',[$scope.currentMonth.index,$scope.currentYear]);
        $location.path(url);
    }

    loadData();

});
