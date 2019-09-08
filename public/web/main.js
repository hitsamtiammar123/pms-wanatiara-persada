(function(){
    app=angular.module('app',['ngRoute']);
    E=angular.element;
    isUndf=angular.isUndefined;
    isArr=angular.isArray;
 
    const controllerlist={controller} 
  
    const servicelist={service};
    const valueList={value};
    const directivelist={directive};
    const factorylist={factory};
    const filterlist={filter};
    const providerlist={provider};
    const bootlist={boot};

    var script_string='<script src="{path}"></script>';
 
    var head=angular.element('head');
    var h=document.querySelector('head');
    var count=0;
    var total={count};
    var totalBoot={countBoot};
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

    function incrementC(){
        count++;
        //console.log("total Files: "+(total+totalBoot)+" total :"+total+" totalBoot :"+totalBoot+" count: "+count);
        if(count===total+totalBoot){
            //angular.bootstrap(document,['app']);
            appElem=$('#app');
            E.get('web/view/frontView.html',viewSuccess).fail(viewFail).always(viewDone)
            appElem.append('<p id="wait">Mohon Tunggu....</p>');
            //console.log('app',app);
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
    


    loadFiles(controllerlist);
    loadFiles(directivelist);
    loadFiles(factorylist);
    loadFiles(filterlist);
    loadFiles(providerlist);
    loadFiles(servicelist);
    loadFiles(valueList);
    loadFiles(bootlist);
     

    $(document).ready(function(){
        angular.element('[data-toggle="popover"]').popover();
    });
    
})()

angular.element(document).on('mouseover','a',function(){
    $(this).css('cursor','pointer');
})

