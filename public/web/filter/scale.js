app.filter('scale',[function(){
    return function(input){
        var i=parseInt(input);
        var r='';
        if(i<=0)
            r='Sangat Buruk';
        else if(i===1)
            r='Buruk';
        else if(i===2)
            r='Sedang';
        else if(i===3)
            r='Baik';
        else if(i>=4)
            r='Sangat Baik';

        return r;
    }
}]);
