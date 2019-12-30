app.controller('IkhtisarController',function($scope,$rootScope,loader,dIndex,
    notifier,alertModal,$routeParams,errorResponse,kpiService){

    var totalData=[];
    var currMonth=$rootScope.month;
    var vm=this;
    var page=1;
    var employee_id=$routeParams.id;


    $scope.dIndex=$rootScope.dIndex!==undefined?$rootScope.dIndex:dIndex;
    $scope.headerList=[];
    $scope.disable_load_btn=false;
    $scope.hide_load_btn=false;
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
    ];
    vm.ikhtisar=[];

    var sI=function(res){
        $scope.data=totalData=$rootScope.ikhtisarData=res.data;
    }

    var e=errorResponse;

    var setCurrentMonth=function(month){
        currMonth=$rootScope.month=month.index;
        setHeader();
    }

    var acumulateKPIResult=function(data){
        for(var i=0;i<data.length;i++){
            var curr=data[i];
            curr.kpiColor='';
            curr.unitColor='';

            var real_key='real_t';
            var pt_key='pt_t';
            var kpia_key='kpia';
            var aw_key='aw';
            var rC=curr[real_key];
            var tC=curr[pt_key];
            var rt;

            rt=(parseFloat(rC)/parseFloat(tC))*100

            if(isNaN(rt)||!isFinite(rt)){
                rt=0;
            }
            else
                rt=rt.toFixed(1);


            curr[kpia_key]=rt+'%';
            var pwqIndex='pw';
            var pwq=curr[pwqIndex];
            var calculate=rt*parseFloat(pwq)/100;
            curr[aw_key]=(calculate.toFixed(1));

        }
    }

    var getBColor=function(number){
        if(number>=120){
            return 'gold-column'
        }
        else if(number>=105 && number<120){
            return 'blue-column'
        }
        else if(number>=95 && number<105){
            return 'green-column'
        }
        else if(number<95){
            return 'red-column'
        }
    }

    var getKPIAchivement=function(kpiresults,date){
        var kpiAchievement;
        var awIndex='aw';

            var aw_total=0;
            var month=date.getMonth();
            for(var j=0;j<kpiresults.length;j++){
                var curr=kpiresults[j];
                var currIndex=awIndex;
                var aw=curr[currIndex];
                var n=parseFloat(aw);
                aw_total+=n;
            }
            var color=getBColor(aw_total);
            kpiAchievement={kpia:aw_total.toFixed(2),bColor:color};

        return kpiAchievement;
    }

    var setTotalAchivement=function(headers){
        var totalAchivements=new Array(12);

        for(var i=0;i<headers.length;i++){
            var header=headers[i];
            var date=new Date(header.period);
            acumulateKPIResult(header.kpiresultheaders);
            var currentTA=getKPIAchivement(header.kpiresultheaders,date);
            totalAchivements[date.getMonth()]=currentTA;
        }
        return totalAchivements;
    }

    var getIndex=function(kpia,date){
        var indices=new Array(12);
        //debugger;
        for(var i in kpia){
            var k=kpia[i];
            if(k){
                var r=k.kpia-100;
                indices[i]=r.toFixed(2);
            }
        }
        return indices;
    }

    var setIkhtisar=function(result){
        var ikhtisars=[];
        for(var i=0;i<result.length;i++){
            var employee={};
            var curr=result[i];
            employee.name=curr.name;
            employee.role=curr.role;
            var totalAchivements=setTotalAchivement(curr.kpiheaders);
            employee.kpia=totalAchivements;
            employee.index=getIndex(employee.kpia);
            ikhtisars.push(employee);
        }
        vm.ikhtisar=vm.ikhtisar.concat(ikhtisars);
    }

    var onSuccess=function(results){
        var result=results.data;
        //debugger;
        if(result.data.length!==0){
            setIkhtisar(result.data);
            last_page=result.last_page;
            page++;
            next_page_url=result.next_page_url;
            $scope.disable_load_btn=false;
        }
        else{
            $scope.hide_load_btn=true;
        }
        alertModal.hide();
    }

    var loadIkhtisarData=function(){
        if(isUndf(employee_id))
            loader.getIkhtisar(page).then(onSuccess,e).finally(kpiService.onDone);
        else{
            loader.getIkhtisarWithEmployeeID(employee_id).then(onSuccess,e).finally(kpiService.onDone);
            $scope.hide_load_btn=true;
        }
        //alertModal.upstream('loading');
        $scope.disable_load_btn=true;
    }

    $scope.loadMore=function(){
        if(!employee_id)
            loadIkhtisarData();
    }

    $scope.getColor=function(ikh){
        //console.log({ikh})
        var intIkh=parseInt(ikh);
        var color='';
        if(intIkh>=120){
            color='gold-font';
        }
        else if(intIkh>=105 && intIkh<120){
            color='blue-font';
        }
        else if(intIkh>=95 && intIkh<105){
            color='green-font';
        }
        else if(intIkh<95){
            color='red-font';
        }

        return color;
    }


    notifier.setNotifier('changeMonth',setCurrentMonth);
    //setHeader();
    loadIkhtisarData();
    //setIkhtisar();

});
