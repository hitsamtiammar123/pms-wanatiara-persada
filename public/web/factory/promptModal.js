app.factory('promptModal',['$compile','$rootScope','$q','dataService','notifier',
function($compile,$rootScope,$q,dataService,notifier){
    var content=`<div id="promptModal" ng-controller="PromptModalController" 
    class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">
    {{title}}</h4></div><div class="modal-body"><div class="form-group"><label for="msg">{{message}}</label><input type="{{type}}" class="form-control"
     ng-model="input_text" id="msg"></div></div><div class="modal-footer"><button type="button" 
    class="btn btn-success" data-dismiss="modal" ng-click="resolve()">Ok</button><button type="button" 
    class="btn btn-danger"  data-dismiss="modal" ng-click="reject()">Cancel</button></div></div></div></div>`;

    var id='#promptModal';
    var hasShown=false;

    var modal=function(title,message,type){
        const FUNCTION_NAME='display-promptModal';
        if(!hasShown){
            init(title,message,type);
            hasShown=true;
        }
        else{
            notifier.notify('setPromptModalTitle',[title]);
            notifier.notify('setPromptModalMessage',[message]);
            notifier.notify('setPromptModalText',['']);
            notifier.notify('setPromptModalType',[type?type:'text']);
        }

        var deffer=$q.defer();
        var modalData=$(id).data('bs.modal');

        notifier.notify('setPromptModalPromise',[deffer]);

        if(modalData){
            modalData.options.backdrop='static';
            E(id).modal(); 
        }
        else{
            E(id).modal({backdrop:'static'});
        }

        return deffer.promise;
    }

    var init=function(title,message,type){
        var c=E(content);
        var scope=$rootScope.$new();
        dataService.set('promptModalTitle',title);
        dataService.set('promptModalMessage',message);
        dataService.set('promptModalType',type);
        $compile(c)(scope);
        E('#app').append(c);
    }

    return modal;
}]); 