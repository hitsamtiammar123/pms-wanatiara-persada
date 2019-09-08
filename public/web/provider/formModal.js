app.provider('formModal',[function(){
    var _except=[];
    var _list={};

    this.except=function(arr){
        _except=arr;
    }

    this.$get=['notifier','$compile','$rootScope','$q',function(notifier,$compile,$rootScope,$q){
        var template=`<div id="{id}" form-controller="{name}" class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">{{title}}</h4></div><div class="modal-body">{content}</div><div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal" ng-click="ok()">Ok</button><button type="button" class="btn btn-danger"  data-dismiss="modal" ng-click="cancel()">Cancel</button></div></div></div></div>`;

        var input_t=`<div class="form-group"><label>{m}</label><input type="text" class="form-control" ng-model="{d}" ></div>`;

        var password_t=`<div class="form-group"><label>{m}</label><input type="password" class="form-control" ng-model="{d}" ></div>`;

        var select_t=`<div class="form-group"><label>{m}</label><select class="form-control" ng-model="{selected}" ng-options="{label} for item in {list}"></select></div>`;

        var compileModal=function(input){
            var result={};
            var result_data={};
            var d_str='data.{s}';
            var result_content='';
            for(var i in input){
                var c_str=d_str.replace('{s}',i);
                var curr=input[i];
                var t=curr.type;
                var data={};

                if(t==='text' || t==='password'){
                    var tem=(t=='text')?input_t:password_t;
                    var m=c_str+'.message';
                    var d=c_str+'.text';
                    var content=tem.replace('{m}','{{'+m+'}}').replace('{d}',d);
                    data.message=curr.hasOwnProperty('message')?curr.message:'';
                    data.text='';
                    
                }
                else if(t==='select'){
                    var tem=select_t;
                    var m=c_str+'.message';
                    var selected=c_str+'.selected';
                    var label='item.'+curr.label;
                    var list=c_str+'.list';
                    var content=tem.replace('{m}','{{'+m+'}}').replace('{selected}',selected)
                    .replace('{label}',label).replace('{list}',list);
                    data.message=curr.hasOwnProperty('message')?curr.message:'';
                    data.list=curr.hasOwnProperty('list')?curr.list:[];
                    data.selected={};

                }
                data.type=t;
                result_data[i]=data;
                result_content+=content;
            }
            result.content=result_content;
            result.data=result_data;

            return result;
        }

        var modal=function(_id){
            var id='#'+_id;

            if(!_list.hasOwnProperty(_id)){
                throw 'Modal has not initialize yet';
            }


            var modalData=E(id).data('bs.modal');
            var deffer=$q.defer();
            var pName="set"+_id+"Promise";

            notifier.notify(pName,[deffer]);

            if(modalData){
                modalData.options.backdrop='static';
                E(id).modal(); 
            }
            else{
                E(id).modal({backdrop:'static'});
            }

            return deffer.promise;
        }

        modal.init=function(id,data,title){
            if(_list.hasOwnProperty(id)||_except.indexOf(id)!==-1){
                return;
            }

            var curr_content=template;
            var controllerName=id+'Controller';
            
            
            var scope=$rootScope.$new();
            var r=compileModal(data);
            curr_content=curr_content.replace('{content}',r.content)
            .replace('{id}',id).replace('{name}',controllerName);

            var c=E(curr_content);
            scope.name=controllerName;
            scope.id=id;
            scope.data=r.data;
            scope.title=title;
            $compile(c)(scope);

            E('#app').append(c);
            _list[id]={
                elem:c,
                scope:scope
            };

        }

        modal.hasInit=function(id){
            if(_list.hasOwnProperty(id)){
                return true;
            }
            return false;
        }

        return modal;
    }];
}]); 