app.controller('TargetManajemenController',function(
    $scope,$rootScope,loader,alertModal,user,dataService,$location,pusher){
        $scope.user=user.employee;
        $scope.staffs;
        $scope.employeeList=$rootScope.employeeList?$rootScope.employeeList:[];
        $scope.isLoadingLog=false;
        $scope.isLogHide=false;
        $scope.logs=[];
        $scope.searchTextLog='';

        var logPage=1;

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

        var onLogLoadSuccess=function(result){

            var logData=result.data.data;
            if(result.data.count!==0){
                $scope.logs=$scope.logs.concat(logData);
                logPage++;
                dataService.digest($scope);
            }
            else
                $scope.isLogHide=true;

        }

        var loadEmployee=function(employee){
            loader.getEmployee(employee.id).then(loadEmployeeSuccess,loadEmployeeFail(employee));
            dataService.digest($scope,function(){
                $scope.staffs=[];
            });
            $rootScope.loading=true;
        }

        var initLog=function(searchText){
            var fetcher=!searchText?loader.fetchLog(logPage):loader.fetchLog(logPage,searchText)

            fetcher.then(onLogLoadSuccess,function(){
                console.log('Terjadi kesalahan saat memuat log');
            }).finally(function(){
                $scope.isLoadingLog=false;
            });
            $scope.isLoadingLog=true;
        }

        var logListener=function(result){
            //console.log({result});
            if(!$scope.isLoadingLog)
                $scope.logs.unshift(result.data);
        }

        $scope.expandEmployee=function(employee,$index){

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

                if($scope.employeeList[$scope.employeeList.length-1]===employee && $index!==0){
                    var id=employee.id;
                    var url=loader.angular_route('realisasi',[id]);
                    $location.path(url);
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

        $scope.initLog=function(){
            initLog($scope.searchTextLog);
        }

        $scope.refreshLog=function(){
            if($scope.isLoadingLog)
                return;
            $scope.isLogHide=false;
            $scope.logs=[];
            logPage=1;
            $scope.searchTextLog='';
            $scope.initLog();
        }

        $scope.searchLog=function(){
            if($scope.isLoadingLog || $scope.searchTextLog==='')
                return;
            $scope.logs=[];
            logPage=1;
            initLog($scope.searchTextLog);
        }

        $scope.onLogKeyUp=function(e){
            if(e.keyCode===13)
                $scope.searchLog();
        }

        initLog();
        setEmployeeList();
        pusher.on('log-listener',logListener);
});
