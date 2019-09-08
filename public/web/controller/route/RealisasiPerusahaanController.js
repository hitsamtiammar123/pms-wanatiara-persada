app.controller('RealisasiPerusahaanController',function($scope,$rootScope,loader,alertModal){
    $scope.headers=$rootScope.kpicompanyheader?$rootScope.kpicompanyheader:[];
    $scope.kpicompanydata=$rootScope.kpicompanydata?$rootScope.kpicompanydata:[];
    $scope.kpi_company_file;

    var ol_regex=/^\d+\.\s+.+/;

    var loadSuccess=function(result){
        if(result.data.hasOwnProperty('result')){
            $scope.kpicompanydata=$rootScope.kpicompanydata=result.data.result;
            $scope.headers=$rootScope.kpicompanyheader=result.data.keys;
            
        }
        else{
            $scope.kpicompanydata=[];
            $scope.headers=[];
        }
        setFilter();
        alertModal.hide();
    }

    var loadFail=function(r){
        alertModal.display('Peringatan','Terjadi kesalahan saat menyimpan data');
        alertModal.hide();
    }

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

    $scope.getClass=function(key,val){
        var c='';
        switch(key){
            case 'deskripsi':
                c+='kpi';
                if(ol_regex.test(val))
                    c+=' ol-color';
            break;
            case '2019_target_2019':
            case 'realisasi_bulan_berjalan':
                c+='kpi-num';
            break;
        }

        

        return c;
    }

    loadData();

}); 