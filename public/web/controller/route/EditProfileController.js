app.controller('EditProfileController',['$scope','$rootScope','user','alertModal','loader',
function($scope,$rootScope,user,alertModal,loader){
    var ep=this;

    ep.user={
        name:user.employee.name,
        gender:user.employee.gender,
        email:user.email
    };
    ep.disabledSubmit=false;
    ep.errors={};

    var validateInput=function(){
        if(ep.user.name===''){
            alertModal.display('Input Salah','Tolong masukan nama anda',true,false);
            return false;
        }
        else if(ep.user.email===''){
            alertModal.display('Input Salah','Tolong masukan email anda',true,false);
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
        console.log(err);
        if(err.status===422)
            fetchErrors(err);
    }

    ep.submit=function(){
        if(validateInput()){
            loader.updateProfile(user.employee.id,ep.user).then(onSuccessUpdate,onUpdateFail).finally(function(){
                ep.disabledSubmit=false;
                alertModal.hide();
            });
            alertModal.display('Peringatan','Mengubah profile, mohon tunggu',false,true);
            ep.disabledSubmit=true;
        }

    }
}]);
