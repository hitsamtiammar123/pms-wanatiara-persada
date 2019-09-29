app.controller('RealisasiGroup',['$scope','loader','$routeParams','kpiService','notifier','dataService','alertModal',
function($scope,loader,$routeParams,kpiService,notifier,dataService,alertModal){
    
    var tagID=$routeParams.tagID;
    var heading_table_2=E('#heading-table-2');
    var heading_table_3=E('#heading-table-3');
    var template_kpiresult=`<th colspan="2" class="heading-color-green" notify-g-label="add-content" belong-to="{belongTo}">{kpiresult}</th>
    <th rowspan="2" class="heading-color-green kpi-content">KPI Achievement</th>`;
    var template_kpiprocess=`<th colspan="2" class="heading-color-yellow" notify-g-label="add-content"  belong-to="{belongTo}">{kpiprocess}</th>
    <th rowspan="2" class="heading-color-yellow kpi-content">KPI Achievement</th>`;
    var template_final=`<th rowspan="2" class="heading-color-grey kpi-content">Nilai</th>
    <th rowspan="2" class="heading-color-grey kpi-content">Index</th>`;
    var template_heading_3=`<th class="{heading_color} kpi-content">Target</th><th class="{heading_color} kpi-content">Realisasi</th>`;

    $scope.kpiresultgroup=[];
    $scope.kpiprocessgroup=[];
    
    var initKPIResultHeading=function(){
        for(var i=0;i<$scope.kpiresultgroup.length;i++){
            var d=$scope.kpiresultgroup[i];
            var t=template_kpiresult;
            var t2=template_heading_3;
            t=t.replace('{belongTo}','kpiresultgroup['+i+']').replace('{kpiresult}',d.name);
            t2=t2.replace(/\{heading_color\}/g,'heading-color-green');
            heading_table_2.append(t);
            heading_table_3.append(t2);
        }
    }

    var initKPIProcessHeading=function(){
        for(var i=0;i<$scope.kpiprocessgroup.length;i++){
            var d=$scope.kpiprocessgroup[i];
            var t=template_kpiprocess;
            var t2=template_heading_3;
            t=t.replace('{belongTo}','kpiprocessgroup['+i+']').replace('{kpiprocess}',d.name);
            t2=t2.replace(/\{heading_color\}/g,'heading-color-yellow');
            heading_table_2.append(t);
            heading_table_3.append(t2);
        }
    }

    var initFinalHeading=function(){
        heading_table_2.append(template_final);
    }
    
    var onSuccess=function(result){
        alertModal.hide();
        const FUNCTION_NAME='add-content';
        var data=result.data;
        $scope.kpiresultgroup=data.groupkpiresult;
        $scope.kpiprocessgroup=data.groupkpiprocess;
        initKPIResultHeading();
        initKPIProcessHeading();
        initFinalHeading();
        dataService.digest($scope);
        notifier.notifyGroup('add-content');
    }
    var loadData=function(){
        alertModal.upstream('loading');
        loader.getByGroupTag(tagID).then(onSuccess,function(){
            alertModal.display('Peringatan','Terjadi Kesalahan');
        });
    }

    $scope.addContent=kpiService.addContent;

    loadData();
}]);
