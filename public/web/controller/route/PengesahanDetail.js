app.controller('PengesahanDetail',['$scope','$routeParams','$rootScope','notifier',
'$location','alertModal','loader','user','confirmModal',
function($scope,$routeParams,$rootScope,notifier,$location,alertModal,loader,user,confirmModal){
    var id=$routeParams.id;

    $scope.currNotification;
    $scope.message='';

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

    var hasApproved=function(){
        alertModal.hide();
        $location.path('/target-manajemen');
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
