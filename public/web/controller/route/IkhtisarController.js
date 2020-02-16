app.controller('IkhtisarController',function($scope,$rootScope,loader,dIndex,
    notifier,alertModal,$routeParams,errorResponse,kpiService,years,$location){

    var totalData=[];
    var currMonth=$rootScope.month;
    var vm=this;
    var page=1;
    var employee_id=$routeParams.id;
    var tag=$routeParams.tag;


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
    $scope.currentYear=$routeParams.year?parseInt($routeParams.year):$rootScope.year;
    $scope.years=years;
    vm.ikhtisar=[];

    var sI=function(res){
        $scope.data=totalData=$rootScope.ikhtisarData=res.data;
    }

    var e=errorResponse;

    var setCurrentMonth=function(month){
        currMonth=$rootScope.month=month.index;
        setHeader();
    }

    var acumulateKPIResult=function(data,header){
        for(var i=0;i<data.length;i++){
            var curr=data[i];
            curr.kpiColor='';
            curr.unitColor='';

            // var real_key='real_t';
            // var pt_key='pt_t';
            var kpia_key='kpia';
            var keys={};
            keys.pt_t='pt_t';
            keys.real_t='real_t';
            keys.real_k='real_k';
            keys.pt_k='pt_k';
            var aw_key='aw';
            var rt;

            if(!header.hasTags){
                if(!kpiService.isPriviledgesKPIResult(curr,keys.pt_t,header))
                    rt=kpiService.getKPIAKPIResult(curr,keys);
                else
                    rt=kpiService.getKPIAKPIResultByPriviledge(curr,kpia_key);
            }
            else{
                rt=kpiService.getKPIAResultTag(curr.unit,curr,{real_t:'real_t',pt_t:'pt_t'});
            }
            curr[kpia_key]=rt+'%';

            var pwqIndex='pw';
            kpiService.setAW(curr,pwqIndex,aw_key,rt);

        }
    }

    var accumulateKPIProcess=function(data,header){
        for(var i=0;i<data.length;i++){
            var curr=data[i];
            if(!header.hasTags){
                curr.kpia=kpiService.getKPIAKPIProcess(curr,{real:'real',pt:'pt'});
                kpiService.setAW(curr,'pw','aw',curr.kpia);
            }
            else
                curr.kpia=kpiService.getKPIAProcessTag(curr.unit,curr,{real:'real'});
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

    var getKPIAchivement=function(header,kpiresults,kpiprocesses){
        if(!header.hasTags){
            var kpiAchievement={
                totalAchieveMent:{},
                IndexAchieveMent:{},
                totalAchieveMentP:{},
                IndexAchieveMentP:{},
                finalAchievement:{}
            };
            var awIndex='aw';
            var keys={};
            keys.tr='ta_result';
            keys.tp='ta_process';
            keys.t_n='tn';
            keys.t_i='ti';
            keys.t_f='tf';

            kpiService.setTotalAchievement(kpiresults,kpiAchievement.totalAchieveMent,kpiAchievement.IndexAchieveMent,'ta_result',awIndex);
            kpiService.setTotalAchievement(kpiprocesses,kpiAchievement.totalAchieveMentP,kpiAchievement.IndexAchieveMentP,'ta_process',awIndex);
            kpiService.setFinalAchievement(kpiAchievement.totalAchieveMent,kpiAchievement.totalAchieveMentP,header,kpiAchievement.finalAchievement,keys);
            return {
                kpia:kpiAchievement.finalAchievement.tn?kpiAchievement.finalAchievement.tn:0,
                bColor:getBColor(kpiAchievement.finalAchievement.tn)
            };
        }
        else{
            var obj={
                kpiresult:kpiresults,
                kpiprocess:kpiprocesses
            }
            kpiService.setTotalAchievementKPITag(obj,header.weight_result,header.weight_process,{kpiresult:'kpiresult',kpiprocess:'kpiprocess'});
            return{
                kpia:obj.ta?obj.ta:0,
                bColor:getBColor(obj.ta)
            }
        }
    }

    var fetchTotalAchievement=function(headers){
        var totalAchivements=new Array(12);

        for(var i=0;i<headers.length;i++){
            var header=headers[i];
            var date=new Date(header.period);
            var currentTA={};
            acumulateKPIResult(header.kpiresultheaders,header);
            accumulateKPIProcess(header.kpiprocesses,header);
            currentTA=getKPIAchivement(header,header.kpiresultheaders,header.kpiprocesses);
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
            var totalAchivements=fetchTotalAchievement(curr.kpiheaders);
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
        if(isUndf(employee_id) || employee_id==='year')
            loader.getIkhtisar(page,$scope.currentYear).then(onSuccess,e).finally(kpiService.onDone);
        else{
            if(!isUndf(tag))
                loader.getIkhtisarWithTagID(employee_id,$scope.currentYear).then(onSuccess,e).finally(kpiService.onDone);
            else
                loader.getIkhtisarWithEmployeeID(employee_id,$scope.currentYear).then(onSuccess,e).finally(kpiService.onDone);
            $scope.hide_load_btn=true;
        }
        //alertModal.upstream('loading');
        $scope.disable_load_btn=true;
    }

    $scope.loadMore=function(){
        if(isUndf(employee_id) || employee_id==='year')
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

    $scope.changeDate=function(){
        var routeParams=[employee_id?employee_id:'year',$scope.currentYear];
        !isUndf(tag)?routeParams.push('tag'):null;
        var url=loader.angular_route('ikhtisar',routeParams);
        $location.path(url);
    }


    notifier.setNotifier('changeMonth',setCurrentMonth);
    loadIkhtisarData();

});
