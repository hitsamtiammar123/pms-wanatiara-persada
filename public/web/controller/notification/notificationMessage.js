app.controller('NotificationMessage',['$scope','$routeParams','$rootScope','notifier'
,'alertModal','loader','user',
function($scope,$routeParams,$rootScope,notifier,alertModal,loader,user){
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


    var onLoadSuccess=function(result){
        alertModal.hide();
        $scope.currNotification=result.data;
        setNotification();
    }

    var onFail=function(){
        console.log('Fail to load notification');
    }

    var fetchNotification=function(){
        loader.getNotification(user.employee.id,id).then(onLoadSuccess,onFail);
        alertModal.upstream('loading');
    }

    var initNotification=function(){
        if($rootScope.notification_list){
            $scope.currNotification=$rootScope.notification_list.find(function(d){
                return d.id===id;
            });

            if($scope.currNotification){
                setNotification();
            }
            else{
                fetchNotification();
            }

        }
        else{
            fetchNotification();
        }
    }

    initNotification();
    //notifier.setNotifier('notificationsHasLoad',initNotification);


}]);
