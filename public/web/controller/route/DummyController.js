app.controller('DummyController',['$scope','formModal','confirmModal','pusher',
function($scope,formModal,confirmModal,pusher){
   // debugger; 
    //console.log({kpiheader,employee});

    var getData=function(){
        var result=[];
        for(var i=0;i<15;i++){
            var list={};
            list.label="label "+(i+1);
            list.data=i;
            result.push(list);
        }
        return result;
    }

    formModal.init('testModal',{
        test3:{
            type:'select',
            message:'Masukan hehe 4',
            list:getData(),
            label:'label'
        }
    },'Silakan masukan data');

    $scope.showModal=function(){
        pusher.dismiss();
        console.log('unsubscribe');
    }


}]);   