app.directive('formController',function(){

    function controller($scope,notifier,dataService){
        var _setPromiseName="set"+$scope.id+"Promise";
        var promise;

        var setPromise=function(_promise){
            promise=_promise;
        }

        var sanitizeData=function(){
            var result={};
            for(var d in $scope.data){
                var curr=$scope.data[d];
                var type=curr.type;
                var p={};


                switch(type){
                    case 'text':
                    case 'password':
                        p.text=curr.text;
                    break;
                    case 'select':
                        p.selected=curr.selected;
                    break;
                }

                result[d]=p;
            }
            return result;
        }

        $scope.ok=function(){
            //console.log('Ini di modal '+id);
            promise.resolve(sanitizeData());
           
        }

        $scope.cancel=function(){
            //console.log('Udah ditunda di '+id);
            promise.reject();
        }

        //console.log('Ini scope dari form Controller di function controller',$scope);
        notifier.setNotifier(_setPromiseName,setPromise);
    }

    function link(scope,elem,attr){
        //console.log('Ini scope dari form Controller di function link',scope);
    }

    return{
        restrict:'A',
        link:link,
        controller:controller
    }

}); 