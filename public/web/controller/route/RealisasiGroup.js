app.controller('RealisasiGroup',['$scope','loader','$routeParams','kpiService','notifier','dataService','alertModal','$parse','KPI_RESULT','KPI_PROCESS','$route',
function($scope,loader,$routeParams,kpiService,notifier,dataService,alertModal,$parse,KPI_RESULT,KPI_PROCESS,$route){

    var tagID=$routeParams.tagID;
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
    var dataChanged={};
    var headerChanged={};

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
    vw.kpiendorsements={};

    $scope.isSaving=false;

    var initHeading2ByData=function(data,type){
        for(var i=0;i<data.length;i++){
            var d=data[i];
            var headmapping={};
            var rPerT={};
            var classname;
            var belongTo;
            var afterEdit='';
            var dataID=d.id;

            if(type===KPI_RESULT){
                classname='heading-color-green';
                belongTo=`rg.kpiresultgroup[${i}].name`;
                contenteditable=true;
                afterEdit='rg.onHeaderChange';
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
                    contenteditable:contenteditable,
                    afterEdit:afterEdit,
                    dataID:dataID

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
    }

    var initHeading3=function(){
        initHeading3ByData(vw.kpiresultgroup,KPI_RESULT);
        initHeading3ByData(vw.kpiprocessgroup,KPI_PROCESS);
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

    var setAggrements=function(){
        kpiService.setAggrements(vw,vw.kpitag.representative,vw.kpitag.representative.atasan);
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
        setAggrements();

        notifier.notifyGroup('rg.add-content');
        //console.log(vw.employees);
    }

    var getEmployeeMapChange=function(employeeID,type){
        if(!dataChanged.hasOwnProperty(employeeID))
            dataChanged[employeeID]={};
        var e_change=dataChanged[employeeID];
        if(!e_change.hasOwnProperty(type))
            e_change[type]={};
        var r_change=e_change[type];
        return r_change;
    }

    var mapChange=function(attrs,value){
        var employeeID=attrs.employeeId;
        var type=attrs.type;
        var r_change=getEmployeeMapChange(employeeID,type);
        var dId=attrs.dId;
        var key=attrs.key;
        var pIndex=parseInt(attrs.pIndex);
        kpiService.mapChange(dId,key,value,vw.employees[pIndex][type],r_change);
    }

    var fillAll=function(attrs,value){
        if(attrs.type===KPI_RESULT){
            var id=attrs.dId;
            var key=attrs.key;
            for(var i in vw.employees){
                var employee=vw.employees[i];
                employee.kpiresult[id][key]=value;

                var r_change=getEmployeeMapChange(employee.id,attrs.type);
                kpiService.mapChange(id,key,value,employee.kpiresult,r_change);
            }
        }
        else if(attrs.type===KPI_PROCESS)
            mapChange(attrs,value);
    }

    var onAfterEdit=function(attrs,value){
        fillAll(attrs,value);
        setDataDetail();
        dataService.digest($scope);
        notifier.notifyGroup('rg.add-content');
        console.log({dataChanged});
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
        vw.kpiendorsements=data.endorsements;
        initData();


    }
    var loadData=function(){
        alertModal.upstream('loading');
        loader.getByGroupTag(tagID).then(onSuccess,function(){
            alertModal.display('Peringatan','Terjadi Kesalahan');
        });
    }

    var saveSucess=function(){

        setTimeout(function(){
            $route.reload();
        },1000)
    }

    var setWeightingChange=function(weighting){
        headerChanged.weighting=weighting;
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
            setWeightingChange(vw.weighting);
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

    vw.onHeaderChange=function(elem,value,scope,attrs){

        var dataID=attrs.id;
        if(!headerChanged.hasOwnProperty('kpiresultgoup'))
            headerChanged.kpiresultgoup={};
        headerChanged.kpiresultgoup[dataID]=value;
        console.log({attrs,headerChanged});
    }

    vw.mapChange=function(elem,value,scope,attrs){
        mapChange(attrs,value);
        //console.log({dataChanged});
    }

    vw.onListSelected=function(data,context,setter){
        var value=data.data.selected.index;
        setter.assign(context.scope,value);
        onAfterEdit(context.attrs,value);
        //vw.mapChange(context.elem,value,context.scope,context.attrs);
    }

    vw.saveChanged=function(){
        loader.savePMSGroup(tagID,dataChanged,headerChanged).then(saveSucess,function(){
            console.log('Ada eror')
        }).finally(function(){
            alertModal.hide();
        });
        alertModal.display('Peringatan','Menyimpan data, mohon tunggu',false,true);
        $scope.isSaving=true;
    }

    vw.isEndorseDisable=function(endorse){
        return kpiService.isEndorseDisable(endorse,vw.kpiendorsements);
    }

    loadData();
}]);
