app.filter('addPercent',function(){
    return function(input){
        if(input && typeof(input)==='string'){
            var p=input.search('%');
            if(p===-1)
                return input+'%';
            else
                return input;
        }
        else if(input && typeof(input)==='number')
            return input+'%';
    }
}) 