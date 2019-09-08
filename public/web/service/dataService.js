app.service('dataService',function(DTIME){
    var listData={};

    this.get=function(key){
        return listData[key];
    }

    this.set=function(key,value){
        listData[key]=value;
    } 

    this.has=function(key){
        return listData.hasOwnProperty(key);
    }

    this.digest=function(scope,pre,post){
        setTimeout(function(){
            scope.$digest();
            post?post(scope):'';
        },DTIME);
        pre?pre(scope):'';
    }

    

    this.only=function(list,keys){
        var result=[];

        for(var i in list){
            var r={};
            var obj=list[i];
            for(var j in keys){
                var key=keys[j];
                r[key]=obj[key];
            }
            result.push(r);
        }

        return result;
    }

});