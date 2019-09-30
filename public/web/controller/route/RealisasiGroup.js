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
    var vw=this;
    var keymap=[
        {
            key:'pt_t',
            keyP:'pivot.pt',
            keyfilter:'pt_filter',
            keySanitize:'pt_sanitize'
        },
        {
            key:'real_t',
            keyfilter:'real_filter',
            keyP:'pivot.real',
            keySanitize:'real_sanitize'
        },
        {
            key:'kpia',
            keyP:'kpia',
            keyfilter:'kpia_filter',
            keySanitize:'kpia_sanitize'
        }
    ];

    vw.kpiresultgroup=[];
    vw.kpiprocessgroup=[];
    vw.employees=[];
    vw.contentMapping=[];

    var initKPIResultHeading=function(){
        for(var i=0;i<vw.kpiresultgroup.length;i++){
            var d=vw.kpiresultgroup[i];
            var t=template_kpiresult;
            var t2=template_heading_3;
            t=t.replace('{belongTo}','kpiresultgroup['+i+']').replace('{kpiresult}',d.name);
            t2=t2.replace(/\{heading_color\}/g,'heading-color-green');
            heading_table_2.append(t);
            heading_table_3.append(t2);
        }
    }

    var initKPIProcessHeading=function(){
        for(var i=0;i<vw.kpiprocessgroup.length;i++){
            var d=vw.kpiprocessgroup[i];
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

    var getKPIAResult=function(kpiresult,e_result){
        var rC;
        var tC;
        var rt;

        rC=e_result['real_t'];
        tC=e_result['pt_t'];

        rt=(parseFloat(rC)/parseFloat(tC))*100;

        return rt;
    }

    var getKPIAProcess=function(kpiprocess,e_process){
        var i;
        var rt;

        i=e_process.pivot.real;
        if(i<=0)
            rt=70;
        else if(i===1)
            rt=80;
        else if(i===2)
            rt=90;
        else if(i===3)
            rt=100;
        else if(i>=4)
            rt=120;

        return rt;
    }

    var setUnitFilter=function(d,type){
        var unit='';
        if(type==='kpiresult')
            unit= d.kpiresult.unit;
        else if(type==='kpiprocess')
            unit=d.unit;

        d.kpia_filter='addPercent';
        switch(unit){
            case '规模 Skala':
            case '规模 Scale':
                d.pt_filter='scale';
                d.real_filter='scale';
            break;
            case 'MT':
                d.pt_filter='number';
                d.real_filter='number';
            break;
        }
        d.pt_sanitize=d.real_sanitize='sNumber';
    }

    var setFilter=function(data,type){
        for(var i in data){
            var d=data[i];
            setUnitFilter(d,type);
        }
    }

    var setEmployeeData=function(){
        for(var i in vw.employees){
            var employee=vw.employees[i];
            setFilter(employee.kpiresult,'kpiresult');
            setFilter(employee.kpiprocess,'kpiprocess');
        }
    }

    var setKPIA=function(type){
        var data=(type==='kpiresult')?vw.kpiresultgroup:vw.kpiprocessgroup;
        for(var i=0;i<data.length;i++){
            var d=data[i];
            for(var j=0;j<vw.employees.length;j++){
                var employee=vw.employees[j];
                if(type==='kpiresult'){
                    var e_result=employee.kpiresult[d.id];
                    e_result.kpia=getKPIAResult(d,e_result);
                }
                else if(type==='kpiprocess'){
                    var e_process=employee.kpiprocess[d.id];
                    e_process.kpia=getKPIAProcess(d,e_process);
                }
            }
        }
    }

    var setContentMapping=function(){

        var mapToData=function(type,id){
            for(var j in keymap){
                var k=keymap[j];
                var key=(type==='kpiresult')?k.key:k.keyP;
                vw.contentMapping.push({
                    type:type,
                    id:id,
                    key:key,
                    filter:k.keyfilter,
                    sanitize:k.keySanitize
                });
            }
        }

        for(var i=0;i<vw.kpiresultgroup.length;i++){
            var d=vw.kpiresultgroup[i];
            var id=d.id;
            mapToData('kpiresult',id);
        }
        for(var i=0;i<vw.kpiprocessgroup.length;i++){
            var d=vw.kpiprocessgroup[i];
            var id=d.id;
            mapToData('kpiprocess',id);
        }

        console.log(vw.contentMapping);
    }

    var initData=function(){
        const FUNCTION_NAME='add-content';
        initKPIResultHeading();
        initKPIProcessHeading();
        initFinalHeading();
        setContentMapping();
        setEmployeeData();
        setKPIA('kpiresult');
        setKPIA('kpiprocess');

        dataService.digest($scope);
        notifier.notifyGroup('add-content');
        console.log(vw.employees);
    }

    var onSuccess=function(result){
        alertModal.hide();
        var data=result.data;
        vw.kpiresultgroup=data.groupkpiresult;
        vw.kpiprocessgroup=data.groupkpiprocess;
        vw.employees=data.employees;
        initData();


    }
    var loadData=function(){
        alertModal.upstream('loading');
        loader.getByGroupTag(tagID).then(onSuccess,function(){
            alertModal.display('Peringatan','Terjadi Kesalahan');
        });
    }

    vw.addContent=kpiService.addContent;

    loadData();
}]);
