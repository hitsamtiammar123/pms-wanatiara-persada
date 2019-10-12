(function(){
    app=angular.module('app',['ngRoute','ngMaterial', 'ngMessages']);
    E=angular.element;
    isUndf=angular.isUndefined;
    isArr=angular.isArray;

    const controllerlist=["web/controller/misc/AlertModalController.js","web/controller/misc/ConfirmModalController.js","web/controller/misc/PromptModalController.js","web/controller/route/DummyController.js","web/controller/route/FrontController.js","web/controller/route/IkhtisarController.js","web/controller/route/NotifikasiPengesahan.js","web/controller/route/PencarianController.js","web/controller/route/PengesahanBaru.js","web/controller/route/PengesahanDetail.js","web/controller/route/RealisasiController.js","web/controller/route/RealisasiGroup.js","web/controller/route/RealisasiPerusahaanController.js","web/controller/notification/notificationMessage.js","web/controller/notification/notificationRedirect.js","web/controller/notification/NotificationRequestChange.js","web/controller/route/EditProfileController.js","web/controller/route/EditPasswordController.js","web/controller/t0/TargetManajemenController.js"]

    const servicelist=["web/service/alertModal.js","web/service/copier.js","web/service/dataService.js","web/service/kpiService.js","web/service/loader.js","web/service/notificationService.js","web/service/notifier.js","web/service/pusher.js","web/service/validator.js"];
    const valueList=["javascript/user","javascript/csrf-token","web/values/commisioner.js","web/values/days.js","web/values/digestT.js","web/values/dIndex.js","web/values/dumb.js","web/values/KPI.js","web/values/kpiKeys.js","web/values/months.js","web/values/pusher_settings.js","web/values/route.js","web/values/unitFilter.js"];
    const directivelist=["web/directive/belongTo.js","web/directive/changeFor.js","web/directive/formController.js","web/directive/ngFile.js"];
    const factorylist=["web/factory/confirmModal.js","web/factory/promptModal.js"];
    const filterlist=["web/filter/addPercent.js","web/filter/integer.js","web/filter/kwh.js","web/filter/prependTab.js","web/filter/range.js","web/filter/sanitizeHash.js","web/filter/sanitizeNewline.js","web/filter/scale.js","web/filter/sNumber.js"];
    const pfile='javascript/provider';

    var script_string='<script src="{path}"></script>';

    var head=angular.element('head');
    var h=document.querySelector('head');
    var count=0;
    var total=37;
    var totalDynamic=37;
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
       
        E.get('app/front-view',viewSuccess).fail(viewFail).always(viewDone)
       
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

    function init(){
        appElem.append('<p id="wait">Mohon Tunggu....</p>');
        loadFiles(controllerlist);
        loadFiles(directivelist);
        loadFiles(factorylist);
        loadFiles(filterlist);
        loadFiles(servicelist);
        loadFiles(valueList);
    }

    $(document).ready(function(){
        angular.element('[data-toggle="popover"]').popover();
        appElem=E('#app');
        init();
    });

})()

angular.element(document).on('mouseover','a',function(){
    $(this).css('cursor','pointer');
})
