app.service('kpiService',['$filter','KPI_PROCESS','KPI_RESULT','months','user','$rootScope',
function($filter,KPI_PROCESS,KPI_RESULT,months,user,$rootScope){
    var self=this;
    var f=months.map((d)=>d.value);

    var sumTotalAchievement=function(data,aw_index){
        var s=0;
        for(var j=0;j<data.length;j++){
            var curr=data[j];
            var aw=curr[aw_index];
            var n=parseFloat(aw);
            s+=n;
        }
        return s;
    }

    var sumTotalAchievementKPITag=function(data){
        var sum=0;
        var count=0;
        for(var i in data){
            var d=data[i];
            sum+=parseInt(d.kpia);
            count++;
        }
        return sum/count;
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
            case 'WMT':
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

    this.formatContent=function(format,setter,elem,scope){
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


    this.addContent=function(context,setter){
        //debugger;
        var elem=context.elem;
        var scope=context.scope;

        var value=setter(scope);

        if(!isUndf(value)){
            var format=context.attrs.format;
            if(format){
                self.formatContent(format,setter,elem,scope);
            }
            else{
                elem.text(value);
            }
        }
        else{
            elem.text('');
        }
        //console.log({attrs,value,scope})
    }

    this.getAchievementIndex=function(s){
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

    this.getDefaultWeighting=function(flag,header){
        return (flag===KPI_RESULT)?(header.weight_result*100):(header.weight_process*100);
    }

    /**
     * berfungsi untuk melakukan kalkulasi ulang setelah bobot diubah
     *
     * @param {string} flag variabel yang menentukan akan melakukan kalkulasi terhadap kpiresult atau kpiprocess
     */
    this.calculateWeight=function(flag,display_weights,header){
        var w_r=display_weights.weight_result?parseInt(display_weights.weight_result):0;
        var w_p=display_weights.weight_process?parseInt(display_weights.weight_process):0;

        if(flag===KPI_RESULT){
            w_p=100-w_r;
        }
        else if(flag===KPI_PROCESS)
            w_r=100-w_p;
        header.weight_result=w_r/100;
        header.weight_process=w_p/100;
        display_weights.weight_process=w_p;
        display_weights.weight_result=w_r;

    }

    this.setHeaderPeriod=function($scope,header){
        var pbS=new Date(header.period_start);
        pbS.setFullYear(pbS.getFullYear()-1);
        pbS.setMonth(11);

        var pbSb=new Date(header.period_start);
        var pbEb=new Date(header.period_end);
        $scope.pb={};

        $scope.pb.start=pbSb.getDate()+' '+f[pbSb.getMonth()]+' '+pbSb.getFullYear();
        $scope.pb.end=pbEb.getDate()+' '+f[pbEb.getMonth()]+' '+pbEb.getFullYear();

        $scope.pb.startB=pbS.getDate()+' '+f[pbS.getMonth()]+' '+pbS.getFullYear();
        $scope.pb.endB=pbEb.getDate()+' '+f[pbEb.getMonth()]+' '+pbEb.getFullYear();
    }


    /**
     * untuk melakukan mapping pada data yang mau diubah
     *
     * @param {number} d index dari data KPIResult
     * @param {string} i index dari data yang mau diubah
     * @param {*} value nilai baru
     */
    this.mapChange=function(d,i,value,listData,_updateMap){
        console.log(d,i,value,listData,_updateMap);
        var data=listData[d];
        if(!_updateMap.hasOwnProperty('updated'))
            _updateMap.updated={};

        if(!data.id)
            return;

        if(!_updateMap.updated.hasOwnProperty(data.id)){
            _updateMap.updated[data.id]={
                id:data.id,
                kpi_header_id:data.kpi_header_id?data.kpi_header_id:data.pivot.kpi_header_id
            };
        }
        var mapping=_updateMap.updated[data.id];
        mapping[i]=value;
    }

    this.mapCreate=function(newdata,_updateMap){
        if(!_updateMap.hasOwnProperty('created'))
            _updateMap.created=[];
        _updateMap.created.push(newdata);
    }

    this.setAggrements=function($scope,employee,atasan){
        $scope.aggrementCount=0;
        $scope.aggrements={};
        if(!$scope.hasOwnProperty('kpiendorsements'))
            return;

        for(var i in $scope.kpiendorsements){
            var endorse=$scope.kpiendorsements[i];
            $scope.aggrements[endorse.id]=(endorse.verified==true)?true:false;

            if(endorse.verified==true){
                $scope.aggrementCount++;
            }

            if(endorse.verified==false&& endorse.id===user.employee.id){
                if(employee.id===user.employee.id ||
                    atasan.id===user.employee.id){
                        $scope.hasEndorse=false;
                    }
            }

        }
    }

    this.isEndorseDisable=function(endorse,endorsements){
        if(endorse.id===user.employee.id){
            if(endorse.verified)
                return true;
            else{
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

    this.onDone=function(){
        $rootScope.loading=false;
    }

    this.getKPIAKPIResult=function(d,keys){
        var unit=d.unit;

        var pt_key=keys.pt_t;
        var real_key=keys.real_t;
        var real_k_key=keys.real_k;
        var pt_k_key=keys.pt_k;
        var rC;
        var tC;
        var rt;
        switch(unit){
            case '$':
                rC=d[real_k_key];
                tC=d[pt_k_key];
                break;
            default:
                rC=d[real_key];
                tC=d[pt_key];
            break;
        }
        rt=(parseFloat(rC)/parseFloat(tC))*100;

        if(isNaN(rt)||!isFinite(rt)){
            rt=0;
        }
        else
            rt=rt.toFixed(1);

        return rt;
    }

    this.getKPIAKPIProcess=function(curr,keys){

        var r=curr[keys.real]-curr[keys.pt];
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

    this.getKPIAKPIResultByPriviledge=function(curr,kpia_key){
        var rt=0;
        if(curr.hasOwnProperty(kpia_key)){
            rt=parseInt(curr[kpia_key]);
        }
        else
            rt=100;


        return rt;
    }

    this.setAW=function(curr,pw_key,aw_key,rt){
        var pwq=curr[pw_key];
        var calculate=rt*parseFloat(pwq)/100;
        curr[aw_key]=(calculate.toFixed(1));
    }

    this.setTotalAchievement=function(data,totalAchieveMent,IndexAchieveMent,q,aw_index){
        var s=sumTotalAchievement(data,aw_index);
        s=!isNaN(s)?s:0;
        totalAchieveMent[q]=s.toFixed(1);
        var index=self.getAchievementIndex(s);
        IndexAchieveMent[q]=index;
    }

    this.setFinalAchievement=function(totalAchieveMent,totalAchieveMentP,header,finalAchievement,keys){
        var t1_fr=parseFloat(totalAchieveMent[keys.tr]);
        var t1_fp=parseFloat(totalAchieveMentP[keys.tp]);

        finalAchievement[keys.t_n]=(t1_fr*header.weight_result+
                                    t1_fp*header.weight_process).toFixed(1);

        finalAchievement[keys.t_i]=self.getAchievementIndex(finalAchievement[keys.t_n]);

        finalAchievement[keys.t_f]=(finalAchievement[keys.t_n]-100).toFixed(1);
    }


     this.getKPIAResultTag=function(unit,e_result,keys){
        var rC;
        var tC;
        var rt;

        rC=e_result[keys.real_t];
        tC=e_result[keys.pt_t];

        switch(unit){
            case 'MT':
            case 'WMT':
                rt=(parseFloat(rC)/parseFloat(tC));
            break;
            case '规模 Skala':
            case '规模 Scale':
                rt=rC;
            break;
        }

        return getKPIAIndex(rt,unit);
    }

    this.getKPIAProcessTag=function(unit,e_process,keys){
        var i;

        i=e_process[keys.real];

        return getKPIAIndex(i,unit);
    }

    this.setTotalAchievementKPITag=function(obj,weight_result,weight_process,keys){
        var taR=sumTotalAchievementKPITag(obj[keys.kpiresult]) * weight_result;
        var taP=sumTotalAchievementKPITag(obj[keys.kpiprocess]) * weight_process;
        obj.ta=(taR+taP).toFixed(2);
        obj.ia=self.getAchievementIndex(obj.ta);
    }

    this.isPriviledgesKPIResult=function(curr,t_key,kpiheaders){
        if(kpiheaders && (
            curr.unit === '#' ||
            curr.unit ==='kwh'
            )
        ){
            var t=parseInt(curr[t_key]);
            if(t===0)
                return true;
            else
                return false;
        }
        return false;
    }


}]);
