app.directive('changeFor',['$parse',function($parse){
    function linkFunction(scope,elem,attrs){
        var f=$parse(attrs.changeFor)(scope);
        elem.bind('change',function(){
            f(elem,scope,attrs);
        });
    }
 
    return {
        restrict:'A',
        link:linkFunction
    }
}]) 