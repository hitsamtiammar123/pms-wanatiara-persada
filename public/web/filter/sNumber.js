app.filter('sNumber',function(){

    function checkRegex(input,reg){
        var r=reg.exec(input);
        var trash=r?r[0]:undefined;
        if(trash)
            input=input.replace(trash,'');
        return input;
    }

    return function(input){
        var c=input;
        if(input!==null && typeof(input)!=='string')
            input=input.toString();

        var vNumber=/^\d+(\.\d+)?$/g;
        if(!vNumber.test(input)){
            input=checkRegex(input,/(\D+)$/gm);
            input=checkRegex(input,/^(\D+)/gm);


            var secondTest=vNumber.test(input);
            if(!secondTest)
                input=input.replace(/\D+/gm,'');
            input=input.trim();
        }
        return input;
    }
})
