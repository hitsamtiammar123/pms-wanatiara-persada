app.controller('DummyController',['$scope','dataService',
function($scope,dataService){
    $scope.isShown=false;

    $scope.pencetHehe=function(){
        $scope.isShown=!$scope.isShown
    }
}]);
