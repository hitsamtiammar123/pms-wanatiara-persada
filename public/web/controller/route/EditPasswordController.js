app.controller('EditPasswordController',['$scope','alertModal','loader','user',
function($scope,alertModal,loader,user){
    var ep=this;

    ep.password={
        password:'',
        new:'',
        retype:''
    };
    ep.disabledSubmit=false;
    ep.errors={};

    var validateInput=function(){
        if(ep.password.password===''){
            alertModal.display('Input Salah','Tolong masukan kata sandi lama anda',true,false);
            return false;
        }
        else if(ep.password.new===''){
            alertModal.display('Input Salah','Tolong masukan kata sandi baru anda',true,false);
            return false;
        }
        else if(ep.password.retype===''){
            alertModal.display('Input Salah','Tolong masukan kembali kata sandi baru anda',true,false);
            return false;
        }
        return true;
    }

    var fetchErrors=function(err){
        var errors=err.data.errors;
        for(var i in errors){
            var d=errors[i];
            ep.errors[i]=d[0];
        }
    }

    var onSuccessUpdate=function(){
        setTimeout(function(){
            window.location.reload();
        },1000)
    }

    var onUpdateFail=function(err){
        //console.log(err);
        if(err.status===422)
            fetchErrors(err);
        else if(err.status===403)
        	alertModal.display('Peringatan','Anda tidak diperbolehkan mengubah data profile',false,true);
    }

    ep.submit=function(){
        if(validateInput()){
            loader.updatePassword(user.employee.id,ep.password).then(onSuccessUpdate,onUpdateFail).finally(function(){
                ep.disabledSubmit=false;
                alertModal.hide();
            });
            //alertModal.display('Peringatan','Mengubah profile, mohon tunggu',false,true);
            ep.disabledSubmit=true;
        }
    }
}]);
