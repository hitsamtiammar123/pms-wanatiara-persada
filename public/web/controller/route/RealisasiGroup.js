app.controller('RealisasiGroup',['$scope','loader','$routeParams','kpiService','notifier','dataService','alertModal','$parse','KPI_RESULT','KPI_PROCESS',
function($scope,loader,$routeParams,kpiService,notifier,dataService,alertModal,$parse,KPI_RESULT,KPI_PROCESS){

    var tagID=$routeParams.tagID;
    var heading_table_2=E('#heading-table-2');
    var heading_table_3=E('#heading-table-3');
    var template_kpiresult=`<th colspan="2" class="heading-color-green" notify-g-label="add-content" belong-to="{belongTo}">{kpiresult}</th>
    <th rowspan="2" class="heading-color-green kpi-content">R/T (%)</th>`;
    var template_kpiprocess=`<th colspan="2" class="heading-color-yellow" notify-g-label="add-content"  belong-to="{belongTo}">{kpiprocess}</th>
    <th rowspan="2" class="heading-color-yellow kpi-content">R/T (%)</th>`;
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
    // vw.weighting={};

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

    var getKPIAIndex=function(i,unit){
        var rt=0;
        var i=parseFloat(i);
        switch(unit){
            case '规模 Skala':
            case '规模 Scale':
                if(i<=0)
                    rt=70;
                else if(i==1)
                    rt=80;
                else if(i==2)
                    rt=90;
                else if(i==3)
                    rt=100;
                else if(i>=4)
                    rt=120;
            break;
            case 'MT':
                if(i<=0.8)
                    rt=80;
                else if(i>0.8 && i<=0.9)
                    rt=90;
                else if(i>0.9 && i<1)
                    rt=95;
                else if(i>=1 && i<=1.025)
                    rt=102;
                else if(i>1.025)
                    rt=110;

        }
        return rt;
    }

    var getBColor=function(i){
        i/=100;
        if(i<=0.8)
            return 'black-column';
        else if(i>0.8 && i<=0.9)
            return 'red-column';
        else if(i>0.9 && i<1)
            return 'green-column';
        else if(i>=1 && i<=1.025)
            return 'blue-column';
        else if(i>1.025)
            return 'gold-column';
    }

    var getKPIAResult=function(kpiresult,e_result){
        var rC;
        var tC;
        var rt;

        rC=e_result['real_t'];
        tC=e_result['pt_t'];



        switch(kpiresult.unit){
            case 'MT':
                rt=(parseFloat(rC)/parseFloat(tC));
            break;
            case '规模 Skala':
            case '规模 Scale':
                rt=rC;
            break;
        }

        return getKPIAIndex(rt,kpiresult.unit);
    }

    var getKPIAProcess=function(kpiprocess,e_process){
        var i;
        var rt;

        i=e_process.pivot.real;

        return getKPIAIndex(i,kpiprocess.unit);
    }

    var setUnitFilter=function(d,type){
        var unit='';
        if(type===KPI_RESULT)
            unit= d.kpiresult.unit;
        else if(type===KPI_PROCESS)
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

    var setWeighting=function(){
        vw.weighting={
            weight_result:vw.kpitag.weight_result*100,
            weight_process:vw.kpitag.weight_process*100
        };

        //dataService.digest($scope);
    }

    var setEmployeeData=function(){
        for(var i in vw.employees){
            var employee=vw.employees[i];
            setFilter(employee.kpiresult,KPI_RESULT);
            setFilter(employee.kpiprocess,KPI_PROCESS);
            setTotalAchievement(employee)
        }
    }

    var getTotalAchievement=function(data){
        var sum=0;
        var count=0;
        for(var i in data){
            var d=data[i];
            sum+=d.kpia;
            count++;
        }
        return sum/count;
    }

    var setTotalAchievement=function(employee){
        var taR=getTotalAchievement(employee.kpiresult) * vw.kpitag.weight_result;
        var taP=getTotalAchievement(employee.kpiprocess) * vw.kpitag.weight_process;
        employee.ta=taR+taP;
        employee.ia=kpiService.getAchievementIndex(employee.ta);
    }

    var setKPIA=function(type){
        var data=(type===KPI_RESULT)?vw.kpiresultgroup:vw.kpiprocessgroup;
        for(var i=0;i<data.length;i++){
            var d=data[i];
            for(var j=0;j<vw.employees.length;j++){
                var employee=vw.employees[j];
                var e_data;
                if(type===KPI_RESULT){
                    e_data=employee.kpiresult[d.id];
                    e_data.kpia=getKPIAResult(d,e_data);
                }
                else if(type===KPI_PROCESS){
                    e_data=employee.kpiprocess[d.id];
                    e_data.kpia=getKPIAProcess(d,e_data);
                }
                e_data.kpiaBColor=getBColor(e_data.kpia);
            }
        }
    }

    var setContentMapping=function(){

        var mapToData=function(type,id){
            for(var j in keymap){
                var k=keymap[j];
                var key=(type===KPI_RESULT)?k.key:k.keyP;
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
            mapToData(KPI_RESULT,id);
        }
        for(var i=0;i<vw.kpiprocessgroup.length;i++){
            var d=vw.kpiprocessgroup[i];
            var id=d.id;
            mapToData(KPI_PROCESS,id);
        }

        console.log(vw.contentMapping);
    }

    var initData=function(){
        const FUNCTION_NAME='add-content';

        initKPIResultHeading();
        initKPIProcessHeading();
        initFinalHeading();
        setContentMapping();
        setKPIA(KPI_RESULT);
        setKPIA(KPI_PROCESS);
        setEmployeeData();
        setWeighting();

        notifier.notifyGroup('rg.add-content');
        console.log(vw.employees);
    }

    var onSuccess=function(result){
        alertModal.hide();
        var data=result.data;
        vw.kpiresultgroup=data.groupkpiresult;
        vw.kpiprocessgroup=data.groupkpiprocess;
        vw.employees=data.employees;
        vw.kpitag=data;
        initData();


    }
    var loadData=function(){
        alertModal.upstream('loading');
        loader.getByGroupTag(tagID).then(onSuccess,function(){
            alertModal.display('Peringatan','Terjadi Kesalahan');
        });
    }

    vw.addContent=kpiService.addContent;
    vw.setWeight=function(elem,value,scope,attrs){
        var val_int=value?parseInt(value):0;
        var setter=$parse(attrs.belongTo);
        var flag=attrs.flag;
        if(val_int<0 || val_int>100){
            var default_val=kpiService.getDefaultWeighting(flag,vw.kpitag);
            setter.assign(scope,default_val);
        }
        else{
            kpiService.calculateWeight(flag,vw.weighting,vw.kpitag);
            setEmployeeData();
        }
        notifier.notifyGroup('rg.add-content');
    }

    loadData();
}]);
