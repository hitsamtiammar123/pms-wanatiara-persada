app.controller('NotifikasiPengesahanController',['$scope','$rootScope','loader','notifier','user','dataService',
'$location',
function($scope,$rootScope,loader,notifier,user,dataService,$location){
    $scope.notifications=[];
    $scope.notification_list;
    $scope.hasLoad=false;
    $scope.isLoading=false;
    $scope.page;

    var notification_data;

    var hasNextPage=function(page){
        var start=(page-1)*5;

        return $rootScope.notification_list[start]?true:false;
    }

    var paginate=function(page){

        var start=(page-1)*5;
        var end=page*5;
        $scope.notifications=$rootScope.notification_list.slice(start,end);
        $rootScope.page=page;
    }

    var setButtonFlag=function(){
        $scope.hasLoad=true;
        $scope.isLoading=false;
    }

    var setFrontEnd=function(){

        $scope.totalPage=Math.ceil(notification_data.total/5);
        $scope.page=$rootScope.page?$rootScope.page:notification_data.page;
        dataService.digest($scope);
    }

    var initNotification=function(){
        if($rootScope.notifications){
            notification_data=$rootScope.notifications;
            setFrontEnd();
            setButtonFlag();
            paginate($scope.page);
        }
    }

    var setNotifications=function(result){
        $scope.notifications=result.data;
        $rootScope.notification_list=$rootScope.notification_list.concat(result.data.data);
        setButtonFlag();
        paginate($scope.page);
    }

    $scope.toNotification=function(notification){
        var id=notification.id;
        var type=notification.type;
        var url=loader.angular_route('notification-detail',[id,type]);
        $location.path(url);
    }

    $scope.nextPage=function(){
        if( $scope.page!==$scope.totalPage){
            $scope.page++;
            if(!hasNextPage($scope.page)){
                loader.getNotifications(user.employee.id,$scope.page).then(setNotifications);
                $scope.isLoading=true;
                $scope.notifications=[];
            }
            else{
                paginate($scope.page);
            }

        }
    }

    $scope.prevPage=function(){
        if($scope.page!==1){
            $scope.page--;
            paginate($scope.page);
        }
    }

    initNotification();
    notifier.setNotifier('notificationsHasLoad',initNotification);

}]);
