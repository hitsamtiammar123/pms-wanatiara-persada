app.factory('confirmModal',['$compile','$rootScope','$q','dataService','notifier',
function($compile,$rootScope,$q,dataService,notifier){

    var content='<div id="confirmModal" ng-controller="ConfirmModalController"class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">{{title}}</h4></div><div class="modal-body"><p class="modal-message">{{message}}</p></div><div class="modal-footer"><button type="button"class="btn btn-success" data-dismiss="modal" ng-click="resolve()">{{btnMessage.yes}}</button><button type="button"class="btn btn-danger"  data-dismiss="modal" ng-click="reject()">{{btnMessage.no}}</button></div></div></div></div>'
    var id='#confirmModal';

    var hasShown=false;

    var modal=function(title,message,btnMessage){
        const FUNCTION_NAME='display-confirmModal';
        if(!hasShown){
            init(title,message,btnMessage);
            hasShown=true;
        }
        else{
            notifier.notify('setConfirmModalTitle',[title]);
            notifier.notify('setConfirmModalMessage',[message]);
            btnMessage?notifier.notify('setConfirmModalBtnMessage',[btnMessage]):'';
        }

        var deffer=$q.defer();
        var modalData=$(id).data('bs.modal');
        notifier.notify('setConfirmModalPromise',[deffer]);
        if(modalData){
            modalData.options.backdrop='static';
            E(id).modal();
        }
        else{
            E(id).modal({backdrop:'static'});
        }

        return deffer.promise;
    }

    var init=function(title,message,btnMessage){
        var c=E(content);
        var scope=$rootScope.$new();
        var _btnMessage=btnMessage?btnMessage:{
            yes:'Yes',
            no:'No'
        };
        dataService.set('confirmModalTitle',title);
        dataService.set('confirmModalMessage',message);
        dataService.set('confirmModalBtnMessage',_btnMessage);
        $compile(c)(scope);
        E('#app').append(c);
    }


    return modal;
}]);
