app.controller('NotificationMessage',['$scope','$routeParams','notifier'
,'notificationService',
function($scope,$routeParams,notifier,notificationService){
    var id=$routeParams.id;

    $scope.currNotification;
    $scope.message='';

    var setMessage=function(){
            $scope.titleMessage='Notifikasi';
            $scope.message=$scope.currNotification.subject;
    }

    var setNotification=function(){
        if(!$scope.currNotification.read_at){
            notifier.notify('decrementUnreadNotification',[$scope.currNotification]);
        }

        setMessage();
    }


    notificationService.initNotification(id,$scope,setNotification);
    //notifier.setNotifier('notificationsHasLoad',initNotification);


}]);
