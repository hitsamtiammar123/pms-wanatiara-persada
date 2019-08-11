[
    {
        url:'/target-manajemen',
        config:{
            templateUrl:'web/view/target-management.html',
            controller:'TargetManajemenController',
            controllerAs:'targetManajemen',
            resolve:{

            }
        }
    },
    {
        url:'/realisasi/:index',
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
        url:'/cari-user',
        config:{
            templateUrl:'web/view/userSearch.html',
            controller:'UserSearchController',
            resolve:{

            }
        }
    },
    {
        url:'/edit-user/:id',
        config:{
            templateUrl:'web/view/userEdit.html',
            controller:'UserEditController',
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
        url:'/tambah-user',
        config:{
            templateUrl:'web/view/userTambah.html',
            controller:'UserTambahController',
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
    }
]
