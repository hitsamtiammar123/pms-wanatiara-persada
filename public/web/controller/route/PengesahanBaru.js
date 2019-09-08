app.controller('PengesahanBaru',['$scope','loader','user','dataService','$location','alertModal',
function($scope,loader,user,dataService,$location,alertModal){
    $scope.requestable_users=[];
    $scope.noRequestable=true;
    $scope.curr_user=null;
    $scope.sendTo='';
    $scope.note='';
    $scope.isSending=false;

    var sendHasSuccess=function(){
        alertModal.hide();
        $location.path('/target-manajemen');

    }
    
    var sendHasDone=function(){
        $scope.isSending=true;
    }

    var loadFail=function(a){
        console.log(a);
    }

    var loadSuccess=function(result){
        $scope.requestable_users=result.data;
        if($scope.requestable_users.length!==0){
            $scope.noRequestable=false;
            $scope.note="Catatan: Anda hanya bisa mengirim permintaan sekali untuk satu bawahan";
        }
        else{
            $scope.note="Belum ada pengguna yang mengesahkan PMS nya";
        }
    }

    var getRequestableUsers=function(){
        loader.getRequestableUsers(user.employee.id).then(loadSuccess,loadFail);
    }

    $scope.send=function(){
        if(!$scope.noRequestable){
            var data={
                subject:$scope.subject,
                to:$scope.curr_user.sendTo.id,
                message:$scope.message
            };
            var employeeID=$scope.curr_user.id;
            loader.requestChange(employeeID,data).then(sendHasSuccess,loadFail).finally(sendHasDone);
            alertModal.display('Peringatan','Mengirim data, mohon tunggu',false,true);
            $scope.isSending=true;

        }
    }

    getRequestableUsers();

}]); 