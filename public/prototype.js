Object.filter=function(obj,callback){
    var result={};
    var check;
    var g=false;
    for(var i in obj){
        if(typeof(callback)==='function')
            check=callback(i,obj[i]);
        else
            check=false;
        
        if(check){
            result[i]=obj[i];
            g=true;
        }
    }
    return g?result:null;
}