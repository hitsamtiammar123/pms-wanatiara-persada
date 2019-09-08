app.service('notifier',function($route){
    var funcToCall={};
    var funcArgs={};
    var restriction={};
    var notify_regex=/const FUNCTION_NAME=[\'\"](.+)[\'\"]/;

    var setRestriction=function(value,restrict){
        if(isUndf(restriction[value])){
            restriction[value]=[];
            
        }
        if(isArr(restrict)){
            restriction[value]=restriction[value].concat(restrict);
        }
    }

    var checkAuthority=function(value,caller){
        var caller_str=caller.toString();
        var match_r=caller_str.match(notify_regex);
        var match_f=match_r?match_r[1]:null;

        if(!isUndf(restriction[value])&&
        restriction[value].length!==0 &&
        match_f===null){
            throw 'This module does not have authority to notifying "'+value+'"';
        }
    }

    this.setNotifier=function(value,func,args,restrict){
        funcToCall[value]=func;
        funcArgs[value]=args;
        setRestriction(value,restrict);

    } 

    this.notify=function(value,args){
        var arg=args?args:funcArgs[value];
        var func=funcToCall[value];
        checkAuthority(value,arguments.callee.caller);

        if(func) 
            func.apply(this,arg);
        //console.log('Route in notifier',$route);
    }  

    this.setNotifierGroup=function(value,func,args,restrict,index){

        if(!funcToCall[value]){
            funcToCall[value]=[];
            funcArgs[value]=[];
            setRestriction(value,restrict);
        } 
            if(isUndf(index)){
                funcToCall[value].push(func);
                funcArgs[value].push(args);
            }
            else{
                funcToCall[value][index]=func;
                funcArgs[value][index]=args;
            }
         
    }

    this.notifyGroup=function(value,args,index){
        //console.log(funcToCall[value])
        if(!funcToCall[value])
            return; 
        checkAuthority(value,arguments.callee.caller);
        var listFunc=funcToCall[value];
        var listArgs=funcArgs[value];
        if(!isUndf(index)){
            var func=listFunc[index];
            var arg=args?args:listArgs[index];
            if(func)
                func.apply(this,arg);
        }
        else{
            for(var i in listFunc){
                var arg=args?args:listArgs[i];
                var func=listFunc[i];
                if(func)
                    func.apply(this,arg);
            }
        }
    }

    this.flushNotifier=function(value){
        delete funcToCall[value];
        delete funcArgs[value];
        delete restriction[value];
    }

}); 