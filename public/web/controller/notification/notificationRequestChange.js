app.controller('NotificationRequestChange',['$scope','$routeParams','notifier',
'$location','alertModal','loader','confirmModal','notificationService',
function($scope,$routeParams,notifier,$location,alertModal,loader,confirmModal,notificationService){
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

    var onFail=function(){
        alertModal.display('Peringatan','Terjadi kesalahan saat mengubah data');
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



    notificationService.initNotification(id,$scope,setNotification);
    //notifier.setNotifier('notificationsHasLoad',initNotification);


}]);
