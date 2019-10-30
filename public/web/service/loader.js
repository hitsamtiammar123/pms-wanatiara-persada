app.service('loader',function($rootScope,$http,DTIME,dataService,route,kpiKeys){

    const MAX_SCOUNT=5;
    var sCount=1;
    var that=this;

    const ajaxConfig={
        transformRequest:angular.identity,
        headers:{
            'Content-Type':undefined,
            'Access-Control-Allow-Headers': 'Content-Type',
            'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
            'Access-Control-Allow-Origin':'*'
        }
    };

    const ajaxConfig2={
        headers:{
            'Content-Type':'application/x-www-form-urlencoded',
            'Access-Control-Allow-Headers': 'Content-Type',
            'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
            'Access-Control-Allow-Origin':'*'
        }
    };



    var e=function(a,b,c){
        console.log(a,b,c);
    }


    var getRouteParam=function(param,param_url){
        var p='';
        var p_u='';
        if(param){
            p=param.join('/');
        }

        if(param_url){
            p_u+='?'+E.param(param_url)
        }

        return p+p_u;
    }

    this.getByGroupTag=function(tagID,month){
        var url=this.route('kpitag',[tagID],{month:month});

        return $http.get(url);
    }

    this.savePMSGroup=function(tagID,dataChange,headerChanged){
        var url=this.route('kpitag',[tagID]);
        var sentData={};
        sentData.dataChanged=JSON.stringify(dataChange);
        sentData.headerChanged=JSON.stringify(headerChanged);
        sentData._method='PUT'

        return $http.post(url,E.param(sentData),ajaxConfig2);

    }

    this.requestChange=function(employeID,data){
        var url=this.route('request-change',[employeID]);
        var fd=new FormData();

        for(var i in data){
            fd.append(i,data[i]);
        }
        return $http.post(url,fd,ajaxConfig);
    }

    this.getRequestableUsers=function(employeeID){
        var url=this.route('requestable-users',[employeeID]);
        return $http.get(url);
    }

    this.notificationMarkAsRead=function(employeID,id){
        var url=this.route('mark-as-read',[employeID,id]);
        return $http.get(url);
    }

    this.getNotifications=function(employeID,page){
        page=page?page:1;
        var url=this.route('get-notification',[employeID],{page:page});

        return $http.get(url);
    }

    this.getNotification=function(employeID,id){
        var url=this.route('get-notification',[employeID,id]);

        return $http.get(url);
    }


    this.getKPICompany=function(){
        var url=this.route('kpicompany');
        return $http.get(url);
    }

    this.getSearchResult=function(item){
        var url=this.route('search.result',null,{search:item.item,type:item.type});
        return $http.get(url);
    }

    this.setEndorsementGroup=function(tagID){
        var url=this.route('kpitag',[tagID,'endorse']);
        return $http.post(url,E.param({_method:'PUT'}));
    }

    this.getSearchList=function(query){
        var url=this.route('search.autocomplete',null,{query:query});
        return $http.get(url);
    }

    this.getIkhtisarWithEmployeeID=function(id){
        var url=this.route('ikhtisar',null,{employee:id});
        return $http.get(url);
    }


    this.getIkhtisar=function(page){
        var url=this.route('ikhtisar',null,{page:page});
        return $http.get(url);
    }

    this.getHeaders=function(id,month){

        var url=this.route('kpiheader',[id],{month:month});
        return $http.get(url);
    }

    this.getEmployee=function(id){
        var url=this.route('employee',[id]);
        return $http.get(url);
    }

    this.getKPIProcess=function(){
        var url=this.route('kpiprocess');
        return $http.get(url);
    }

    this.savePMS=function(id,body){
        var url=this.route('kpiheader',[id]);
        var sentData={};

        sentData.kpiresult=JSON.stringify(
            body.kpiresult
            );
        sentData.kpiprocesses=JSON.stringify(
            body.kpiprocesses
            );
        sentData.kpiresultdeletelist=JSON.stringify(
            body.kpiresultdeletelist
        );
        sentData.kpiprocessdeletelist=JSON.stringify(
            body.kpiprocessdeletelist
        );
        sentData.weighting=JSON.stringify(
            body.weighting
        );
        sentData._method='PUT';

        return $http.post(url,E.param(sentData),ajaxConfig2);
    }

    this.fetchPMSPDF=function(id,option){
        var url=this.route('pdf-pms',[id],option);
        return $http.get(url,{responseType:'blob'});
    }

    this.fetchPMSGroupPDF=function(id,option){
        var url=this.route('pdf-pms-group',[id],option);
        return $http.get(url,{responseType:'blob'});
    }

    this.fetchLog=function(page,searchtext){
        var param={};
        param.page=page;
        searchtext?param.q=searchtext:null;

        var url=this.route('log',[],param);
        return $http.get(url);
    }

    this.uploadKPICompany=function(data){
        var form=new FormData();
        form.append('file',data.file);

        var url=this.route('kpicompany.upload',[data.id]);
        return $http.post(url,form,ajaxConfig);
    }

    this.resetEndorsement=function(id,data){
        var url=this.route('kpiendorsement',[id,'reset']);
        data._method='PUT';

       return $http.post(url,E.param(data),ajaxConfig2);
    }

    this.updateProfile=function(id,profile){
        var url=this.route('employee',[id]);
        profile._method='PUT';

        return $http.post(url,E.param(profile),ajaxConfig2);
    }

    this.updatePassword=function(id,password){
        var url=this.route('employee',[id,'password']);
        password._method='PUT';

        return $http.post(url,E.param(password),ajaxConfig2);
    }

    this.setEndorsement=function(data){
        var url=this.route('kpiendorsement',[data.id]);
        data._method='PUT';
        return $http.post(url,data);
    }

    this.route=function(name,param,param_url){
        return route.url+route.routelist[name]+getRouteParam(param,param_url);
    }

    this.angular_route=function(name,param,param_url){
        return route.angular_route[name]+getRouteParam(param,param_url);
    }

    this.download=function(blob,filename){
        const url=URL.createObjectURL(blob);
        var a=E('<a>').attr({
            href:url,
            download:filename
        });
        E('body').append(a);
        a[0].click();
        URL.revokeObjectURL(url);

    }



});
