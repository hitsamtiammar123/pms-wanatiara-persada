app.filter('integer',function(){
    return function(input){
        return parseInt(input).toString();
    }
}) 