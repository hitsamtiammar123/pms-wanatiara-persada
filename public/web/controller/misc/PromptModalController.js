app.controller('PromptModalController',['$scope','dataService','notifier',
function($scope,dataService,notifier){
    var promise;

    $scope.title=dataService.get('promptModalTitle');
    $scope.message=dataService.get('promptModalMessage');
    $scope.input_text='';
    $scope.type=dataService.get('promptModalType');

    var setPromise=function(_promise){
        promise=_promise;
    }

    var setTitle=function(title){
        $scope.title=title; 
        dataService.digest($scope);
    }    
 
    var setMessage=function(message){
        $scope.message=message;
        dataService.digest($scope);
    }

    var setText=function(text){
        $scope.input_text=text;
        dataService.digest($scope);
    }

    var setType=function(type){
        $scope.type=type;
        dataService.digest($scope);
    }

    $scope.resolve=function(){
        promise.resolve($scope.input_text);   
    }

    $scope.reject=function(){
        promise.reject();
    }


    notifier.setNotifier('setPromptModalPromise',setPromise,[],['display-promptModal']);
    notifier.setNotifier('setPromptModalMessage',setMessage,[],['display-promptModal']);
    notifier.setNotifier('setPromptModalTitle',setTitle,[],['display-promptModal']);
    notifier.setNotifier('setPromptModalText',setText,[],['display-promptModal']);
    notifier.setNotifier('setPromptModalType',setType,[],['display-promptModal'])
}]); 