app.filter('sanitizeHash',function(){
    return function(input){
        var hashRegex=/-+(\w)/g;
        if(hashRegex.test(input)){
            var hashMatches=input.match(hashRegex);

            for(var i=0;i<hashMatches.length;i++){
                var m=hashMatches[i];
                var nm=m.toUpperCase().replace('-','');
                input=input.replace(m,nm);
            }
            return input;
        }
        else
            return input;
    }
}) 