(function(){
    app=angular.module('app',['ngRoute','ngMaterial', 'ngMessages']);
    E=angular.element;
    isUndf=angular.isUndefined;
    isArr=angular.isArray;
   

    const controllerlist=["http://localhost/pms-wanatiara-persada-v1-angular/web/controller/misc/AlertModalController.js","http://localhost/pms-wanatiara-persada-v1-angular/web/controller/misc/ConfirmModalController.js","http://localhost/pms-wanatiara-persada-v1-angular/web/controller/misc/PromptModalController.js","http://localhost/pms-wanatiara-persada-v1-angular/web/controller/route/DummyController.js","http://localhost/pms-wanatiara-persada-v1-angular/web/controller/route/FrontController.js","http://localhost/pms-wanatiara-persada-v1-angular/web/controller/route/IkhtisarController.js","http://localhost/pms-wanatiara-persada-v1-angular/web/controller/route/PencarianController.js","http://localhost/pms-wanatiara-persada-v1-angular/web/controller/route/RealisasiController.js","http://localhost/pms-wanatiara-persada-v1-angular/web/controller/route/TargetManajemenController.js"]

    const servicelist=["http://localhost/pms-wanatiara-persada-v1-angular/web/service/alertModal.js","http://localhost/pms-wanatiara-persada-v1-angular/web/service/copier.js","http://localhost/pms-wanatiara-persada-v1-angular/web/service/dataService.js","http://localhost/pms-wanatiara-persada-v1-angular/web/service/loader.js","http://localhost/pms-wanatiara-persada-v1-angular/web/service/notifier.js","http://localhost/pms-wanatiara-persada-v1-angular/web/service/validator.js"];
    const valueList=["http://localhost/pms-wanatiara-persada-v1-angular/web/values/commisioner.js","http://localhost/pms-wanatiara-persada-v1-angular/web/values/digestT.js","http://localhost/pms-wanatiara-persada-v1-angular/web/values/dIndex.js","http://localhost/pms-wanatiara-persada-v1-angular/web/values/dumb.js","http://localhost/pms-wanatiara-persada-v1-angular/web/values/months.js","http://localhost/pms-wanatiara-persada-v1-angular/web/values/route.js","http://localhost/pms-wanatiara-persada-v1-angular/web/values/unitFilter.js","http://localhost/pms-wanatiara-persada-v1-angular/web/values/user.js"];
    const directivelist=["http://localhost/pms-wanatiara-persada-v1-angular/web/directive/belongTo.js","http://localhost/pms-wanatiara-persada-v1-angular/web/directive/changeFor.js","http://localhost/pms-wanatiara-persada-v1-angular/web/directive/formController.js"];
    const factorylist=["http://localhost/pms-wanatiara-persada-v1-angular/web/factory/confirmModal.js","http://localhost/pms-wanatiara-persada-v1-angular/web/factory/promptModal.js"];
    const filterlist=["http://localhost/pms-wanatiara-persada-v1-angular/web/filter/addPercent.js","http://localhost/pms-wanatiara-persada-v1-angular/web/filter/range.js","http://localhost/pms-wanatiara-persada-v1-angular/web/filter/sanitizeHash.js","http://localhost/pms-wanatiara-persada-v1-angular/web/filter/sanitizeNewline.js","http://localhost/pms-wanatiara-persada-v1-angular/web/filter/sNumber.js"];
    const providerlist=["http://localhost/pms-wanatiara-persada-v1-angular/web/provider/formModal.js"];
    const bootlist=["http://localhost/pms-wanatiara-persada-v1-angular/web/boot/resolve.js","http://localhost/pms-wanatiara-persada-v1-angular/web/boot/routing.js","http://localhost/pms-wanatiara-persada-v1-angular/web/boot/providerConf.js","http://localhost/pms-wanatiara-persada-v1-angular/web/boot/config.js","http://localhost/pms-wanatiara-persada-v1-angular/web/boot/run.js"];
    

    var script_string='<script src="{path}"></script>';

    var head=angular.element('head');
    var h=document.querySelector('head');
    var count=0;
    var total=34;
    var totalBoot=4;
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
            E.get('web/view/frontView.html',viewSuccess).fail(viewFail).always(viewDone)
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

    function init(){
        appElem.append('<p id="wait">Mohon Tunggu....</p>');
        loadFiles(controllerlist);
        loadFiles(directivelist);
        loadFiles(factorylist);
        loadFiles(filterlist);
        loadFiles(providerlist);
        loadFiles(servicelist);
        loadFiles(valueList);
        loadFiles(bootlist);
    }

    init();

    $(document).ready(function(){
        angular.element('[data-toggle="popover"]').popover();
    });

})()

angular.element(document).on('mouseover','a',function(){
    $(this).css('cursor','pointer');
})
