app.service('alertModal',['notifier','$rootScope','$compile','dataService',function (notifier,$rootScope,$compile,dataService){
    const alertmodal='#alertModal';
    var content='<div id="alertModal" ng-controller="AlertModalController"class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">{{title}}</h4></div><div class="modal-body"><p class="modal-message">{{message}}</p></div><div class="modal-footer"><button type="button"class="btn btn-danger" ng-show="showButton" data-dismiss="modal">Ok</button></div></div></div></div>';
    var hasShown=false;
    var listUpstream={};
    var self=this;
    var isShown=false;
    var hasHide=false;

    var init=function(title,message,_isShowButton){
        var c=E(content);
        var scope=$rootScope.$new();
        dataService.set('alertModalTitle',title);
        dataService.set('alertModalMessage',message);
        dataService.set('alertModalShowButton',_isShowButton);
        $compile(c)(scope);
        E('#app').append(c);
        hasShown=true;
    }

    var showTimeout=function(title,message,isShowButton,isStatic){
        self.hide();
        setTimeout(function(){
            self.display(title,message,isShowButton,isStatic);
        },500);
        hasHide=false;
    }

    this.setUpstream=function(name,obj){
        listUpstream[name]=obj;
    }

    this.upstream=function(name){
        var ups=listUpstream[name];
        self.display(ups.title,ups.message,ups.isShowButton,ups.isStatic);
    }


    this.display=function(title,message,isShowButton,isStatic){
        const FUNCTION_NAME='display-alertModal';


        var _isShowButton=!isUndf(isShowButton)?isShowButton:true;
        var _isStatic=!isUndf(isStatic)?isStatic:true;


        if(!hasShown){
            init(title,message,_isShowButton);
        }
        else{
            if(isShown || hasHide){
                showTimeout(title,message,isShowButton,isStatic);
                return;
            }

            notifier.notify('setAlertModalTitle',[title]);
            notifier.notify('setAlertModalMessage',[message]);
            notifier.notify('setAlertModalHideButton',[_isShowButton]);
        }


        var modalData=$(alertmodal).data('bs.modal');
        if(modalData){
            if(_isStatic)
                modalData.options.backdrop='static';
            else
                modalData.options.backdrop=true;

            E(alertmodal).modal();
        }
        else{
            E(alertmodal).modal({backdrop:(_isStatic?'static':true)});
        }
        isShown=true;


    }

    this.hide=function(){
        if(isShown){
            E(alertmodal).modal('hide');
            isShown=false;
            hasHide=true;
        }
    }

}]);
