app.controller('PencarianController',function($scope,loader,$q,$timeout,$location){

    var interval=[];
    var deffered;
    var hasSelected=false;
    var result_data={};
    var searchLoading=false;

    $scope.searchText='';
    $scope.selectedItem;
    $scope.items=[];
    $scope.loadingMessage='';
    $scope.hasSearch=true;
    $scope.cache=true;


    var onFetchSuccess=function(result){
        $scope.items=result.data;
        deffered.resolve($scope.items);

    }

    var onFetchFail=function(){
        deffered.reject();
    }

    var onFetchDone=function(){
        $scope.loadingMessage='';
        searchLoading=false;
    }

    var onFetchResultSuccess=function(result){
        result_data=result.data;
        if(result_data!==''){
            $scope.hasSearch=false;
            hasSelected=false;

        }

    }

    var ferchResultData=function(){
        //console.log($scope.selectedItem);
        var selectedItem={};
        if(hasSelected){
            selectedItem=$scope.selectedItem;
        }
        else{
            selectedItem={
                item:$scope.searchText,
                type:'employee',
                isAuto:true
            }
        }

        loader.getSearchResult(selectedItem).then(onFetchResultSuccess,onFetchFail).finally(onFetchDone);
    }

    var fetchResultList=function(){
        var query=$scope.searchText;
        loader.getSearchList(query).then(onFetchSuccess,onFetchFail)
    }

    var timeout=function(){
        interval.pop();
        if(interval.length===0 && $scope.searchText!==''){
            hasSelected=false;
            fetchResultList()
        }
    }

    $scope.toPMSPage=function(){
        var r=result_data;
        var url='';
        if(r.tag===null)
            url='/realisasi/'+r.id;
        else
            url='/realisasi-group/'+r.tag.id;
        $location.path(url);
    }

    $scope.toIkhtisarPage=function(){
        var r=result_data;
        url='/ikhtisar/'+r.id;
        $location.path(url);
    }

    $scope.queryResult=function(){
        deffered=$q.defer();
        return deffered.promise;
    }

    $scope.cari=function(){
            if(searchLoading)
                return;
            $scope.loadingMessage='Memuat...';
            ferchResultData();
            searchLoading=true;
    }

    $scope.filterData=function(){
        var search=$scope.searchText.toLowerCase();
        var result=$scope.items.filter(function(data){
            return data.item.toLowerCase().search(search)!==-1
        });
        return result;
    }

    $scope.selectedItemChange=function(item){
        if(item)
            hasSelected=true;
    }

    $scope.searchTextChange=function(searchText){
        $scope.hasSearch=true;
        hasSelected=false;
        interval.push(searchText);
        $timeout(timeout,500);
    }
});
