app.controller('LogUpdate',['$scope',function($scope){
    $scope.detail=[
        {
            label:"Data Sasaran Hasil pada KPI \" Ini KPI sudah diubah\" ",
            type:'update',
            changes:[
                {
                    changeOn:"Data Realisasi pada bulan Agustus",
                    oldValue:"10",
                    newValue:"12"
                },
                {
                    changeOn:"Data Realisasi pada bulan Agustus",
                    oldValue:"10",
                    newValue:"12"
                },
                {
                    changeOn:"Data Realisasi pada bulan Agustus",
                    oldValue:"10",
                    newValue:"12"
                }
            ]
        },
        {
            label:"Data Sasaran Hasil baru sudah dibuat dengan nama \" Ini KPI sudah diubah\" ",
            type:'create',
            changes:[
                {
                    changeOn:"Nama",
                    value:"10%"
                },
                {
                    changeOn:"Unit",
                    value:"10%"
                },
                {
                    changeOn:"Performance Weightning bulan Agustus",
                    value:"10%"
                }
            ]
        },
        {
            label:"Data Sasaran Hasil denga nama \"Hehehe\" sudah dihapus ",
            type:'netral'
        },
        {
            label:"Bobot sudah diubah dari 10% menjadi 30% ",
            type:'netral'
        }
    ]
    $scope.shownTables={};

    var initTables=function(){
        for(i in $scope.detail){
            $scope.shownTables[i]=true;
          }
    }

    $scope.toogleShown=function(i){
        $scope.shownTables[i]= !$scope.shownTables[i]
    }

    $scope.getHeader=function(d){
        if(d.type==='update'){
            return [
                'Perubahan pada',
                'Nilai Baru',
                'Nilai Lama'
            ];
        }
        else if(d.type==='create'){
            return [
                'Data',
                'Nilai'
            ];
        }
    }

    $scope.getData=function(d){
        if(d.type==='update'){
            return [
                'Perubahan pada',
                'Nilai Baru',
                'Nilai Lama'
            ];
        }
        else if(d.type==='create'){
            return [
                'Data',
                'Nilai'
            ];
        }
    }

    initTables();
}]);
