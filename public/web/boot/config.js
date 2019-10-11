app.config(function(cPProvider,$routeProvider,$locationProvider,routingProvider,$rootScopeProvider,formModalProvider){

    //var argList=arguments.callee.toString().match(/function\((.+)\)/)[1].split(',');
    var argList=[
        'cPProvider',
        '$routeProvider',
        '$locationProvider',
        'routingProvider',
        '$rootScopeProvider'
    ];
    for(var i=0;i<argList.length;i++){
        var a=argList[i];
        var arg=arguments[i];
        if(cPProvider.hasOwnProperty(a))
            cPProvider[a](arg);
    }
});
