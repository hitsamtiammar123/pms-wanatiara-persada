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
        },
        415:function(){
            alertModal.upstream('file-not-supported');
        },
        'default':function(){
            alertModal.upstream('something-wrong');
        },
        '-1':function(){
            alertModal.upstream('connection-lost');
        }

    };

    var res=function(err){
        var status=err.status;
        if(errorResponseDefault.hasOwnProperty(status)){
            var callback=errorResponseDefault[status];
            callback(err);
        }
        else
            errorResponseDefault.default(err);
    }

    return res;
}]);
