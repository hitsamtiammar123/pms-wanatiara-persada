app.filter('sanitizeNewline',function(){
    return function(input){
        return input.replace(/\n+/,'');
    }
}) 