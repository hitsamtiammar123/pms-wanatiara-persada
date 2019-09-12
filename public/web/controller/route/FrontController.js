app.controller('FrontController',function($scope,$rootScope,dataService,months,
    notifier,user,loader,notifier,pusher,alertModal,days){
    $scope.tahunkiwari=new Date().getFullYear();
    $scope.months=months;
    $scope.pmsIndex=user.employee.id;
    $rootScope.listSubJabatan=$rootScope.listSubJabatan?$rootScope.listSubJabatan:[];
    $scope.currentMonth=months[$rootScope.month];
    $scope.unreadNotification=0;
    $scope.date={};
    $scope.user_name=(user.employee.gender==='male'?'Pak ':'Bu ')+user.employee.name;

    var isDownloading=false;

    var logoutForm=E('#logout-form');

    var decrementUnreadNotification=function(currNotification){
        $scope.unreadNotification--;
        currNotification.read_at=new Date();
        loader.notificationMarkAsRead(user.employee.id,currNotification.id);
    }

    var addZero=function(num){
        if(num<10)
            return '0'+num;
        else
            return num;
    }

    var dateLoop=function(){
        var date=new Date();
        var h=date.getHours();
        
        $scope.date.day=days[date.getDay()];
        $scope.date.date=addZero(date.getDate());
        $scope.date.month=months[date.getMonth()].value;
        $scope.date.year=date.getFullYear();

        $scope.date.hour=addZero(h);
        $scope.date.minute=addZero(date.getMinutes());
        $scope.date.second=addZero(date.getSeconds());

        if(h>=4 && h<=11)
            $scope.greting_message='Selamat Pagi';
        else if(h>=12 && h<=14)
            $scope.greting_message='Selamat Siang';
        else if(h>=15 && h<=18)
            $scope.greting_message='Selamat Sore';
        else if((h>=19 && h<=23)||(h>=0 && h<=3))
            $scope.greting_message='Selamat Malam';
        
        dataService.digest($scope);

    }

    var initDate=function(){
        dateLoop();
        setInterval(dateLoop,1000);
    }

    var incrementUnreadNotification=function(result){
        if($rootScope.hasOwnProperty('notification_list')&&$rootScope.hasOwnProperty('unreadNotification')){
            $scope.unreadNotification++;
            $scope.notification_list.unshift(result.data);
            dataService.digest($scope);
            notifier.notify('notificationsHasLoad');
            console.log(result);
        }
    }

    var notificationHasFetch=function(result){
        var notifications=result.data;
        $scope.unreadNotification=$rootScope.unreadNotification=notifications.unread;
        $rootScope.notifications=notifications;
        $rootScope.notification_list=notifications.data;
        
        notifier.notify('notificationsHasLoad');
    }

    var fetchNotification=function(){
        loader.getNotifications(user.employee.id).then(notificationHasFetch);
    }
  
    $scope.changed=function(){
        //console.log($scope.currentMonth);
        notifier.notify('changeMonth',[$scope.currentMonth]);
    }

    $scope.downloadPDF=function(){
        if(isDownloading)
            return;

       loader.fetchPMSPDF(user.employee.id).then(function(result){
            var filename='PMS '+user.employee.name+' - '+$scope.currentMonth.value+' '+$scope.tahunkiwari+'.pdf';
            loader.download(result.data,filename);
       },function(){
            alertModal.display('Peringatan','Terjadi kesalahan saat mengunduh berkas');
       }).finally(function(){
            isDownloading=false;
       });
       isDownloading=true;
       alertModal.display('Berkas sedang diunduh','Mohon Tunggu');
        
    }

    $scope.toPrint=function(){
        var url=loader.route('print-pms',[user.employee.id]);
        window.open(url);
    }

    $scope.logout=function(){
        if(logoutForm.length!==0){
            pusher.dismiss();
            var f=logoutForm[0];
            f.submit();
        }
    }


    fetchNotification();
    notifier.setNotifier('decrementUnreadNotification',decrementUnreadNotification);
    pusher.on('new-notification',incrementUnreadNotification);
    initDate();


});  