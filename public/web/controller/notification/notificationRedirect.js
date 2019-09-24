app.controller('NotificationRedirect',['$scope','$routeParams','notifier',
'$location','notificationService',
function($scope,$routeParams,notifier,$location,notificationService){
    var id=$routeParams.id;

    $scope.currNotification;
    $scope.message='';

    var setMessage=function(){
        var type=$scope.currNotification.type;
        if(type==='redirect'){
            $scope.titleMessage='Notifikasi Pemberitahuan Pengesahan';
            $scope.message=$scope.currNotification.subject;
        }
    }

    var setNotification=function(){
        if(!$scope.currNotification.read_at){
            notifier.notify('decrementUnreadNotification',[$scope.currNotification]);
        }

        setMessage();
    }


    $scope.toPMS=function(){
        if($scope.currNotification.type==='redirect'){
            var url=$scope.currNotification.redirectTo;
            $location.path(url);
        }
    }


    notificationService.initNotification(id,$scope,setNotification);
    //notifier.setNotifier('notificationsHasLoad',initNotification);


}]);
