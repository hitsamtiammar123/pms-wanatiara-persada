app.directive('belongTo',['$parse','$filter','notifier','cP','formModal',
function($parse,$filter,notifier,cP,formModal){

    var scopeObj={
        outerIndex:'='
    }

    var dataSelected={};
    var ctrlPressed=false;

    cP.onlocationChangeStart.belongTo=function(event,next,current){
        // notifier.flushNotifier('realisasi-content');
        // notifier.flushNotifier('add-content');
    }

    function checkText(elem){
        var val=elem.text();
        if(val===''){
            elem.css('border-bottom','1px lightgrey solid');
        }
        else{
            elem.css('border-bottom','none');
        }
    }

    function addFormat($filter,value,format){
        var nvalue=$filter(format)(value);
        return nvalue?nvalue:value;

    }



    function linkFunction(scope,elem,attrs){
        var setter=$parse(attrs.belongTo);
        var validateFunc;
        var afterEdit=false;
        var getter=setter(scope);
        var autoFill;
        var highlight=attrs.highlight;
        var format;
        var list_f_af=[];
        var list_f_f=[];
        var notifyLabel;
        var sanitize;
        var editable="false";
        var onDataSelected;
        var onDataEscape;
        var onDataPaste;
        var onDataCopy;
        var context={
            elem:elem,
            scope:scope,
            attrs:attrs
        }
        var contenteditable=attrs.contenteditable;
        var oldValue='';
        var value=elem.text();
        var getter=setter(scope);
        var edited=false;
        var elemID;
        var href;
        var listData;
        var listID;
        var onListSelected;
        var context={elem:elem,scope:scope,attrs:attrs};;

        var getId=function(){
            return new Date().getTime().toString().substr(6);
        }

        var setAfterEdit=function(){
            var sp_ed=attrs.afterEdit.split('|');
            afterEdit=true;
            list_f_af=[];
            for(var i=0;i<sp_ed.length;i++){
                list_f_af.push($parse(sp_ed[i])(scope));
            }
        }

        var setFormat=function(){
            var sp_f=attrs.format.split('|');
            list_f_f=[];
            for(var i=0;i<sp_f.length;i++){
                list_f_f.push(sp_f[i]);
            }
            format=attrs.format;
        }

        var applyFormat=function(value){
            for(var i=0;i<list_f_f.length;i++){
                f=addFormat($filter,value,list_f_f[i]);
                elem.text(f);
                value=f;
            }
        }

        var onFocusOut=function(){
            var value=elem.text().trim();
            setter=$parse(attrs.belongTo);
            if(isUndf(attrs.contenteditable)||attrs.contenteditable==='false'){
                getter=setter(scope);
                elem.text(getter);
                return;
            }

            getter=setter(scope);
            if(attrs.sanitize){
                value=$filter(attrs.sanitize)(value);
            }

            setter.assign(scope,value);
            oldValue=value;

            if(afterEdit &&edited){
                for(var i=0;i<list_f_af.length;i++){
                    list_f_af[i]?list_f_af[i](elem,value,scope,attrs):'';
                }
                edited=false;
            }

            if(attrs.format){
                var f;
                value=setter(scope);
                setFormat();
                applyFormat(value);
                //addFormat($filter,elem,value,format);
            }
        }

        var dataSelect=function(){
            //debugger;
            var contenteditable=elem.attr('contenteditable');
            if(contenteditable!=='true')
                return;
            var check=Object.filter(dataSelected,function(id,curr){
                return curr===context;
            })
            var data=setter(scope);
            if(!check){
                //debugger;
                elemID=getId();
                context.scope.elemID=elemID;
                dataSelected[elemID]=context;
                angular.isFunction(onDataSelected)?onDataSelected(context,data,true):'';
            }
            else{
                angular.isFunction(onDataSelected)?onDataSelected(context,data,false):'';
                delete dataSelected[elemID];
            }
        }

        //console.log('belong-to '+getter,scope,attrs);

        if(attrs.validation){
            validateFunc=$parse(attrs.validation)(scope);
        }
        if(attrs.editable){
            editable=attrs.editable;
        }

        if(attrs.afterEdit){
            setAfterEdit();
        }

        if(attrs.autoFill){
            autoFill=attrs.autoFill;
        }

        if(attrs.format){
            setFormat();
        }

        if(!autoFill || autoFill!=='false' ){
            elem.text(getter);
        }

        if(attrs.sanitize){
            sanitize=attrs.sanitize;
        }

        if(attrs.onDataSelected){
            onDataSelected=$parse(attrs.onDataSelected)(scope);
        }

        if(attrs.onDataEscaped){
            onDataEscape=$parse(attrs.onDataEscaped)(scope);
        }

        if(attrs.onDataPaste){
            onDataPaste=$parse(attrs.onDataPaste)(scope);
        }

        if(attrs.onDataCopy){
            onDataCopy=$parse(attrs.onDataCopy)(scope);
        }

        if(attrs.notifyLabel||attrs.notifyGLabel){
            notifyLabel=attrs.notifyLabel||attrs.notifyGLabel;
            var sanitizeS;
            var nfunc;

            if(attrs.notifyLabel){
                sanitizeS=$filter('sanitizeHash')(notifyLabel);
                nfunc=$parse(sanitizeS)(scope);
                notifier.setNotifier(notifyLabel,nfunc,[context,setter],[notifyLabel]);
            }
            if(attrs.notifyGLabel){
                var notifySplit=notifyLabel.split(':');
                sanitizeS=$filter('sanitizeHash')(notifySplit[0]);
                nfunc=$parse(sanitizeS)(scope);
                if(isUndf(notifySplit[1]))
                    notifier.setNotifierGroup(notifySplit[0],nfunc,[context,setter],[notifyLabel]);
                else
                    notifier.setNotifierGroup(notifySplit[0],nfunc,[context,setter],[notifyLabel],notifySplit[1]);
            }
        }

        if(attrs.listData){
            listData=$parse(attrs.listData)(scope);
            listID=attrs.listId?attrs.listId:'list'+getId();
            onListSelected=$parse(attrs.onListSelected)(scope);
            var m=attrs.type;

            var data={
                data:{
                    type:'select',
                    message:'',
                    list:listData,
                    label:'data'
                }
            }
            formModal.init(listID,data,'Silakan pilih data');
        }



        elem.bind('keydown',function(e){
            // if(!contenteditable || contenteditable==='false')
            //     return;
            oldValue=elem.text();
            var data=setter(scope);
            if(e.key==='Escape'){
                onDataEscape&&angular.isFunction(onDataEscape)?onDataEscape(context,data):'';
                dataSelected={};
            }
            else if(e.key==='Control'){
                ctrlPressed=true;
            }

        });

        elem.bind('keyup',function(e){
            edited=true;
            if(e.key==="Control"){
                ctrlPressed=false;
                console.log('ctrlHasUp');
            }
        });


        elem.bind('focusin',function(){
            setter=$parse(attrs.belongTo);
            getter=setter(scope)
            elem.text(getter);
           // console.log(getter);
        });
        elem.bind('focusout',function(){
            onFocusOut();
        });

        elem.on('paste',function(e){
            onDataPaste&&angular.isFunction(onDataPaste)?onDataPaste(context,e):'';
            ctrlPressed=false;
        });

        elem.on('copy',function(e){
            onDataCopy&&angular.isFunction(onDataCopy)?onDataCopy(context,e):'';
            ctrlPressed=false;
        });

        elem.bind('onmousemove',function(e){
            console.log('',e);

        });

        elem.on('click',function(e){
            if(ctrlPressed){
                dataSelect();
            }
            if(listData){
                var callback_f=onListSelected&&angular.isFunction(onListSelected)?onListSelected:function(){};
                formModal(listID).then(function(data){
                    callback_f(data,context,setter);
                },function(){});
            }
        })

        if(highlight && highlight==='true'){
            elem.bind('keyup',function(e){
                checkText(elem);
            })
            checkText(elem);
        }
        if(format){
            var v=elem.text();
            var f;
            applyFormat(v);
        }



    }

    return {
        restrict:'A',
        link:linkFunction,
        replace: true,
        transclude: true,
    };
}])
