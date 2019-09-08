app.provider('resolve',[function(){
    var countR=0;
    var counter=0; 

    var successF=function(onSuccess,args){
        return function(result){
            counter++;
            args.unshift(result);
            return onSuccess.apply(this,args);
        }
    }

    var failF=function(alertModal){
        return function(){
            alertModal.display('Peringatan','Terjadi kesalahan pada saat memuat data, mohon muat ulang halaman',false,true);
        }
    }

    this.$get=function(){
        return {}
    }
}]);