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
    const providerlist={provider_key};
    const bootlist={boot_key};

    var script_string='<script src="{path}"></script>';

    var head=angular.element('head');
    var h=document.querySelector('head');
    var count=0;
    var total={count};
    var totalBoot={countBoot};
    var totalDynamic={countDynamic};
    var totalStatic={countStatic};
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
            loadFilesSync(providerlist);
            loadFilesSync(bootlist);
        }

    }

    function incrementD(){
        count++;
        if(count===totalDynamic+totalStatic){
            $(document).ajaxSuccess(null);
            hasLoad();
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

    function loadFilesSync(list){
        for(var l in list){
            var file=list[l];
            var s=$('<script>').attr({src:file,type:'text/javascript'});
            $.get(file,[],incrementD);

        }
    }

    function boot(){
        var configFile='config.js.php';
        var routingFile='js/web/boot/routing.js';
        var runFile='js/web/boot/run.js';
        var cpFile='js/web/boot/providerConf.js';

        var cScript=script_string;
        var rScript=script_string;
        var runScript=script_string;
        var cpScript=script_string;

        cScript=cScript.replace('{path}',configFile);
        rScript=rScript.replace('{path}',routingFile);
        runScript=runScript.replace('{path}',runFile);
        cpScript=cpScript.replace('{path}',cpFile);



        appendScript(configFile,incrementC);
        appendScript(routingFile,incrementC);
        appendScript(runFile,incrementC);
        appendScript(cpFile,incrementC);


    }

    function redirect(){
        var url=$(this).attr('data-href')
        window.location.replace(url)
    }

    $(document).ajaxSuccess(incrementD);


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

