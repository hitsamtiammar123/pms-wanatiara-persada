app.config(function({providers}){
    var argList=arguments.callee.toString().match(/function\((.+)\)/)[1].split(',');
    for(var i=0;i<argList.length;i++){
        var a=argList[i];
        var arg=arguments[i];
        if(cPProvider.hasOwnProperty(a))
            cPProvider[a](arg);
    }
});