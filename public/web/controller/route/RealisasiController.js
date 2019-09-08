app.controller('RealisasiController',function($scope,$rootScope,validator,loader,$route,
    $filter,notifier,copier,alertModal,dataService,user,$routeParams,formModal,confirmModal
    ,$sce,pusher,months,$location){
       

    $scope.totalAchieveMent={};
    $scope.IndexAchieveMent={}; 
    $scope.IndexAchieveMentP={};
    $scope.totalAchieveMentP={};
    $scope.aggrements={};
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
    $scope.f=[
        'Jan 一月',
        'Feb 二月',
        'Mar 三月',
        'Apr 四月',
        'May 五月',
        'June 六月',
        'July 七月',
        'Aug 八月',
        'Sept 九月',
        'Oct 十月',
        'Nov 十一月',
        'Dec 十二月'
    ];;
    $scope.kpiprocesses=[];
    $scope.headerLabelProcess=['Juni','Juli','Target Juni','Target Juli','Realisasi Juni','Realisasi Juli','Juni','Juli','Juni','Juli'];
    $scope.pw_indices=['pw_1','pw_2'];
    $scope.pt_indices=['pt_t1','pt_k1','pt_t2','pt_k2'];
    $scope.real_indices=['real_t1','real_k1','real_t2','real_k2'];
    $scope.kpia_indices=['kpia_1','kpia_2'];
    $scope.aw_indices=['aw_1','aw_2'];
    $scope.header={};
    $scope.user=user;
    $scope.hasEndorse=true;
    $scope.months=months;
    

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
    const KPI_RESULT='kpiresult';
    const KPI_PROCESS='kpiprocess';  
    var kpiresult_elem=E('.realisasi-table tbody').eq(0); 
    var kpiprocess_elem=E('.realisasi-table tbody').eq(1); 
    var currentEmployee=user.employee;
    var deleteListResult=[];
    var deleteListProcess=[];

    $scope.currentMonth=$scope.months[currMonth];

    var onFail=function(a,b,c){
        console.log(a,b,c)
    }
    
    var PMSHasChanged=function(){
        var curr=$route.current.$$route.controller;
        flushStreamAndHeader();
        if(curr==='RealisasiController'){
            alertModal.display('Peringatan','Terjadi Perubahan Data',false,true);
            setTimeout($route.reload,1500);
        }        
    }

    var setUsers=function(){
        $scope.employee=curr_employee;
        $scope.atasan=curr_employee.atasan;
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
        $scope.headerLabelProcess=[];
        var t;
        for(var i=0;i<10;i++){
            var input=''
            t=(i%2===0)?currMonth-1:currMonth;

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
        var pbS=new Date(kpiheaders.period_start);
        pbS.setFullYear(pbS.getFullYear()-1);
        pbS.setMonth(11);

        var pbSb=new Date(kpiheaders.period_start);
        var pbEb=new Date(kpiheaders.period_end);
        
        $scope.pb.start=pbSb.getDate()+' '+$scope.f[pbSb.getMonth()]+' '+pbSb.getFullYear();
        $scope.pb.end=pbEb.getDate()+' '+$scope.f[pbEb.getMonth()]+' '+pbEb.getFullYear();
        
        $scope.pb.startB=pbS.getDate()+' '+$scope.f[pbS.getMonth()]+' '+pbS.getFullYear();
        $scope.pb.endB=pbEb.getDate()+' '+$scope.f[pbEb.getMonth()]+' '+pbEb.getFullYear();
    }
 
    var setHeader=function(){
        setHeaderResultLabel();
        setHeaderProcessLabel();
        setUserHeading();
    }
    
    var getAchievementIndex=function(s){
        var index='';
        if(s<80){
            index="D";
        }
        else if(s>=80 && s<82){
            index="C"
        }
        else if(s>=82 && s<85){
            index="C+"
        }
        else if(s>=85 && s<90){
            index="B-"
        }
        else if(s>=90 && s<95){
            index="B"
        } 
        else if(s>=95 && s<100){
            index="B+"
        }
        else if(s>=100 && s<102){
            index="A-"
        }
        else if(s>=102 && s<105){
            index="A"
        }
        else if(s>=105){
            index="A+"
        }

        return index;
    }

    var sumTotalAchievement=function(data,i){
        var s=0;
        var awIndex='aw_';
        for(var j=0;j<data.length;j++){
            var curr=data[j];
            var currIndex=awIndex+(i+1);
            var aw=curr[currIndex];
            var n=parseFloat(aw);
            s+=n;
        }
        return s;
    }
 
 
    var setTotalAchievement=function(data,totalAchieveMent,IndexAchieveMent){
        const FUNCTION_NAME='add-content';
      	if(!data)
          	return;
        
        var salaryIndex={};

        //debugger;
        for(var i=0;i<2;i++){
            var s=sumTotalAchievement(data,i);
            var q='t'+(i+1);
            if(isNaN(s))
                continue;
            totalAchieveMent[q]=s.toFixed(1)+'%';
            var index=getAchievementIndex(s);
            IndexAchieveMent[q]=index;

            switch(index){
                case 'A+':
                    salaryIndex[q]='10%';
                break;
                case 'A':
                    salaryIndex[q]='7.5%';
                break;
                case 'A-':
                    salaryIndex[q]='5%';
                break;
                case 'B+':
                case 'B':
                case 'B-':
                    salaryIndex[q]='0%';
                break;
                case 'C+':
                    salaryIndex[q]='-5%';
                break
                case 'C':
                    salaryIndex[q]='-7.5%';
                break;
                case 'C-':
                    salaryIndex[q]='-10%';
                break;
                case 'D+':
                    salaryIndex[q]='-12.5%';
                break;
                case 'D':
                    salaryIndex[q]='-15%';
                break;
                case 'D-':
                    salaryIndex[q]='-20%';
                break;
            }
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

    var setContentEditable=function(data,type){
        if(type===KPI_RESULT){

            for(var i=0;i<data.length;i++){
                var curr=data[i];
                curr.kpia_contentEditable=false;
                curr.aw_contentEditable=false;
                if($scope.hasEndorse){
                    curr.pw_contentEditable=false;
                    curr.pt_contentEditable=false;
                    curr.real_contentEditable=false;
                    curr.name_contentEditable=false;
                    curr.unit_contentEditable=false;
                }
                else{
                    curr.pw_contentEditable=true;
                    curr.pt_contentEditable=true;
                    curr.real_contentEditable=true;
                    curr.name_contentEditable=true;
                    curr.unit_contentEditable=true;
                }
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
                    curr.contentEditable.unit=true;
                    curr.contentEditable.pw_1=true;
                    curr.contentEditable.pw_2=true;
                    curr.contentEditable.pt_1=true;
                    curr.contentEditable.pt_2=true;
                    curr.contentEditable.real_1=true;
                    curr.contentEditable.real_2=true;
                }

            }
        }
    }

    var getKPIA=function(d,j){
        var unit=d.unit;

        var pt_key='pt_t'+(j+1);
        var real_key='real_t'+(j+1);
        var real_k_key='real_k'+(j+1);
        var pt_k_key='pt_k'+(j+1);
        var rC;
        var tC;
        var rt;
        switch(unit){
            case '$':
                if(j===1){
                    rC=d[real_k_key];
                    tC=d[pt_k_key];
                    break;
                }
            default:
                rC=d[real_key];
                tC=d[pt_key];
            break;
        }
        rt=(parseFloat(rC)/parseFloat(tC))*100;

        return rt;
    }

    var setBColor=function(data){
        //debugger;
        for(var i=0;i<data.length;i++){
            var curr=data[i];
            var counter=1;
            curr.kpiColor='';
            curr.unitColor='';
            for(var j=0;j<2;j++){
                var kpia_key='kpia_'+(j+1);
                var aw_key='aw_'+(j+1);
                var rt;
            
                rt=getKPIA(curr,j);

                if(isNaN(rt)||!isFinite(rt)){
                    rt=0;
                    curr.kpia_contentEditable='true';
                }
                else
                    rt=rt.toFixed(1);

                
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
                var pwq=curr[pwqIndex];
                var calculate=rt*parseFloat(pwq)/100;
                curr[aw_key]=(calculate.toFixed(1));
            
            }
        }

    }
    
    var setBColorP=function(data){
        var getColor=function(r){
            if(r<0)
                return 'black-column';
            else if(r===0)
                return 'green-column';
            else if(r===1)
                return 'blue-column';
            else if(r>1)
                return 'gold-column';
            else
                return '';
        }

        var getIndex=function(r){
            if(r<0)
                return 80;
            else if(r===0)
                return 100;
            else if(r===1)
                return 110;
            else if(r>1)
                return 120;
            else
                return 0;
        }

        for(var i=0;i<data.length;i++){
            var curr=data[i];
            curr.contentEditable={};
            curr.kpia_filter='addPercent';
            curr.aw_filter='addPercent';
            curr.pw_filter='addPercent';

            var kt_1=parseInt(curr.real_1)-parseInt(curr.pt_1);
            var kt_2=parseInt(curr.real_2)-parseInt(curr.pt_2);
            curr.kpia_1=getIndex(kt_1);
            curr.kpia_2=getIndex(kt_2);
            curr.bColor_kpia_1=getColor(kt_1);
            curr.bColor_kpia_2=getColor(kt_2);

            curr.aw_1=((curr.kpia_1/100)*parseInt(curr.pw_1)).toFixed(1);
            curr.aw_2=((curr.kpia_2/100)*parseInt(curr.pw_2)).toFixed(1);
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

    var setDataFilter=function(d){
        var unit=d.unit.trim();
        d.pw_filter='addPercent';
        d.pt_filter='';
        d.real_filter='';
        d.kpia_filter='addPercent';
        d.aw_filter='addPercent';
        d.pw_sanitize=d.pt_sanitize=d.real_sanitize=d.kpia_sanitize=d.aw_sanitize='sNumber';
        switch(unit){
            case '%':
            case 'MV':
                d.pt_filter='addPercent';
                d.real_filter='addPercent';
            break;
            case '$':
            case 'WMT':
                d.pt_filter='number';
                d.real_filter='number';
            break;
        }
    }

    var parseDataToInt=function(d,keys){
        for(i in keys){
            d[keys[i]]=parseInt(d[keys[i]]);
        }
    }

    var setFilter=function(data){
        for(var i=0;data&&i<data.length;i++){
            var d=data[i];
            setDataFilter(d);
            switch(d.unit){
                case '$':
                case '#':
                case 'WMT':
                case 'MT':
                    var keys=$scope.pt_indices.concat($scope.real_indices);
                    parseDataToInt(d,keys);
                    d.pt_k2=(parseInt(d.pt_k1)+parseInt(d.pt_t2))+'';
                    d.real_k2=(parseInt(d.real_k1)+parseInt(d.real_t2))+'';
                break;  
            }
        }

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
            console.log({deleteListResult});
        }
        else if(flag===KPI_PROCESS){
            _undoDt=kpiprocessstream.undoData();
            deleteListProcess=_undoDt?kpiprocessdeletestream.undoData():deleteListProcess;
            console.log({deleteListProcess});
        }
        applyCopiedTable(_undoDt,flag);
       
    }
 
    var redoData=function(flag){
        var _redoDt;
        if(flag===KPI_RESULT){
            _redoDt=kpiresultstream.redoData();
            deleteListResult=_redoDt?kpiresultdeletestream.redoData():deleteListResult;
            console.log({deleteListResult});
        }
        else if(flag===KPI_PROCESS){
            _redoDt=kpiprocessstream.redoData();
            deleteListProcess=_redoDt?kpiprocessdeletestream.redoData():deleteListProcess;
            console.log({deleteListProcess});
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
                if(ccdata!=='')
                    $scope.data[p_index][i_index]=ccdata;

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
        $scope.aggrementCount=0;
        for(var i in $scope.kpiendorsements){
            var endorse=$scope.kpiendorsements[i];
            $scope.aggrements[endorse.id]=endorse.verified==1?true:false;

            if(endorse.verified==1){
                $scope.aggrementCount++;
            }

            if(endorse.verified===0&& endorse.employee.id===user.employee.id){
                if($scope.employee.id===user.employee.id || 
                    $scope.atasan.id===user.employee.id){
                        $scope.hasEndorse=false;
                    }
            }

        }

        $scope.aggrementStr='HRD <span class="glyphicon glyphicon-ok"></span>';
        $scope.aggrementStr=$sce.trustAsHtml($scope.aggrementStr);

        
    }

    var initData=function(){
        //debugger;
        const FUNCTION_NAME='realisasi-content';
        setHeader();
        setUsers();
        setFilter($scope.data);
        setBColor($scope.data);
        setTotalAchievement($scope.data,$scope.totalAchieveMent,$scope.IndexAchieveMent);
        setTotalW($scope.data,$scope.totalW);

        setBColorP($scope.kpiprocesses);
        setTotalAchievement($scope.kpiprocesses,$scope.totalAchieveMentP,$scope.IndexAchieveMentP);
        setTotalW($scope.kpiprocesses,$scope.totalW_P);
        setAggrements();
        setContentEditable($scope.data,KPI_RESULT);
        setContentEditable($scope.kpiprocesses,KPI_PROCESS);
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
        $rootScope.employees[employeeIndex].headers[currMonth]=kpiheaders;
        //user=header.employee;
        $scope.data=header.kpiresults;
        $scope.kpiprocesses=header.kpiprocesses;
        $scope.kpiendorsements=header.kpiendorsements;
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

    var loadFail=function(){
        alertModal.display('Peringatan','Terjadi Kesalahan pada saat memuat data',false,true);
    }

    var checkEmployee=function(){
        if(!$rootScope.employees.hasOwnProperty(employeeIndex)){
            $rootScope.employees[employeeIndex]={};
            $rootScope.employees[employeeIndex].headers={};
        }
    }

    var flushHeaderPropertyOnRootScope=function(){
        for(var i=0;i<12;i++){
            if($rootScope.employees[employeeIndex].headers.hasOwnProperty(i)){
                delete $rootScope.employees[employeeIndex].headers[i];
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
        var headers=$rootScope.employees[employeeIndex].headers;


       if(!headers.hasOwnProperty(month)){
            alertModal.upstream('loading');
            loader.getHeaders(employeeIndex,month+1).then(loadSuccessHeader,loadFail);
       }
       else{
           var header=headers[month];
           kpiheaders=header;
           $scope.header=kpiheaders;
           $scope.data=header.kpiresults;
           $scope.kpiprocesses=header.kpiprocesses;
           $scope.kpiendorsements=header.kpiendorsements;
           $scope.kpiendorsementIndex=Object.keys($scope.kpiendorsements).reverse();
           loadEmployee();
       }
    }

    var appendKPIProcess=function(kpiprocess){
        var data={};
        data.id=kpiprocess.id;
        data.name=kpiprocess.name;
        data.unit=kpiprocess.unit;
        data.pw_1='';
        data.pw_2='';
        data.pt_1='';
        data.pt_2='';
        data.real_1='';
        data.real_2='';

        $scope.kpiprocesses.push(data);
        dataService.digest($scope);
    }
    
    var setKPIResultDetail=function(data,totalAchieveMent,IndexAchieveMent){
        setBColor(data);
        setTotalAchievement(data,totalAchieveMent,IndexAchieveMent);
        setFilter(data);  
    }

    var setKPIProcessDetail=function(data,totalAchieveMent,IndexAchieveMent){
        setBColorP(data);
        setTotalAchievement(data,totalAchieveMent,IndexAchieveMent);
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
        console.log({dataSelected});
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
        console.log({result});
        c.setData('text/plain',result);
        alertModal.display('Peringatan','Data telah disalin',true,false);
    }

    var onEndorseUpdated=function(result){
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

    $scope.changeMonth=function(){
        //console.log($scope.currentMonth);
        var index=$scope.currentMonth.index;
        var url=loader.angular_route('realisasi',[employeeIndex,index]);
        $location.path(url);
    }

    $scope.addContent=function(context,setter){
        //debugger;
        var elem=context.elem;
        var scope=context.scope;

        var value=setter(scope);
        
        if(!isUndf(value)){
            elem.text(value);
        }
        else{
            elem.text('');
        }
        //console.log({attrs,value,scope})
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
            var v=setter(scope);
            var f;
            var sp_f=format.split('|');
            for(var i=0;i<sp_f.length;i++){
                var csp=sp_f[i];
                var nvalue=$filter(csp)(v);
                f=nvalue?nvalue:v;
                elem.text(f)
                v=f;
            }
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
        data.unit='';

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

        data.pt_contentEditable='true';
        data.pw_contentEditable='true';
        data.real_contentEditable='true';
        $scope.data.push(data);
        hasNew=true;
    }
 
    $scope.validateNum=validator.validateNum;
 
    $scope.removeData=function(index){
        $scope.data.splice(index,1); 
    } 

    $scope.saveChanged=function(){

        var body={
            kpiresult:$scope.data,
            kpiprocesses:$scope.kpiprocesses,
            kpiresultdeletelist:deleteListResult?deleteListResult:[],
            kpiprocessdeletelist:deleteListProcess?deleteListProcess:[]
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
  
    $scope.setBColor=function(elem,value,scope,attrs){
        onAfterEdit($scope.data,$scope.totalAchieveMent,$scope.IndexAchieveMent);
        //console.log({elem,value,scope,attrs});
    } 

    $scope.setBColorP=function(elem,value,scope,attrs){
        onAfterEdit($scope.kpiprocesses,$scope.totalAchieveMentP,$scope.IndexAchieveMentP);
    }
    
    $scope.setEndorse=function(endorse){
        var message=!endorse.verified?'Apa anda yakin ini mengesahkan PMS ini':'Apa anda yakin ingin membalikan keadaan pada PMS ini?';
        confirmModal('Peringatan',message).then(function(){
            endorse.verified=!endorse.verified;
            loader.setEndorsement({
                id:endorse.id,
                verified:endorse.verified
            }).then(onEndorseUpdated);
            alertModal.display('Peringatan','Mengirim data, mohon tunggu',false,true);
        },function(){
            $scope.aggrements[endorse.id]=!$scope.aggrements[endorse.id];
        })
    }

    $scope.isEndorseDisable=function(endorse){
        if(endorse.employee.id===user.employee.id){
            if(endorse.verified)
                return true;
            else{
                var endorsements=$scope.kpiendorsements;
                var level=endorse.level;
                for(var i=1;i<level;i++){
                    var curr_endorse=endorsements[i];
                    if(curr_endorse && !curr_endorse.verified)
                        return true;
                }
                return false;
            }
        }
        else
            return true;
    }

    $scope.addRow=function(){

        var copy_data=angular.copy($scope.data);
        $scope.addNewData();
        var copy_data_2=angular.copy($scope.data)
        kpiresultstream.pushData(copy_data_2,copy_data);
        var scrollHeight=kpiresult_elem.prop('scrollHeight');
        kpiresult_elem.scrollTop(scrollHeight);
    }

    $scope.addPRow=function(){
        formModal('kpiprocess').then(function(data){
            var newProcess=data.kpiprocess.selected;
            var checked=$scope.kpiprocesses.find(function(d){return d.id===newProcess.id});
            var copy_data=angular.copy($scope.kpiprocesses);
            if(!checked)
                appendKPIProcess(data.kpiprocess.selected);
            else
                alertModal.display('Peringatan','Data Sasaran Proses sudah dimasukan sebelumnya',true,false);
            var copy_data_2=angular.copy($scope.kpiprocesses);
            kpiprocessstream.pushData(copy_data_2,copy_data);

            var scrollHeight=kpiprocess_elem.prop('scrollHeight');
            kpiprocess_elem.scrollTop(scrollHeight);
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
            dataService.digest($scope);
            kpiresultstream.pushData(copy_data,copy_data_2);
        });
    }

    $scope.getQIndex=function(keyIndex,i){
        var r='';

        switch(keyIndex){
            case 'kpia':
            case 'aw':
                if(i===1)
                    i++;
                r='q'+(i+1);
            break;
            default:
                r='q'+(i+1);
            break;
        }

        
        return r;

    }

    $scope.setDataFilter=function(elem,value,scope,attrs){
        const FUNCTION_NAME='realisasi-content';
        var data=scope.d;
        data.unit=value;
        setDataFilter(data);
        dataService.digest($scope);
        notifier.notifyGroup('realisasi-content');
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


    setTimeout(function(){
        E('#disturbing').remove();
    },10)
    E('#unit-bug').next().attr('id','disturbing');
}) 