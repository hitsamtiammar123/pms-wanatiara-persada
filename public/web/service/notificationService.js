app.service('notificationService',['$rootScope','loader','alertModal','user','errorResponse',
function($rootScope,loader,alertModal,user,errorResponse){

    var setNotification=null;
    var scope={};
    var id;

    var onLoadSuccess=function(result){
        alertModal.hide();
        scope.currNotification=result.data;
        setNotification?setNotification():null;
    }

    var onFail=errorResponse;

    var fetchNotification=function(){
        loader.getNotification(user.employee.id,id).then(onLoadSuccess,onFail);
        alertModal.upstream('loading');
    }

    this.initNotification=function(_id,$scope,_setNotification){
        setNotification=_setNotification?_setNotification:null;
        scope=$scope?$scope:{};
        id=_id;

        if($rootScope.notification_list){
            $scope.currNotification=$rootScope.notification_list.find(function(d){
                return d.id===id;
            });

            if($scope.currNotification){
                setNotification?setNotification():null;
            }
            else{
                fetchNotification();
            }

        }
        else{
            fetchNotification();
        }
    }
}]);
