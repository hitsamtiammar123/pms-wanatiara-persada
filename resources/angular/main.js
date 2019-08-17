(function(){
    app=angular.module('app',['ngRoute','ngMaterial', 'ngMessages']);
    E=angular.element;
    isUndf=angular.isUndefined;
    isArr=angular.isArray;

    const controllerlist={controller_key}

    const servicelist={service_key};
    const valueList={values_key};
    const directivelist={directive_key};
    const factorylist={factory_key};
    const filterlist={filter_key};
    const pfile='{pfile}';

    var script_string='<script src="{path}"></script>';

    var head=angular.element('head');
    var h=document.querySelector('head');
    var count=0;
    var total={count};
    var totalDynamic={countDynamic};
    var appElem;


    function appendScript(src,callback){
	    var s=document.createElement('script');
	    var a=document.createAttribute('src');
	    a.value=src;
        s.attributes.setNamedItem(a);
        s.type="text/javascript";
        s.onload=callback;
        h.appendChild(s);

    }

    function viewDone(){
        E('#wait').remove();
    }

    function viewSuccess(data){
        appElem.append(data);
        angular.bootstrap(document,['app']);
    }

    function viewFail(a,b,c){
        console.log(a,b,c);
        appElem.append('Terjadi kesalahan saat memuat halaman');
    }

    function hasLoad(){
        appElem=$('#app');
        E.get({frontview},viewSuccess).fail(viewFail).always(viewDone)
        appElem.append('<p id="wait">Mohon Tunggu....</p>');
    }

    function incrementC(){
        count++;
        if(count===totalDynamic){
            appendScript(pfile,function(){
                hasLoad();
            });
        }

    }

    function loadFiles(list){
        //debugger;
        for(var l in list){
            var file=list[l];
            var s=$('<script>').attr({src:file,type:'text/javascript'});
            //head.append(s);
            appendScript(file,incrementC);
        }
    }

    loadFiles(controllerlist);
    loadFiles(directivelist);
    loadFiles(factorylist);
    loadFiles(filterlist);
    loadFiles(servicelist);
    loadFiles(valueList);



    $(document).ready(function(){
        angular.element('[data-toggle="popover"]').popover();
    });

})()

angular.element(document).on('mouseover','a',function(){
    $(this).css('cursor','pointer');
})

