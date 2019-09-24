app.controller('PengesahanDetail',['$scope','$routeParams','$rootScope','notifier',
'$location','alertModal','loader','user','notificationService',
function($scope,$routeParams,$rootScope,notifier,$location,alertModal,loader,user,notificationService){
    var id=$routeParams.id;

    $scope.currNotification;
    var toNotification=function(){
        var type=$scope.currNotification.type;
        var id=$scope.currNotification.id;
        var url=loader.angular_route('notification-detail',[id,type]);
        $location.path(url);
    }

    var setNotification=function(){
        if(!$scope.currNotification.read_at){
            notifier.notify('decrementUnreadNotification',[$scope.currNotification]);
        }
        toNotification();
    }

    notificationService.initNotification(id,$scope,setNotification);


}]);
