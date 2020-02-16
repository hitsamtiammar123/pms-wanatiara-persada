app.controller('RealisasiController',function($scope,$rootScope,validator,loader,$route,
    $filter,notifier,copier,alertModal,dataService,user,$routeParams,formModal,confirmModal
    ,$sce,pusher,months,$location,$parse,kpiKeys,kpiService,KPI_PROCESS,KPI_RESULT,errorResponse,years){


    $scope.totalAchieveMent={};
    $scope.IndexAchieveMent={};
    $scope.IndexAchieveMentP={};
    $scope.totalAchieveMentP={};
    $scope.finalAchievement={};
    $scope.hasChanged=false;
    $scope.headerLabel=$scope.headerLabel?$scope.headerLabel:[];
    $scope.currentData=[];
    $scope.atasan={};
    $scope.dlt_result={dspl_dlt:false,toogle_delete:false,dlt_message:'Hapus Baris'};
    $scope.dlt_process={dspl_dlt:false,toogle_delete:false,dlt_message:'Hapus Baris'}
    $scope.fp=[];
    $scope.totalW=[];
    $scope.totalW_P=[];
    $scope.rContent=[];
    $scope.displayData=[];
    $scope.pb={};
    $scope.f=months.map(function(d){
        return d.value;
    });
    $scope.kpiprocesses=[];
    $scope.headerLabelProcess=[];
    $scope.pw_indices=['pw_1','pw_2'];
    $scope.pt_indices=['pt_t1','pt_k1','pt_t2','pt_k2'];
    $scope.real_indices=['real_t1','real_k1','real_t2','real_k2'];
    $scope.kpia_indices=['kpia_1','kpia_2'];
    $scope.aw_indices=['aw_1','aw_2'];
    $scope.header={};
    $scope.user=user;
    $scope.hasEndorse=true;
    $scope.months=months;
    $scope.years=years;
    $scope.display_weights={};


    var currMonth=$routeParams.month?parseInt($routeParams.month):$rootScope.month;
    var dataSelected={};
    var dataSelectedP={};
    const indexData=['name','unit'].concat($scope.pw_indices,$scope.pt_indices,$scope.real_indices);
    const indexDataP=['unit','pw_1','pw_2','pt_1','pt_2','real_1','real_2'];
    var employeeIndex=$routeParams.index;
    var kpiheaders;
    var kpiprocesses=$rootScope.kpiprocesses?$rootScope.kpiprocesses:null;
    var kpiresultstream=copier.stream(employeeIndex+'_kpiresult');
    var kpiprocessstream=copier.stream(employeeIndex+'_kpiprocess');
    var kpiresultdeletestream=copier.stream(employeeIndex+'_kpiresult_delete_list');
    var kpiprocessdeletestream=copier.stream(employeeIndex+'_kpiprocess_delete_list');
    var kpiresult_elem=E('.realisasi-table tbody').eq(0);
    var kpiprocess_elem=E('.realisasi-table tbody').eq(1);
    var currentEmployee=user.employee;
    var deleteListResult=[];
    var deleteListProcess=[];
    var updateMap={};
    var updateMapP={};
    var isDownloading=false;
    var curr_employee={};

    $scope.currentMonth=$scope.months[currMonth];
    $scope.currentYear=$routeParams.year?parseInt($routeParams.year):$rootScope.year;
    $scope.currendDate=new Date();


    var onFail=function(a,b,c){
        console.log(a,b,c)
    }

    /**
     *
     * @description
     * Fungsi ini dipanggil sebagai callback jika atasan atau bawahan yang dinilai melakukan
     * perubahan data
     * @returns void
     */
    var PMSHasChanged=function(){
        var curr=$route.current.$$route.controller;
        flushStreamAndHeader();
        if(curr==='RealisasiController' && !$rootScope.loading ){
            //alertModal.display('Peringatan','Terjadi Perubahan Data',false,true);
            setTimeout($route.reload,1500);
        }
    }

    var setUsers=function(){
        $scope.employee=curr_employee;
        $scope.atasan=curr_employee.atasan;
    }

    var vanishDisturbingColumn=function(){
        setTimeout(function(){
            E('#disturbing').remove();
        },10)
        E('#unit-bug').next().attr('id','disturbing');
    }

    var setHeaderResultLabel=function(){
        $scope.headerLabel=[];
        $scope.fp=[];
        for(var j=0,k=true,p=0,t=currMonth-1;j<14;j++){
            var input='';
            if(t<0)
                t=11;
            else if(t>11)
                t=0;
            switch(j){
                case 0:
                case 1:
                case 10:
                case 11:
                case 12:
                case 13:

                    input=$scope.f[t];
                    if(k){
                        t++;
                        k=false;
                    }
                    else{
                        t--;
                        k=true;
                    }
                break;
                case 2:
                case 3:
                case 4:
                case 5:
                    if(j%2===0){
                        input+='Target ';
                    }
                    else
                        input+='Kumulatif ';
                    input+=$scope.f[t];
                    if(j%2===0){
                        input+='份目标';
                    }
                    else
                        input+='份累计';

                    p++;
                    if(p===2)
                        t++;
                    else if(p===4){
                        t--;
                        p=0;
                    }
                break;
                case 6:
                case 7:
                case 8:
                case 9:
                    if(j%2===0){
                        input+='Realisasi ';
                        }
                    else
                        input+='Kumulatif ';
                    input+=$scope.f[t];
                    if(j%2===0){
                        input+='份实行';
                    }
                    else
                        input+='份累计';
                    p++;
                    if(p===2)
                        t++;
                    else if(p===4){
                        t--;
                        p=0;
                    }
                break;
            }
            $scope.headerLabel.push(input);

        }
    }

    var setHeaderProcessLabel=function(){
        var t;
        for(var i=0;i<10;i++){
            var input=''
            t=(i%2===0)?currMonth!==0?currMonth-1:11:currMonth;

            switch(i){
                case 2:
                case 3:
                    input+='Target ';
                break;
                case 4:
                case 5:
                    input+='Realisasi ';
                break;

            }
            input+=$scope.f[t];
            switch(i){
                case 2:
                case 3:
                    input+='份目标';
                break;
                case 4:
                case 5:
                    input+='份实行';
                break;

            }
            $scope.headerLabelProcess.push(input);
        }
    }

    var setUserHeading=function(){
        kpiService.setHeaderPeriod($scope,kpiheaders);
    }

    var setHeader=function(){
        setHeaderResultLabel();
        setHeaderProcessLabel();
        setUserHeading();
    }

    var setTotalAchievement=function(data,totalAchieveMent,IndexAchieveMent){
        const FUNCTION_NAME='add-content';
      	if(!data)
          	return;

        //debugger;
        for(var i=0;i<2;i++){
            var q='t'+(i+1);
            var aw_index='aw_'+(i+1);
            kpiService.setTotalAchievement(data,totalAchieveMent,IndexAchieveMent,q,aw_index);
        }

        notifier.notifyGroup('add-content');

    }

    var setFinalAchivement=function(){
        const FUNCTION_NAME='add-content';
        for(var i=1;i<=2;i++){
            var keys={};
            keys.tr='t'+i;
            keys.tp='t'+i;
            keys.t_n='t'+i+'_n';
            keys.t_i='t'+i+'_i';
            keys.t_f='t'+i+'_f';
            var w=(i===2)?{weight_result:$scope.header.weight_result,weight_process:$scope.header.weight_process}:$scope.header.weighing_prev
            kpiService.setFinalAchievement($scope.totalAchieveMent,$scope.totalAchieveMentP,w,$scope.finalAchievement,keys);
        }
        notifier.notifyGroup('add-content');
    }

    var setCurrentMonth=function(month){
        var index=month.index;
        currMonth=$rootScope.month=index;
        setHeader();
    }

    var setTotalW=function(data,totalW){

        if(data){
            for(var i=0;i<2;i++){
                totalW[i]=0;
                for(var j=0;j<data.length;j++){
                    var d=data[j];
                    var pw_index='pw_'+(i+1);
                    var w=d[pw_index];
                    var w_i=parseFloat(w);
                    w_i=isNaN(w_i)?0:w_i;
                    totalW[i]+=w_i;
                }
            }
        }
    }

    /**
     *
     * @author Hitsam Tiammar <hitsamtiammar@gmail.com>
     * @module RealisasiContent
     *
     *
     * @description
     * berfungsi untuk men-set ContentEditable pada data KPIResult untuk attribute performance weighting
     * @param {Object} curr merupakan data KPIResult yang bersangkutan. Harus berupa objek
     * @param {number} j index ke berapa data tersebut ada pada array KPIResult
     * @param {boolean} hasEndorse Menentukan apakah PMS sudah di-endorse
     *
     * @returns void
     */
    var setPWContentEditable=function(curr,j,hasEndorse){
        if(hasEndorse && j<2){
            curr.pw_contentEditable[j]=false;
        }
        else{
            if(j===0){
                curr.pw_contentEditable[j]=curr.hasNew?true:false;
            }
            else if(j===1)
                curr.pw_contentEditable[j]=true;
        }
    }

    var setPTAndRealContentEditable=function(curr,j,hasEndorse){
        if(hasEndorse){
            curr.pt_contentEditable[j]=false;
            curr.real_contentEditable[j]=false;
        }
        else{
            if(j<2){
                if(!curr.hasNew){
                    curr.pt_contentEditable[j]=false;
                    curr.real_contentEditable[j]=false;
                }
                else{
                    curr.pt_contentEditable[j]=true;
                    curr.real_contentEditable[j]=true;
                }
            }
            else if(j>=2){
                if(j===3){
                    switch(curr.unit){
                        case '$':
                        case 'WMT':
                        case 'MT':
                        case 'kwh':
                            curr.pt_contentEditable[j]=false;
                            curr.real_contentEditable[j]=false;
                        break;
                        default:
                                curr.pt_contentEditable[j]=true;
                                curr.real_contentEditable[j]=true;
                        break;
                    }
                }
                else{
                    if(curr.unit ==='kwh')
                        curr.pt_contentEditable[j]=false;
                    else
                        curr.pt_contentEditable[j]=true;
                    curr.real_contentEditable[j]=true;
                }

            }
        }
    }

    var setKPIAContentEditable=function(curr,hasEndorse){
        for(var i=0;i<2;i++){
            if(hasEndorse){
                curr.kpia_contentEditable[i]=false;
            }
            else{
                if(kpiService.isPriviledgesKPIResult(curr,'pt_t'+(i+1),kpiheaders )){
                    curr.kpia_contentEditable[i]=true;
                }
                else
                    curr.kpia_contentEditable[i]=false;
            }
        }
    }

    var setContentEditable=function(data,type){
        if(type===KPI_RESULT){

            for(var i=0;i<data.length;i++){
                var curr=data[i];
                curr.aw_contentEditable=false;
                curr.pw_contentEditable=[];
                curr.pt_contentEditable=[];
                curr.real_contentEditable=[];
                curr.kpia_contentEditable=[];
                if($scope.hasEndorse){
                    curr.name_contentEditable=false;
                    curr.unit_contentEditable=false;
                }
                else{
                    curr.name_contentEditable=true;
                    curr.unit_contentEditable=true;
                }

                for(var j=0;j<4;j++){
                    setPWContentEditable(curr,j,$scope.hasEndorse);
                    setPTAndRealContentEditable(curr,j,$scope.hasEndorse);
                }
                setKPIAContentEditable(curr,$scope.hasEndorse);
            }

        }
        else if(type===KPI_PROCESS){
            for(var i=0;i<data.length;i++){
                var curr=data[i];
                curr.contentEditable={};
                curr.contentEditable.kpia_1=false;
                curr.contentEditable.kpia_2=false;
                curr.contentEditable.aw_1=false;
                curr.contentEditable.aw_2=false;

                if($scope.hasEndorse){
                    curr.contentEditable.unit=false;
                    curr.contentEditable.pw_1=false;
                    curr.contentEditable.pw_2=false;
                    curr.contentEditable.pt_1=false;
                    curr.contentEditable.pt_2=false;
                    curr.contentEditable.real_1=false;
                    curr.contentEditable.real_2=false;
                }
                else{
                    if(curr.hasNew===true){
                        curr.contentEditable.pw_1=true;
                        curr.contentEditable.pt_1=true;
                        curr.contentEditable.real_1=true;
                    }
                    else{
                        curr.contentEditable.pw_1=false;
                        curr.contentEditable.pt_1=false;
                        curr.contentEditable.real_1=false;
                    }
                    curr.contentEditable.unit=true;
                    curr.contentEditable.pw_2=true;
                    curr.contentEditable.pt_2=true;
                    curr.contentEditable.real_2=true;
                }

            }
        }
    }


    /**
     * Berfungsi untuk menentukan apakah KPIResult adalah KPIResult dengan priviledge tertentu
     *
     * @param {JSON} curr Data KPIResult yang bersangkutan
     * @param {Object} obj Object berupa parameter keluaran yang akan menerima flag dari suatu priviledge
     * @return bool
     */
    var isPriviledgesKPIResult=function(curr,i){
        if(kpiheaders && (
            curr.unit === '#' ||
            curr.unit ==='kwh'
            )
        ){
            var t_key ='pt_t'+(i+1);
            var t=parseInt(curr[t_key]);
            if(t===0)
                return true;
            else
                return false;
        }
        return false;
    }

    var setBColorKPIAandPW=function(curr){
        for(var j=0;j<2;j++){
            var kpia_key='kpia_'+(j+1);
            var aw_key='aw_'+(j+1);
            var pw_key=$scope.pw_indices[j];
            var rt;

            var keys={};
            keys.pt_t='pt_t'+(j+1);
            keys.real_t='real_t'+(j+1);
            keys.real_k='real_k'+(j+1);
            keys.pt_k='pt_k'+(j+1);


            if(!kpiService.isPriviledgesKPIResult(curr,'pt_t'+(j+1),kpiheaders))
                rt=kpiService.getKPIAKPIResult(curr,keys);

            else
                rt=kpiService.getKPIAKPIResultByPriviledge(curr,kpia_key);


            curr[kpia_key]=rt+'%';
            var bColor='bColor_kpia_'+(j+1);

            if(rt>=120){
                curr[bColor]='gold-column'
            }
            else if(rt>=105 && rt<120){
                curr[bColor]='blue-column'
            }
            else if(rt>=95 && rt<105){
                curr[bColor]='green-column'
            }
            else if(rt<95){
                curr[bColor]='red-column'
            }
            var pwqIndex='pw_'+(j+1);
            // var pwq=curr[pwqIndex];
            // var calculate=rt*parseFloat(pwq)/100;
            // curr[aw_key]=(calculate.toFixed(1));
            kpiService.setAW(curr,pwqIndex,aw_key,rt);

            if(j===1)
                curr['bColor_'+pw_key]='can-edit-content';
        }
    }

    var setBColorPTandReal=function(curr){
        for(var i=0;i<4;i++){
            var real_key=$scope.real_indices[i];
            var pt_key=$scope.pt_indices[i];

            if(i>=2){
                curr['bColor_'+real_key]='can-edit-content';
                curr['bColor_'+pt_key]='can-edit-content';
            }
        }
    }

    var setBColor=function(data){
        //debugger;
        for(var i=0;i<data.length;i++){
            var curr=data[i];
            curr.kpiColor='';
            curr.unitColor='';
            setBColorKPIAandPW(curr);
            setBColorPTandReal(curr);

        }

    }

    var setBColorP=function(data){
        var getKPIAColor=function(r){
            if(r<=80)
                return 'black-column';
            else if(r===100)
                return 'green-column';
            else if(r===110)
                return 'blue-column';
            else if(r>=120)
                return 'gold-column';
            else
                return '';
        }

        var getEditableColor=function(hasEndorse){
           // if(hasEndorse)
                return 'can-edit-content';
            // else
            //     return '';
        }

        for(var i=0;i<data.length;i++){
            var curr=data[i];
            curr.contentEditable={};
            curr.kpia_filter='addPercent';
            curr.aw_filter='addPercent';
            curr.pw_filter='addPercent';

            curr.kpia_1=kpiService.getKPIAKPIProcess(curr,{real:'real_1',pt:'pt_1'});
            curr.kpia_2=kpiService.getKPIAKPIProcess(curr,{real:'real_2',pt:'pt_2'});
            curr.bColor_kpia_1=getKPIAColor(curr.kpia_1);
            curr.bColor_kpia_2=getKPIAColor(curr.kpia_2);
            curr.bColor_real=getEditableColor($scope.hasEndorse);
            curr.bColor_pt=getEditableColor($scope.hasEndorse);
            curr.bColor_pw=getEditableColor($scope.hasEndorse);

            kpiService.setAW(curr,'pw_1','aw_1',curr.kpia_1);
            kpiService.setAW(curr,'pw_2','aw_2',curr.kpia_2);
        }
    }

    var loadData=function(){
        if(!$scope.data||$scope.data.length===0){
            //loader.loadDData(null,rI);
            loader.loadVData();
            rI();
        }
        else
            rI();
    }

    var applyUnitFilter=function(d,isApply){
        if(!isApply)
            return;
        switch(d.unit){
            case '$':
            case 'WMT':
            case 'MT':
            case 'kwh':
                d.pt_k2=(parseInt(d.pt_k1)+parseInt(d.pt_t2))+'';
                d.real_k2=(parseInt(d.real_k1)+parseInt(d.real_t2))+'';
            if(d.unit!=='kwh')
                break;
            case 'kwh':
                d.pt_t1=d.pt_k1=d.pt_t2=d.pt_k2=0;
            break;
        }
    }

    var setDataFilter=function(d,isApply){
        var unit=d.unit.trim();
        d.pw_filter='integer|addPercent';
        d.pt_filter='';
        d.real_filter='';
        d.kpia_filter='addPercent';
        d.aw_filter='addPercent';
        d.kpia_sanitize=d.aw_sanitize='sNumber';
        d.pw_sanitize='sNumber'
        switch(unit){
            case '%':
            case 'MV':
                d.pt_filter='addPercent';
                d.real_filter='addPercent';
            break;
            case '$':
            case 'WMT':
            case 'MT':
                d.pt_filter='number';
                d.real_filter='number';

            break;
            case 'kwh':
                d.pt_filter='kwh';
                d.real_filter='number';

            break;
        }
        d.pt_sanitize=d.real_sanitize='sNumber';

        applyUnitFilter(d,isApply);
    }

    var setFilter=function(data,isApply){
        isApply=!isUndf(isApply)?isApply:true;
        for(var i=0;data&&i<data.length;i++){
            var d=data[i];
            setDataFilter(d,isApply);
        }
        //console.log(data);
    }

    var applyCopiedTable=function(copyData,flag){
        if(copyData){
            if(flag===KPI_RESULT){
                $scope.data=[];
                dataSelected={};
            }
            else if(flag===KPI_PROCESS){
                $scope.kpiprocesses=[];
                dataSelectedP={};
            }
            var copy_data=angular.copy(copyData);
            setTimeout(function(){

                if(flag===KPI_RESULT){
                    $scope.data=copy_data;
                    kpiheaders.kpiresults=$scope.data;
                    setTotalAchievement($scope.data,$scope.totalAchieveMent,$scope.IndexAchieveMent);
                    setTotalW($scope.data,$scope.totalW);
                    console.log($scope.data);
                }
                else if(flag===KPI_PROCESS){
                    $scope.kpiprocesses=copy_data;
                    kpiheaders.kpiprocesses=$scope.kpiprocesses;
                    setTotalAchievement($scope.kpiprocesses,$scope.totalAchieveMentP,$scope.IndexAchieveMentP);
                    setTotalW($scope.kpiprocesses,$scope.totalW_P);
                    console.log($scope.kpiprocesses);
                }
                setFinalAchivement();
                $scope.$digest();
            },50)
        }
    }

    var pushDataIntoDeleteList=function(list,deleteList,stream){
        var copy_list=angular.copy(deleteList);
        for(var i=0;i<list.length;i++){
            var d=list[i];
            var id=d['id'];
            if(deleteList.indexOf(id)===-1)
                deleteList.push(id);
        }
        stream.pushData(deleteList,copy_list);
    }

    var copyData=function(i,data){
        copier.setCopyData(i,data);
    }

    var pasteData=function(i,flag){
        //debugger;
        var copyData=copier.getCopyData(i);
        var deleteList;
        var stream;
        var data;
        if(copyData){
            copyData=copyData.map(function(d){d.id=null;return d;});
            if(flag===KPI_RESULT){
                kpiresultstream.pushData(copyData,$scope.data);
                deleteList=deleteListResult;
                stream=kpiresultdeletestream;
                data=$scope.data;
            }
            else if(flag===KPI_PROCESS){
                kpiprocessstream.pushData(copyData,$scope.kpiprocesses);
                deleteList=deleteListProcess;
                stream=kpiprocessdeletestream;
                data=$scope.kpiprocesses;
            }

            pushDataIntoDeleteList(data,deleteList,stream);
            applyCopiedTable(copyData,flag);


        }
    }

    var undoData=function(flag){
        var _undoDt;
        if(flag===KPI_RESULT){
            _undoDt=kpiresultstream.undoData();
            deleteListResult=_undoDt?kpiresultdeletestream.undoData():deleteListResult;
            console.log(deleteListResult,'deleteListResult');
        }
        else if(flag===KPI_PROCESS){
            _undoDt=kpiprocessstream.undoData();
            deleteListProcess=_undoDt?kpiprocessdeletestream.undoData():deleteListProcess;
            console.log(deleteListProcess,'deleteListProcess');
        }
        applyCopiedTable(_undoDt,flag);

    }

    var redoData=function(flag){
        var _redoDt;
        if(flag===KPI_RESULT){
            _redoDt=kpiresultstream.redoData();
            deleteListResult=_redoDt?kpiresultdeletestream.redoData():deleteListResult;
            console.log(deleteListResult,'deleteListResult');
        }
        else if(flag===KPI_PROCESS){
            _redoDt=kpiprocessstream.redoData();
            deleteListProcess=_redoDt?kpiprocessdeletestream.redoData():deleteListProcess;
            console.log(deleteListProcess,'deleteListProcess');
        }
        applyCopiedTable(_redoDt,flag);
    }

    var applyPastedData=function(context,d){
        var local_scope=context.scope;
        var local_attrs=context.attrs;
        var c_index=local_scope.$index;
        var p_index=parseInt(local_attrs.dIndex);
        var d_flag=context.attrs.flag;
        var f_index=indexData.indexOf(d_flag);
        var first_flag=d_flag;
        var first_index=c_index;
        var i_index=local_attrs.iIndex;
        var table_indices=['name','unit'].concat($scope.pw_indices,$scope.pt_indices,$scope.real_indices);
        var j_index=table_indices.indexOf(i_index);
        var first_index=i_index;
        //console.log({context,d});
        //debugger;
        for(var i=0;i<d.length;i++){
            var cdata=d[i];
            if(isUndf($scope.data[p_index]))
                $scope.addNewData();

            for(var j=0;j<cdata.length;j++){
                var ccdata=cdata[j];
                if(
                    $scope.pw_indices.indexOf(d_flag)!==-1 ||
                    $scope.pt_indices.indexOf(d_flag)!==-1 ||
                    $scope.real_indices.indexOf(d_flag)!==-1
                ){
                    var sanitize='sNumber';
                    ccdata=$filter(sanitize)(ccdata+'');
                }
                if(ccdata!==''){
                    $scope.data[p_index][i_index]=ccdata;
                    if($scope.data[p_index]['id'])
                        kpiService.mapChange(p_index,i_index,ccdata,$scope.data,updateMap);

                }

                    c_index=0;
                    if(i_index==='real_k2'){
                        f_index=indexData.indexOf(first_flag);
                        j_index=table_indices.indexOf(first_index);
                    }
                    else{
                        j_index++;
                        f_index++;
                    }
                    i_index=table_indices[j_index];
                    d_flag=indexData[f_index];
                    changeFlag=false;


            }
            if(!$scope.data[p_index]['id'])
                kpiService.mapCreate($scope.data[p_index],updateMap);

            p_index++;
            d_flag=first_flag;

            f_index=indexData.indexOf(d_flag);
            i_index=first_index;
            j_index=table_indices.indexOf(i_index);

        }
    }

    var applyPastedDataP=function(context,d){
        var local_scope=context.scope;
        var local_attrs=context.attrs;
        var p_index=parseInt(local_attrs.dIndex);
        var fFlag=local_attrs.belongTo.replace('p.','');
        var f_index=indexDataP.indexOf(fFlag);
        var first_flag=fFlag;
        var first_index=f_index;

        for(var i=0;i<d.length;i++){
            var cdata=d[i];

            for(var j=0;j<cdata.length;j++){
                var ccdata=cdata[j];
                if(!$scope.kpiprocesses.hasOwnProperty(p_index))
                    break;

                if(fFlag!=='unit'){
                    var sanitize='sNumber';
                    ccdata=$filter(sanitize)(ccdata);
                }

                if(ccdata!=='')
                    $scope.kpiprocesses[p_index][fFlag]=ccdata;

                if(f_index==='real_2'){
                    fFlag=first_flag;
                    f_index=first_index;
                }
                else{
                    f_index++;
                    fFlag=indexDataP[f_index];
                }

            }

            p_index++;
            fFlag=first_flag;
            f_index=first_index;

        }
    }

    var defaultColor=function(l){
        var attrs=l.attrs;
        var s=l.scope;
        var flag=attrs.flag;
        switch(flag){
            case 'kpi':
            case 'unit':
                s.d[flag+'Color']='';
            break;
            default:
                if(!isUndf(s.i))
                    s.d[flag]['bColor'+(s.i+1)]='';
            break;
        }
    }

    var pasteCopiedData=function(context,e,flag){
        const FUNCTION_NAME='realisasi-content';
        e.preventDefault();
        var c=(e.originalEvent||e).clipboardData;
        var r=copier.readFromClipboard(c);
        //console.log('from realisasi',{r});
        //debugger;
        if(r===null){
            alertModal.display('Peringatan','Data yang dicopy tidak valid',true,false);
        }
        else{

            if(flag===KPI_RESULT){
                var copy_data=angular.copy($scope.data);
                applyPastedData(context,r);
                setBColor($scope.data);
                setTotalAchievement($scope.data,$scope.totalAchieveMent,$scope.IndexAchieveMent);
                setFilter($scope.data);
                setTotalW($scope.data,$scope.totalW);
                var copy_data_2=angular.copy($scope.data);
                kpiresultstream.pushData(copy_data_2,copy_data);
                kpiresultdeletestream.pushData(deleteListResult,deleteListResult);
                //pushDataIntoDeleteList($scope.data,deleteListResult,kpiresultdeletestream);
            }
            else if(flag===KPI_PROCESS){
                var copy_data=angular.copy($scope.kpiprocesses);
                applyPastedDataP(context,r);
                setBColorP($scope.kpiprocesses);
                setTotalAchievement($scope.kpiprocesses,$scope.totalAchieveMentP,$scope.IndexAchieveMentP);
                setTotalW($scope.kpiprocesses,$scope.totalW_P);
                var copy_data_2=angular.copy($scope.kpiprocesses);
                kpiprocessstream.pushData(copy_data_2,copy_data);
                kpiprocessdeletestream.pushData(deleteListProcess,deleteListProcess);
                //pushDataIntoDeleteList($scope.kpiprocesses,deleteListProcess,kpiprocessdeletestream);
            }
            setFinalAchivement();

            dataService.digest($scope);
            notifier.notifyGroup('realisasi-content');
        }
    }

    var clearSelectedData=function(){

    }

    var checkSelectedData=function(dataSelected){
        var col=0;
        var row=0;
        var maxCol=0;
        var maxRow=0;

        var keys=Object.keys(dataSelected);
        var firstKey=keys[0];
        var lengthOfFirstKey=dataSelected.hasOwnProperty(firstKey)?
        Object.values(dataSelected[firstKey]).length:-1;

        for(var i=1;lengthOfFirstKey!==-1&&i<keys.length;i++){
            var key=keys[i];
            var curr=dataSelected[key];
            var lengthOfData=Object.values(curr).length;
            if(lengthOfFirstKey!==lengthOfData)
                return false;
        }
        return true;
    }

    var initializeKPIProcessModal=function(){
        if(!formModal.hasInit('kpiprocess')){
            var data={
                kpiprocess:{
                    type:'select',
                    message:'Catatan: Masukan data sasaran proses yang belum anda masukan sebelumnya',
                    list:kpiprocesses,
                    label:'name'
                }
            }
            formModal.init('kpiprocess',data,'Silakan Masukan data Sasaran Proses');
        }
    }


    var setAggrements=function(){
        //debugger;
        $scope.aggrementStr='';
        kpiService.setAggrements($scope,$scope.employee,$scope.atasan);
        $scope.aggrementStr='HRD <span class="glyphicon glyphicon-ok"></span>';
        $scope.aggrementStr=$sce.trustAsHtml($scope.aggrementStr,$scope.employee);
    }

    var initData=function(){
        //debugger;
        const FUNCTION_NAME='realisasi-content';
        setHeader();
        setUsers();
        setFilter($scope.data,false);
        setBColor($scope.data);
        setTotalAchievement($scope.data,$scope.totalAchieveMent,$scope.IndexAchieveMent);
        setTotalW($scope.data,$scope.totalW);

        setBColorP($scope.kpiprocesses);
        setTotalAchievement($scope.kpiprocesses,$scope.totalAchieveMentP,$scope.IndexAchieveMentP);
        setTotalW($scope.kpiprocesses,$scope.totalW_P);
        setAggrements();
        setContentEditable($scope.data,KPI_RESULT);
        setContentEditable($scope.kpiprocesses,KPI_PROCESS);

        setFinalAchivement();
        notifier.notifyGroup('realisasi-content');
        //console.log($scope.data);
    }

    var loadKPIProcessDone=function(){
        initializeKPIProcessModal();
        initData();
        alertModal.hide();
    }

    var loadKPIProcessSucess=function(result){
        kpiprocesses=$rootScope.kpiprocesses=result.data;
        loadKPIProcessDone();
    }

    var loadDoneEmployee=function(employee){
        curr_employee=employee;
        loadKPIProcess();
    }

    var loadSuccessHeader=function(result){
        //debugger;
        var header=result.data;
        kpiheaders=header;
        $scope.header=kpiheaders;
        $rootScope.employees[employeeIndex].headers[$scope.currentYear][currMonth]=kpiheaders;
        //user=header.employee;
        $scope.data=header.kpiresults;
        $scope.kpiprocesses=header.kpiprocesses;
        $scope.kpiendorsements=header.kpiendorsements;
        $scope.display_weights={
            weight_result:header.weight_result*100,
            weight_process:header.weight_process*100
        };
        $scope.hasTags=header.hasTags;
        header.hasTags?$scope.tags=header.tags:null;
        // $scope.kpiendorsements=$scope.kpiendorsements.sort(function(d1,d2){
        //     return parseInt(d1.level)-parseInt(d2.level);
        // });
        loadEmployee();
    }

    var loadSuccessEmployee=function(result){
        //debugger;
        var employee=result.data;
        $rootScope.employees[employeeIndex].employee=employee;
        loadDoneEmployee(employee);
    }

    var loadFail=errorResponse;

    var checkEmployee=function(){
        if(!$rootScope.employees.hasOwnProperty(employeeIndex)){
            $rootScope.employees[employeeIndex]={};
            $rootScope.employees[employeeIndex].headers={};
        }

    }

    var flushHeaderPropertyOnRootScope=function(){
        for(var j=0;j<years.length;j++){
            var listOnYears=$rootScope.employees[employeeIndex].headers[years[j]];
            for(var i=0;i<12 &&listOnYears ;i++){
                if(listOnYears.hasOwnProperty(i)){
                    delete listOnYears[i];
                }
            }
        }

    }

    var flushStreamAndHeader=function(){

        flushHeaderPropertyOnRootScope();
        kpiresultdeletestream.flush();
        kpiprocessdeletestream.flush();
        kpiprocessstream.flush();
        kpiresultstream.flush();
    }


    var saveSuccess=function(result){
        alertModal.hide();
        setTimeout(function(){
            flushStreamAndHeader();
            $route.reload();
        },1000)
    }

    var saveDone=function(){
        $rootScope.loading=false;
        $scope.hasChanged=false;
    }

    var loadKPIProcess=function(){
        if(kpiprocesses){
            loadKPIProcessDone();
        }
        else{
            loader.getKPIProcess().then(loadKPIProcessSucess,loadFail);
        }
    }

    var loadEmployee=function(){
        var employee_data=$rootScope.employees[employeeIndex];
        if(!employee_data.hasOwnProperty('employee')){
            loader.getEmployee(employeeIndex).then(loadSuccessEmployee,loadFail);
        }
        else{
            var employee=employee_data.employee;
            loadDoneEmployee(employee);
        }
    }


    var loadHeader=function(month){
        $rootScope.employees[employeeIndex].headers[$scope.currentYear]?null:$rootScope.employees[employeeIndex].headers[$scope.currentYear]={};
        var headers=$rootScope.employees[employeeIndex].headers[$scope.currentYear];


       if(!headers.hasOwnProperty(month)){
            //alertModal.upstream('loading');
            loader.getHeaders(employeeIndex,month+1,$scope.currentYear).then(loadSuccessHeader,loadFail).finally(kpiService.onDone);
            $rootScope.loading=true;
       }
       else{
            var header=headers[month];
            loadSuccessHeader({data:header});
       }
    }

    var appendKPIProcess=function(kpiprocess){
        var data={};
        data.id=kpiprocess.id;
        data.name=kpiprocess.name;
        data.unit=kpiprocess.unit;
        data.pw_1=0;
        data.pw_2=0;
        data.pt_1=0;
        data.pt_2=0;
        data.real_1=0;
        data.real_2=0;
        data.kpi_header_id=kpiheaders.id;

        data.hasNew=true;

        $scope.kpiprocesses.push(data);
        dataService.digest($scope);
        return data;
    }

    var setKPIResultDetail=function(data,totalAchieveMent,IndexAchieveMent){
        setFilter(data);
        setBColor(data);
        setTotalAchievement(data,totalAchieveMent,IndexAchieveMent);
        setFinalAchivement();
        setContentEditable(data,KPI_RESULT);
    }

    var setKPIProcessDetail=function(data,totalAchieveMent,IndexAchieveMent){
        setBColorP(data);
        setTotalAchievement(data,totalAchieveMent,IndexAchieveMent);
        setFinalAchivement();
        setContentEditable(data,KPI_PROCESS);
    }

    var onAfterEdit=function(data,totalAchieveMent,IndexAchieveMent){
        const FUNCTION_NAME='realisasi-content';

        if(data===$scope.data)
            setKPIResultDetail(data,totalAchieveMent,IndexAchieveMent);
        else if(data===$scope.kpiprocesses)
            setKPIProcessDetail(data,totalAchieveMent,IndexAchieveMent);

        dataService.digest($scope);
        notifier.notifyGroup('realisasi-content');
    }

    var onDataSelected=function(context,data,toogle,dataSelected){
        var elem=context.elem;
        var cdata=context.scope.d;
        //var parent=context.scope.$parent;
        var index=parseInt(context.attrs.dIndex);
        var cindex=parseInt(context.attrs.cIndex);
        var flag=context.attrs.flag;
        //debugger;
        if(toogle){
            elem.addClass('dSelected');

            if(!dataSelected[index]){
                dataSelected[index]={};
            }
            context.data=data;
            dataSelected[index][cindex]=context;

        }
        else{
            elem.removeClass('dSelected');
            delete dataSelected[index][cindex];
            if(Object.values(dataSelected[index]).length===0)
                delete dataSelected[index];

        }
        dataService.digest($scope);
        //console.log({dataSelected});
    }

    var onDataEscaped=function(context,data,dataList,flag){
        for(var i in dataList){
            var curr=dataList[i];
            for(var j in curr){
                var curr2=curr[j];
                curr2.elem.removeClass('dSelected');
            }
        }

        if(flag==='kpiresult')
            dataSelected={};
        else if(flag==='kpiprocess')
            dataSelectedP={};

        dataService.digest($scope);
        //console.log(context,data,listSelected);
    }

    var onDataCopy=function(context,e,dataSelected){
        if(!checkSelectedData(dataSelected)){
            alertModal.display('Peringatan','Jumlah kolom data yang dipilih harus sama',true,false);
            return;
        }

        var result='';
        var buffer=[];

       // debugger;
        for(var i in dataSelected){
            var curr=dataSelected[i];
            var obj_length=Object.values(curr).length;
            var c=0;
            for(var j in curr){
                var dcurr=curr[j];
                var str;
                if(typeof(dcurr.data)==='string')
                    str=dcurr.data.replace(/\n/g,'').trim();
                else if(typeof(dcurr.data)==='number')
                    str=dcurr.data+'';
                buffer.push(str);
                c++;
            }

            if(obj_length!==0){
                var buffStr=buffer.join(' ');
                result+=buffStr+"\n";
                buffer=[];
            }

        }

        var c=(e.originalEvent||e).clipboardData;
        if(result===''){
            result=context.elem.text();
        }
        else{
            e.preventDefault();
        }
        c.clearData();
        //console.log({result});
        c.setData('text/plain',result);
        alertModal.display('Peringatan','Data telah disalin',true,false);
    }

    var onEndorseUpdated=function(){
        alertModal.hide();
        setTimeout(function(){
            $route.reload();
        },1000)

    }



    var appendDeleteListIntoStream=function(data,list,stream){
        var copy_list=angular.copy(list);
        list.push(data);
        stream.pushData(list,copy_list);
    }


    var calculateWeight=kpiService.calculateWeight;

    $scope.openMenu=function($mdMenu,ev){
        $mdMenu.open(ev);
    }

    $scope.copyData=function(){
        copyData(employeeIndex+'_kpiresult',$scope.data);
    }

    $scope.pasteData=function(){
        pasteData(employeeIndex+'_kpiresult',KPI_RESULT);
    }

    $scope.undoData=function(){
        undoData(KPI_RESULT);
    }

    $scope.redoData=function(){
        redoData(KPI_RESULT);
    }

    $scope.copyDataP=function(){
        copyData(employeeIndex+'_kpiprocess',$scope.kpiprocesses);
    }

    $scope.pasteDataP=function(){
        pasteData(employeeIndex+'_kpiprocess',KPI_PROCESS);
    }

    $scope.undoDataP=function(){
        undoData(KPI_PROCESS);
    }

    $scope.redoDataP=function(){
        redoData(KPI_PROCESS);
    }

    $scope.changeDate=function(){
            //console.log($scope.currentMonth);
            var index=$scope.currentMonth.index;
            var url=loader.angular_route('realisasi',[employeeIndex,index,$scope.currentYear]);
            $location.path(url);
    }

    $scope.addContent=kpiService.addContent;

    $scope.setWeight=function(elem,value,scope,attrs){

        var flag=attrs.flag;
        var val_int=value?parseInt(value):0;
        var setter=$parse(attrs.belongTo);
        if(val_int<0 || val_int>100){

            var default_val=kpiService.getDefaultWeighting(flag,$scope.header);
            setter.assign(scope,default_val);
            alertModal.display('Peringatan','Bobot harus diantara 0 dan 100');
        }
        else{
            calculateWeight(flag,$scope.display_weights,$scope.header);
        }
        setFinalAchivement();
    }

    $scope.realisasiContent=function(context,setter){

        var elem=context.elem;
        var scope=context.scope;
        var attrs=context.attrs;
        var flag=attrs.flag;
        var f_index=flag+'_filter';
        var format;

        if(scope.d)
            format=scope.d[f_index];
        else if(scope.p){
           // debugger;
            format=scope.p[f_index];
        }
        var sanitize=attrs.sanitize;
        var getter=setter(scope);

        if(!isUndf(sanitize)&&sanitize!==''){
            getter=$filter(sanitize)(getter);
        }
        setter.assign(scope,getter);

        if(format){
            kpiService.formatContent(format,setter,elem,scope);
        }
        else{
            elem.text(getter);
        }

    }

    $scope.addNewData=function(){
        var data={}

        data.id=null;
        data.kpi_result_id=null;
        data.kpi_header_id=kpiheaders.id;
        data.name='';
        data.unit='%';

        data.pw_1=0;
        data.pw_2=0;

        data.pt_t1=0;
        data.pt_k1=0;
        data.pt_t2=0;
        data.pt_k2=0;

        data.real_t1=0;
        data.real_k1=0;
        data.real_t2=0;
        data.real_k2=0;

        data.kpia_1=0;
        data.kpia_2=0;

        data.aw_1=0;
        data.aw_2=0;

        data.hasNew=true;

        data.pt_contentEditable=[];
        data.pw_contentEditable=[];
        data.real_contentEditable=[];
        $scope.data.push(data);
        hasNew=true;

        return data
    }

    $scope.validateNum=validator.validateNum;

    $scope.removeData=function(index){
        $scope.data.splice(index,1);
    }

    $scope.saveChanged=function(){

        updateMap.created=dataService.only(updateMap.created,kpiKeys.kpiresult);
        updateMapP.created=dataService.only(updateMapP.created,kpiKeys.kpiprocess);

        var body={
            kpiresult:updateMap,
            kpiprocesses:updateMapP,
            kpiresultdeletelist:deleteListResult?deleteListResult:[],
            kpiprocessdeletelist:deleteListProcess?deleteListProcess:[],
            weighting:{
                weight_result:$scope.header.weight_result,
                weight_process:$scope.header.weight_process,
            }
        }

        loader.savePMS($scope.header.id,body).then(saveSuccess,loadFail).finally(saveDone);
        $scope.hasChanged=true;
        $rootScope.loading=true;
        alertModal.display('Peringatan','Menyimpan data, mohon tunggu',false,true);
    }

    $scope.getHeaderColor=function(index){
        if((index>=0&&index<=1)||(index>=6&&index<=9)||(index>=12))
            return 'light-blue';
        else
            return 'light-grey';

    }

    $scope.setTotalW=function(){
        setTotalW($scope.data,$scope.totalW);
    }

    $scope.setTotalW_P=function(){
        setTotalW($scope.kpiprocesses,$scope.totalW_P);
    }

    $scope.setBColor=function(){
        onAfterEdit($scope.data,$scope.totalAchieveMent,$scope.IndexAchieveMent);
        //console.log({elem,value,scope,attrs});
    }

    $scope.setBColorP=function(){
        onAfterEdit($scope.kpiprocesses,$scope.totalAchieveMentP,$scope.IndexAchieveMentP);
    }

    $scope.mapChange=function(elem,value,scope,attrs){
        var i=attrs.iIndex;
        var d=parseInt(attrs.dIndex);
        kpiService.mapChange(d,i,value,$scope.data,updateMap);
        console.log(updateMap);
    }

    $scope.mapChangeP=function(elem,value,scope,attrs){
        var i=attrs.belongTo.replace('p.','');
        var d=parseInt(attrs.dIndex);
        kpiService.mapChange(d,i,value,$scope.kpiprocesses,updateMapP);
        console.log({updateMapP});
    }


    $scope.setEndorse=function(endorse){
        var message=!endorse.verified?'Apa anda yakin ini mengesahkan PMS ini':'Apa anda yakin ingin membalikan keadaan pada PMS ini?';
        confirmModal('Peringatan',message).then(function(){
            endorse.verified=!endorse.verified;
            loader.setEndorsement({
                id:kpiheaders.id
            }).then(onEndorseUpdated,loadFail).finally(saveDone);
            alertModal.display('Peringatan','Mengirim data, mohon tunggu',false,true);
            $scope.hasChanged=true;
            $rootScope.loading=true;
        },function(){
            $scope.aggrements[endorse.id]=!$scope.aggrements[endorse.id];
        });
    }

    $scope.isEndorseDisable=function(endorse){
        return kpiService.isEndorseDisable(endorse,$scope.kpiendorsements);
    }

    $scope.addRow=function(){

        var copy_data=angular.copy($scope.data);
        var newdata=$scope.addNewData();
        var copy_data_2=angular.copy($scope.data)
        kpiresultstream.pushData(copy_data_2,copy_data);
        var scrollHeight=kpiresult_elem.prop('scrollHeight');
        kpiresult_elem.scrollTop(scrollHeight);
        setBColor($scope.data);
        setContentEditable($scope.data,KPI_RESULT);
        kpiService.mapCreate(newdata,updateMap);
        console.log(updateMap);
    }

    $scope.addPRow=function(){
        formModal('kpiprocess').then(function(data){
            var newProcess=data.kpiprocess.selected;
            var newData;
            var checked=$scope.kpiprocesses.find(function(d){return d.id===newProcess.id});
            var copy_data=angular.copy($scope.kpiprocesses);
            if(!checked)
                newData=appendKPIProcess(newProcess);
            else{
                alertModal.display('Peringatan','Data Sasaran Proses sudah dimasukan sebelumnya',true,false);
                return;
            }
            var copy_data_2=angular.copy($scope.kpiprocesses);
            kpiprocessstream.pushData(copy_data_2,copy_data);

            var scrollHeight=kpiprocess_elem.prop('scrollHeight');
            kpiprocess_elem.scrollTop(scrollHeight);
            setBColorP($scope.kpiprocesses);
            setContentEditable($scope.kpiprocesses,KPI_PROCESS);
            kpiService.mapCreate(newData,updateMapP);
            //console.log({updateMapP});
        });
    }

    $scope.toogleDelete=function(dlt_obj){
        //var btn_delete=E('[ng-click="toogleDelete()"]');
        if(!dlt_obj.toogle_delete){
            dlt_obj.dspl_dlt=true;
            dlt_obj.toogle_delete=true;
            dlt_obj.dlt_message='Batal Hapus';
        }
        else{
            dlt_obj.dspl_dlt=false;
            dlt_obj.toogle_delete=false;
            dlt_obj.dlt_message='Hapus Baris';
        }
    }

    $scope.deleteRowP=function(index){

        var copy_data=angular.copy($scope.kpiprocesses);
        var copy_data_2=angular.copy(copy_data);

        appendDeleteListIntoStream(copy_data[index]['id'],deleteListProcess,kpiprocessdeletestream);
        copy_data.splice(index,1);
        $scope.kpiprocesses=[];
        dataService.digest($scope,null,function(){
            $scope.kpiprocesses=copy_data;
            setTotalW($scope.kpiprocesses,$scope.totalW_P);
            dataService.digest($scope);
            kpiprocessstream.pushData(copy_data,copy_data_2);
        });
    }

    $scope.deleteRow=function(index){
        var copy_data=angular.copy($scope.data);
        var copy_data_2=angular.copy(copy_data);

        copy_data[index]['id']?appendDeleteListIntoStream(copy_data[index]['id'],deleteListResult,kpiresultdeletestream):'';
        copy_data.splice(index,1);
        $scope.data=[];
        dataService.digest($scope,null,function(){
            $scope.data=copy_data;
            setTotalW($scope.data,$scope.totalW);
            dataService.digest($scope);
            kpiresultstream.pushData(copy_data,copy_data_2);
        });
    }

    $scope.setDataFilter=function(elem,value,scope,attrs){
        const FUNCTION_NAME='realisasi-content';
        var data=scope.d;
        data.unit=value;
        setFilter($scope.data);
        setTotalAchievement($scope.data,$scope.totalAchieveMent,$scope.IndexAchieveMent);
        setContentEditable($scope.data,KPI_RESULT);
        setFinalAchivement();
        dataService.digest($scope);
        notifier.notifyGroup('realisasi-content');
        //console.log($scope.data);
    }


    $scope.downloadPDF=function(){
        if(isDownloading)
            return;

       loader.fetchPMSPDF(curr_employee.id,{month:$scope.currentMonth.index+1,year:$scope.currentYear}).then(function(result){
            var filename='PMS '+curr_employee.name+' - '+$scope.currentMonth.value+' '+$scope.currendDate.getFullYear()+'.pdf';
            loader.download(result.data,filename);
       },function(){
            alertModal.display('Peringatan','Terjadi kesalahan saat mengunduh berkas');
       }).finally(function(){
            isDownloading=false;
       });
       isDownloading=true;
       alertModal.display('Berkas sedang diunduh','Mohon Tunggu');

    }

    $scope.dataSelected=function(context,data,toogle){
        onDataSelected(context,data,toogle,dataSelected);
    }

    $scope.dataSelectedP=function(context,data,toogle){
        onDataSelected(context,data,toogle,dataSelectedP);
    }

    $scope.dataEscaped=function(context,data){
        onDataEscaped(context,data,dataSelected,'kpiresult');
    }

    $scope.dataEscapedP=function(context,data){
        onDataEscaped(context,data,dataSelectedP,'kpiprocess');
    }

    $scope.dataPaste=function(context,e){
        pasteCopiedData(context,e,KPI_RESULT);
    }

    $scope.dataPasteP=function(context,e){
        pasteCopiedData(context,e,KPI_PROCESS);
    }

    $scope.dataCopy=function(context,e){
        onDataCopy(context,e,dataSelected);
    }

    $scope.dataCopyP=function(context,e){
        onDataCopy(context,e,dataSelectedP);
    }

    notifier.setNotifier('changeMonth',setCurrentMonth);
    checkEmployee();
    loadHeader(currMonth);
    pusher.on('pms-has-changed-'+employeeIndex,PMSHasChanged);
    vanishDisturbingColumn();
    $scope.currendDate.setFullYear($scope.currentYear);

});
