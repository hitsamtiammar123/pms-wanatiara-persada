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
            keySanitize:'pt_sanitize',
            keyContentEditable:'ptContentEditable'
        },
        {
            key:'real_t',
            keyfilter:'real_filter',
            keyP:'pivot.real',
            keySanitize:'real_sanitize',
            keyContentEditable:'realContentEditable'
        },
        {
            key:'kpia',
            keyP:'kpia',
            keyfilter:'kpia_filter',
            keySanitize:'kpia_sanitize',
            keyContentEditable:'kpiaContentEditable'
        }
    ];

    vw.kpiresultgroup=[];
    vw.kpiprocessgroup=[];
    vw.employees=[];
    vw.contentMapping=[];
    vw.datalist=[
        {
            data:'Sangat Buruk',
            index:0
        },
        {
            data:'Buruk',
            index:1
        },
        {
            data:'Sedang',
            index:2
        },
        {
            data:'Baik',
            index:3
        },
        {
            data:'Sangat Baik',
            index:4
        },
    ];
    vw.headingmap2=[];
    vw.headingmap3=[];
    vw.rPerTString='R/T (%)';

    var initHeading2ByData=function(data,type){
        for(var i=0;i<data.length;i++){
            var d=data[i];
            var headmapping={};
            var rPerT={};
            var classname;
            var belongTo;

            if(type===KPI_RESULT){
                classname='heading-color-green';
                belongTo=`rg.kpiresultgroup[${i}].name`;
                contenteditable=true;
            }
            else if(type===KPI_PROCESS){
                classname='heading-color-yellow';
                belongTo=`rg.kpiprocessgroup[${i}].name`;
                contenteditable=false;
            }
                headmapping.attr={
                    class:classname,
                    colspan:2,
                    notifyGLabel:'add-content',
                    belongTo:belongTo,
                    contenteditable:contenteditable

                }
                rPerT.attr={
                    class:classname+' kpi-content',
                    rowspan:2,
                    belongTo:'rg.rPerTString',
                    contenteditable:false
                };

            vw.headingmap2.push(headmapping);
            vw.headingmap2.push(rPerT);
        }
    }

    var initHeading2=function(){
        initHeading2ByData(vw.kpiresultgroup,KPI_RESULT);
        initHeading2ByData(vw.kpiprocessgroup,KPI_PROCESS);
        //console.log(vw.headingmap2);
    }

    var initHeading3=function(){
        initHeading3ByData(vw.kpiresultgroup,KPI_RESULT);
        initHeading3ByData(vw.kpiprocessgroup,KPI_PROCESS);
        //console.log(vw.headingmap3);
    }

    var initHeading3ByData=function(data,type){
        for(var i=0;i<data.length;i++){
            var headmapping={};
            var headmapping2={};

            if(type===KPI_RESULT){
                classname='heading-color-green';
            }
            else if(type===KPI_PROCESS){
                classname='heading-color-yellow';
            }


            headmapping.value='Target';
            headmapping2.value='Realisasi';

            // headmapping.attr=`class="${classname} kpi-content"`;
            // headmapping2.attr=`class="${classname} kpi-content"`;
            headmapping.attr={
                class:classname+' kpi-content'
            };
            headmapping2.attr={
                class:classname+' kpi-content'
            };


            vw.headingmap3.push(headmapping);
            vw.headingmap3.push(headmapping2);
        }
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
    }

    var setContentEditable=function(data,flag){
            for(var i in data){
                var d=data[i];
                d.kpiaContentEditable=false;
                d.ptContentEditable=true;
                d.realContentEditable=true;
            }

    }

    var setEmployeeData=function(){
        for(var i in vw.employees){
            var employee=vw.employees[i];
            setFilter(employee.kpiresult,KPI_RESULT);
            setFilter(employee.kpiprocess,KPI_PROCESS);
            setContentEditable(employee.kpiresult,KPI_RESULT);
            setContentEditable(employee.kpiprocess,KPI_PROCESS);
            setTotalAchievement(employee);
        }
        //console.log(vw.employees);
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
        employee.ta=(taR+taP).toFixed(2);
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
                    sanitize:k.keySanitize,
                    contenteditable:k.keyContentEditable
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

        //console.log(vw.contentMapping);
    }

    var setUserHeading=function(){
        kpiService.setHeaderPeriod(vw,vw.kpitag);
    }

    var initData=function(){
        const FUNCTION_NAME='add-content';

        initHeading2();
        initHeading3()
        setUserHeading();
        setContentMapping();
        setKPIA(KPI_RESULT);
        setKPIA(KPI_PROCESS);
        setEmployeeData();
        setWeighting();

        notifier.notifyGroup('rg.add-content');
        //console.log(vw.employees);
    }

    var fillAll=function(attrs,value){
        if(attrs.type===KPI_RESULT){
            var id=attrs.dId;
            var key=attrs.key;
            for(var i in vw.employees){
                var employee=vw.employees[i];
                employee.kpiresult[id][key]=value;
            }
        }
    }

    var onAfterEdit=function(attrs,value){
        fillAll(attrs,value);
        setDataDetail();
        dataService.digest($scope);
        notifier.notifyGroup('rg.add-content');
    }

    var setDataDetail=function(){
        setKPIA(KPI_RESULT);
        setKPIA(KPI_PROCESS);
        setEmployeeData();
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

    vw.isListed=function(mapping,curr_data){
        if((mapping.type==='kpiprocess' && mapping.key!=='kpia')||
            (mapping.type==='kpiresult' && curr_data.kpiresult.unit==='规模 Scale' && mapping.key!=='kpia'))
            return true;
        return false;
    }

    vw.onAfterEdit=function(elem,value,scope,attrs){
        onAfterEdit(attrs,value);
    }

    vw.onListSelected=function(data,context,setter){
        var value=data.data.selected.index;
        setter.assign(context.scope,value);
        onAfterEdit(context.attrs,value);
    }

    loadData();
}]);
