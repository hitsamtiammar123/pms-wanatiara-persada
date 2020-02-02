app.controller('TargetManajemenController',function(
    $scope,$rootScope,loader,notifier,alertModal,user,dataService,$location){
        $scope.user=user.employee;
        $scope.staffs;
        $scope.employeeList=$rootScope.employeeList?$rootScope.employeeList:[];

        var setInitialEmployee=function(){
            $scope.employeeList=[];
            $scope.employeeList.push(user.employee);
            $scope.staffs=user.employee.bawahan;
        }

        var setEmployeeList=function(){
            if($scope.employeeList.length===0){
                setInitialEmployee();
            }
            else{
                var employee_temp=$scope.employeeList[$scope.employeeList.length-1];
                checkEmployeeData(employee_temp.id);
                var employee=$rootScope.employees[employee_temp.id].employee;
                if(employee)
                    $scope.staffs=employee.bawahan;
                else
                    setInitialEmployee();
            }
        }

        var checkEmployeeData=function(id){
            if(!$rootScope.employees.hasOwnProperty(id)){
                $rootScope.employees[id]={};
                $rootScope.employees[id].headers={};
                return false;
            }
            return true;
        }

        var sliceEmployeeList=function(employee){
            var index=$scope.employeeList.indexOf(employee);
            $scope.employeeList=$scope.employeeList.slice(0,index+1);
            $rootScope.employeeList=$scope.employeeList;
            return index;
        }

        var loadEmployeeSuccess=function(result){
            var employee=result.data;
            var id=employee.id;
            $scope.staffs=employee.bawahan;
            $rootScope.employees[id].employee=employee;
            dataService.digest($scope);
            if(employee.bawahan.length===0)
                alertModal.display('Peringatan','Bawahan dari '+employee.name+' Tidak ada',true,false);
            $rootScope.loading=false;
        }

        var loadEmployeeFail=function(employee){
            return function(){
                //debugger;
                var index=$scope.employeeList.indexOf(employee);
                $scope.employeeList=$scope.employeeList.slice(0,index);
                alertModal.display('Peringatan','Terjadi kesalahan saat memuat data, mohon coba lagi');
                $rootScope.loading=false;
            }

        }

        var loadEmployee=function(employee){
            loader.getEmployee(employee.id).then(loadEmployeeSuccess,loadEmployeeFail(employee));
            dataService.digest($scope,function(){
                $scope.staffs=[];
            });
            $rootScope.loading=true;
        }

        $scope.expandEmployee=function(employee){
            var data=$scope.employeeList.find(function(d,index){
                return d.id===employee.id;
            });
            if(!data){
                $scope.employeeList.push(employee);
                $rootScope.employeeList=$scope.employeeList;
                if(!checkEmployeeData(employee.id)){
                    loadEmployee(employee);
                }
                else{
                    var employee_1=$rootScope.employees[employee.id].employee;
                    if(employee_1)
                        $scope.staffs=employee_1.bawahan;
                    else
                        loadEmployee(employee);
                }
            }
            else{

                if($scope.employeeList[$scope.employeeList.length-1]===employee){
                    $scope.toPMS(employee);
                    return;
                }

                var index=sliceEmployeeList(employee);
                $rootScope.employeeList=$scope.employeeList;
                if(index!==0)
                    $scope.staffs=$rootScope.employees[employee.id].employee.bawahan
                else
                    $scope.staffs=user.employee.bawahan;
            }
            //console.log(id);
        }

        $scope.toPMS=function(bawahan){
            var url='';
            if(bawahan.tags.length===0)
                url='realisasi/'+bawahan.id;
            else
                url='realisasi-group/'+bawahan.tags[0].id;
            $location.path(url);
        }

        setEmployeeList();
});
