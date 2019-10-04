app.service('kpiService',['$filter','KPI_PROCESS','KPI_RESULT','months','user',
function($filter,KPI_PROCESS,KPI_RESULT,months,user){
    var self=this;
    var f=months.map(function(d){
        return d.value;
    });

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

}]);
