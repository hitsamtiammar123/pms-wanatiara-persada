app.controller('AlertModalController',function($scope,notifier,dataService){
    $scope.title=dataService.get('alertModalTitle');
    $scope.message=dataService.get('alertModalMessage');
    $scope.showButton=dataService.get('alertModalShowButton');

    var setTitle=function(title){
        $scope.title=title; 
        dataService.digest($scope);
    }    
 
    var setMessage=function(message){
        $scope.message=message;
        dataService.digest($scope);
    }

    var setHideButton=function(showButton){
        $scope.showButton=showButton;
        dataService.digest($scope);
    }



    notifier.setNotifier('setAlertModalTitle',setTitle,[],['display-alertModal']);
    notifier.setNotifier('setAlertModalMessage',setMessage,[],['display-alertModal']);
    notifier.setNotifier('setAlertModalHideButton',setHideButton,[],['display-alertModal']);
});