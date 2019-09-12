app.provider('routing',['resolveProvider',function(resolveProvider){

    this.routelist=[
        {
            url:'/target-manajemen',
            config:{
                templateUrl:'web/view/t0/target-manajemen.html',
                controller:'TargetManajemenController',
                controllerAs:'targetManajemen',
                resolve:{
                   
                }
            }  
        },
        {
            url:'/realisasi/:index/:month?',
            config:{
                templateUrl:'web/view/realisasi.html',
                controller:'RealisasiController',
                resolve:{
                    
                }
            }
        },
        { 
            url:'/ikhtisar/:id?',
            config:{
                templateUrl:'web/view/ikhtisar.html',
                controller:'IkhtisarController',
                controllerAs:'ikhsCtrl',
                resolve:{
                 
                }
            }

        },
        {
            url:'/pencarian',
            config:{
                templateUrl:'web/view/pencarian.html',
                controller:'PencarianController',
                resolve:{
                  
                }
               
            } 
        },
        {
            url:'/dummy',
            config:{
                templateUrl:'web/view/dummy.html',
                controller:'DummyController',
                resolve:{
                   
                }
            }
        },
        {
            url:'/realisasi-perusahaan',
            config:{
                templateUrl:'web/view/realisasi-perusahaan.html',
                controller:'RealisasiPerusahaanController',
                resolve:{
                    
                }
            }
        },
        {
            url:'/pengesahan/notifikasi',
            config:{
                templateUrl:'web/view/pengesahan-notifikasi.html',
                controller:'NotifikasiPengesahanController',
                resolve:{

                }
            }
        },
        {
            url:'/pengesahan/baru',
            config:{
                templateUrl:'web/view/pengesahan-baru.html',
                controller:'PengesahanBaru',
                resolve:{

                }
            }
        },
        {
            url:'/pengesahan/detail/:id',
            config:{
                templateUrl:'web/view/pengesahan-detail.html',
                controller:'PengesahanDetail',
                resolve:{

                }
            }
        }
    ]
    

    this.$get=function(){
        return {}
    }
}]);