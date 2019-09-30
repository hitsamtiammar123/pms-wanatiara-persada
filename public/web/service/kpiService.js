app.service('kpiService',['$filter',function($filter){

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
}]);
