app.controller('NotificationRequestChange',['$scope','$routeParams','$rootScope','notifier',
'$location','alertModal','loader','user','confirmModal',
function($scope,$routeParams,$rootScope,notifier,$location,alertModal,loader,user,confirmModal){
    var id=$routeParams.id;

    $scope.currNotification;
    $scope.message='';

    var setMessage=function(){
        var type=$scope.currNotification.type;
        if(type==='request-change'){
            $scope.titleMessage=$scope.currNotification.subject;
            $scope.message=$scope.currNotification.message;
        }
    }

    var setNotification=function(){
        if(!$scope.currNotification.read_at){
            notifier.notify('decrementUnreadNotification',[$scope.currNotification]);
        }

        setMessage();
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


    $scope.toPMS=function(){
        if($scope.currNotification.type==='redirect'){
            var url=$scope.currNotification.redirectTo;
            $location.path(url);
        }
    }

    $scope.approvedChange=function(){
        if($scope.currNotification.type==='request-change'){
            confirmModal('Peringatan','Apakah anda yakin ingin mengubah status pengesahan?').then(function(){
                var data={
                    notificationID:id
                }
                var employeeID=$scope.currNotification.to.id;
                loader.resetEndorsement(employeeID,data).then(hasApproved,onFail);

                alertModal.upstream('loading');

            },function(){
                $scope.currNotification.approved=!$scope.currNotification.approved;
            })
        }
    }


    initNotification();
    //notifier.setNotifier('notificationsHasLoad',initNotification);


}]);
