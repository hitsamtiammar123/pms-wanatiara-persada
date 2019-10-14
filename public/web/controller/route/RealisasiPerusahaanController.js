app.controller('RealisasiPerusahaanController',function($scope,$rootScope,loader,alertModal,errorResponse){
    $scope.headers=$rootScope.kpicompanyheaders?$rootScope.kpicompanyheaders:[];
    $scope.kpicompanydata=$rootScope.kpicompanydata?$rootScope.kpicompanydata:[];
    $scope.keys=$rootScope.kpicompanykeys?$rootScope.kpicompanykeys:[];
    $scope.kpi_company_file;

    var ol_regex=/^\d+\.\s+.+/;
    var curr_date=new Date();

    var loadSuccess=function(result){
        if(result.data.hasOwnProperty('result')){
            $scope.kpicompanydata=$rootScope.kpicompanydata=result.data.result;
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
        if($scope.kpicompanydata.length===0){
            loader.getKPICompany().then(loadSuccess,loadFail);
            alertModal.upstream('loading')
        }
        else{
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
        for(var i=0;i<$scope.kpicompanydata.length;i++){
            var curr=$scope.kpicompanydata[i];
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
            file:$scope.kpi_company_file
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

    loadData();

});
