app.controller('ConfirmModalController',['$scope','dataService','notifier',
function($scope,dataService,notifier){
    var promise;

    $scope.title=dataService.get('confirmModalTitle');
    $scope.message=dataService.get('confirmModalMessage');
    $scope.btnMessage=dataService.get('confirmModalBtnMessage')
    
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

    var setBtnMessage=function(btnMessage){
        $scope.btnMessage=btnMessage;
        dataService.digest($scope);
    }

    $scope.resolve=function(){
        promise.resolve();   
    }

    $scope.reject=function(){
        promise.reject();
    }


    notifier.setNotifier('setConfirmModalPromise',setPromise,[],['display-confirMmodal']);
    notifier.setNotifier('setConfirmModalTitle',setTitle,[],['display-confirmModal']);
    notifier.setNotifier('setConfirmModalMessage',setMessage,[],['display-confirmModal']);
    notifier.setNotifier('setConfirmModalBtnMessage',setBtnMessage,[],['display-confirmModal'])

}]); 