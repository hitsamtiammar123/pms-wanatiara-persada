app.directive('ngFile',function($parse){

    function link(scope,elem,attrs){

        var v=attrs.ngFile;
        var setter=$parse(v)

        var file=elem[0];
       

        elem.on('change',function(e){
            setter.assign(scope,file.files[0]);
        });

    }

    return {
        restrict:'A',
        link:link
    }
}); 