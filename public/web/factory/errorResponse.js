app.factory('errorResponse',['alertModal',function(alertModal){
    var errorResponseDefault={
        404:function(){
            alertModal.upstream('not-found');
        },
        500:function(){
            alertModal.upstream('internal-server-error');
        },
        403:function(){
            alertModal.upstream('forbidden');
        },
        503:function(){
            alertModal.upstream('maintenance');
        }

    };

    var res=function(err){
        var status=err.status;
        if(errorResponseDefault.hasOwnProperty(status)){
            var callback=errorResponseDefault[status];
            callback(err);
        }
    }

    return res;
}]);
