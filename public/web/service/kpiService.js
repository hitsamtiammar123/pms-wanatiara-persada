app.service('kpiService',['$filter','KPI_PROCESS','KPI_RESULT',function($filter,KPI_PROCESS,KPI_RESULT){

    var formatContent=function(format,setter,elem,scope){
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
                formatContent(format,setter,elem,scope);
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

}]);
