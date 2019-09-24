app.controller('NotificationRedirect',['$scope','$routeParams','$rootScope','notifier',
'$location','alertModal','loader','user',
function($scope,$routeParams,$rootScope,notifier,$location,alertModal,loader,user){
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


    $scope.toPMS=function(){
        if($scope.currNotification.type==='redirect'){
            var url=$scope.currNotification.redirectTo;
            $location.path(url);
        }
    }


    initNotification();
    //notifier.setNotifier('notificationsHasLoad',initNotification);


}]);
