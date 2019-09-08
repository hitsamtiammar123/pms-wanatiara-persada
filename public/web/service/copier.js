app.service('copier',function($filter,validator){
    var currData;
    var copyList={};
    var listIndices={};
    var streamList={};

    var Stream=function(){
        var copyList=[];
        var listIndex=0;

        
        this.undoData=function(){
            var list=copyList;
            var _listIndex=listIndex-1;
            if(_listIndex!==0){
                _listIndex--;
                listIndex--;
                return angular.copy(list[_listIndex]);
            }
            else
                return null;
        }

        this.redoData=function(){
            var list=copyList;
            var _listIndex=listIndex-1;
            if(_listIndex!==list.length-1){
                _listIndex++;
                listIndex++;
                return angular.copy(list[_listIndex]);
            }
            else
                return null;
        }

        this.pushData=function(currdata,previousdata){
            var list=copyList;
            var currCopy=angular.copy(currdata);
            // var listIndex=listIndices[index];
            if(list.length===0){
                list.push(previousdata);
                listIndex++;
            }
            //list.insert(listIndex,currCopy);
            list.splice( listIndex, 0, currCopy );
            listIndex++;

        }

        this.flush=function(){
            copyList=[];
            listIndex=0;
        }

    }

    var sanitizeNewline=function(input){
        var m=input.match(/"(.|\n)+"/g);
        for(var i=0;m&&i<m.length;i++){
            var c=m[i];
            var rm=c.replace("\n",'');
            input=input.replace(c,rm);
        } 
        
        return input.replace(/\n/g,"\n ");
    }

    var filterPastedData=function(str){
        str=str.replace(/"+/g,'').trim();
        var tokens=str.split(/[\u0020\t]+/);
        var result=[];
        var curr='';
        var counter=0;
        var maxcounter=0;
        //var numCounter=0;
        //var charCounter=0;
        var j=0;
        var d=[];
        var flag;
        var valid=true;
        var next_token;
        var prev_token='';
        var i=0;
        var token;

        var newLineRgx=/\n+/;
        var falseCounter=false;

        do{
            token=angular.copy(tokens[i]);

            if(token===''){
                next_token=tokens[++i];
                continue;
            }
            
            if(validator.isNum(token)){
                curr=token;
                flag='num';
            }
            else if(validator.isUnitFilter(token)){
                if(validator.isUnitFilter(prev_token))
                    curr+=token+' ';
                else
                    curr=token;
                flag='unitfilter';
            }
            else if(validator.isChar(token)){
                curr+=token+' ';
                flag='char';
            }

 
            next_token=tokens[++i];
            if(!isUndf(next_token)){
                var nLatin=validator.isChar(next_token);
                var nNum=validator.isNum(next_token);
                var nUnit=validator.isUnitFilter(next_token);
                
                if(flag==='num'||(flag==='char'&&nNum)
                ||(flag==='unitfilter'&&!nUnit)||(flag==='char'&&nUnit)){
                    curr=curr.replace(newLineRgx,'');
                    d.push(curr);
                    counter++;
                    curr='';
                }

            }
            else{
                curr=curr.replace(newLineRgx,'');
                counter++;
                d.push(curr);
                curr='';
            } 

            if(newLineRgx.test(token) ||isUndf(next_token)){
                if(d.length===0){
                    counter++;
                    d.push(curr);
                    curr='';
                }

                result.push(d);
                d=[];
                if(counter>=maxcounter && counter<=12){
                    maxcounter=counter;
                    counter=0;
                }
                else{
                    falseCounter=true;
                }
            }

            if(falseCounter){
                result=null;
                break;
            }
            prev_token=token;

        }while(!isUndf(next_token))

        return result;

    }

    this.stream=function(name){
        if(!streamList.hasOwnProperty(name))
            streamList[name]=new Stream();

        return streamList[name];
    }
 
    this.setCopyData=function(index,data){
        currData=angular.copy(data);
        if(isUndf(copyList[index])){
            copyList[index]=[];
            listIndices[index]=0;
            //streamList[index]=new Stream();
        }
        
    } 

    this.getCopyData=function(i){
        return currData;
    }

    this.readFromClipboard=function(clipboard){
        var r=[];
        var data=clipboard.getData('text/plain');
        var ndata=sanitizeNewline(data);
        var cr=filterPastedData(ndata);
        return cr;
    }
}); 